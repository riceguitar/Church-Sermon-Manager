/**
 * Sermon edit screen: auto-fill the MP3 duration field from the audio URL.
 *
 * Two-stage: first try the browser's own media pipeline (a detached <audio>
 * element) — instant for local or small files. If the browser can't read the
 * metadata quickly (large remote VBR MP3s force it to download big spans and it
 * stalls), fall back to a server round-trip that parses only the file's header.
 * Only ever writes to the field when it is empty — it never overwrites a value
 * the user (or an import) already set.
 *
 * No dependency on jQuery/CMB2 JS - runs as soon as the DOM is ready.
 */
( function () {
	'use strict';

	/**
	 * Run `fn` once the DOM is interactive, or immediately if it already is.
	 *
	 * @param {Function} fn Callback.
	 */
	function ready( fn ) {
		if ( 'loading' === document.readyState ) {
			document.addEventListener( 'DOMContentLoaded', fn );
		} else {
			fn();
		}
	}

	/**
	 * Zero-pad a number to (at least) two digits.
	 *
	 * @param {number} value
	 * @return {string}
	 */
	function pad2( value ) {
		return ( value < 10 ? '0' : '' ) + value;
	}

	/**
	 * Format a duration in seconds as H:MM:SS, matching PHP's
	 * gmdate( 'H:i:s', ... ) convention used elsewhere in the plugin.
	 *
	 * @param {number} totalSeconds
	 * @return {string}
	 */
	function formatDuration( totalSeconds ) {
		var secondsInt = Math.max( 0, Math.round( totalSeconds ) );
		var hours      = Math.floor( secondsInt / 3600 ) % 24;
		var minutes    = Math.floor( ( secondsInt % 3600 ) / 60 );
		var seconds    = secondsInt % 60;

		return pad2( hours ) + ':' + pad2( minutes ) + ':' + pad2( seconds );
	}

	/**
	 * Build the ordered list of URLs to try loading metadata from.
	 *
	 * If we're on an https page and the audio URL is http, try the
	 * https-swapped variant first (mixed content is commonly blocked
	 * silently, so this avoids a guaranteed failure being first in line).
	 *
	 * @param {string} url
	 * @return {string[]}
	 */
	function buildCandidates( url ) {
		var candidates = [];

		if ( 'https:' === window.location.protocol && /^http:\/\//i.test( url ) ) {
			candidates.push( url.replace( /^http:\/\//i, 'https://' ) );
		}

		candidates.push( url );

		return candidates;
	}

	/**
	 * Try to resolve the duration (in seconds) of a remote audio file by
	 * letting the browser load its metadata. Resolves with `null` if no
	 * candidate URL works within the overall timeout.
	 *
	 * @param {string} url
	 * @return {Promise<number|null>}
	 */
	function computeDuration( url ) {
		return new Promise( function ( resolve ) {
			var candidates   = buildCandidates( url );
			var index        = 0;
			var settled      = false;
			var currentAudio = null;

			// Short budget: the browser reads local/small files near-instantly; if
			// it hasn't by now it's a large remote file it will stall on, so give
			// up and let the server-side fallback handle it.
			var overallTimer = setTimeout( function () {
				finish( null );
			}, 6000 );

			function cleanupAudio() {
				if ( ! currentAudio ) {
					return;
				}

				currentAudio.removeEventListener( 'loadedmetadata', onLoadedMetadata );
				currentAudio.removeEventListener( 'error', onError );
				currentAudio.removeAttribute( 'src' );
				currentAudio.src = '';

				try {
					currentAudio.load();
				} catch ( e ) {
					// Element is being discarded anyway - ignore.
				}

				currentAudio = null;
			}

			function finish( duration ) {
				if ( settled ) {
					return;
				}

				settled = true;
				clearTimeout( overallTimer );
				cleanupAudio();
				resolve( duration );
			}

			function onLoadedMetadata() {
				if ( settled || ! currentAudio ) {
					return;
				}

				var duration = currentAudio.duration;

				if ( isFinite( duration ) && duration > 0 ) {
					finish( duration );
				} else {
					tryNextCandidate();
				}
			}

			function onError() {
				if ( settled ) {
					return;
				}

				tryNextCandidate();
			}

			function tryNextCandidate() {
				cleanupAudio();

				if ( settled ) {
					return;
				}

				if ( index >= candidates.length ) {
					finish( null );
					return;
				}

				var candidate = candidates[ index++ ];
				var audio     = document.createElement( 'audio' );

				audio.preload = 'metadata';
				currentAudio  = audio;

				audio.addEventListener( 'loadedmetadata', onLoadedMetadata );
				audio.addEventListener( 'error', onError );

				audio.src = candidate;
			}

			tryNextCandidate();
		} );
	}

	/**
	 * Ask the server to read the duration by parsing only the file's header.
	 * Used when the browser can't resolve it. Resolves with seconds or null.
	 *
	 * @param {string} url
	 * @return {Promise<number|null>}
	 */
	function computeDurationServer( url ) {
		var cfg = window.smAudioDuration;

		if ( ! cfg || ! cfg.ajaxUrl || ! cfg.nonce ) {
			return Promise.resolve( null );
		}

		var body = new URLSearchParams();
		body.set( 'action', 'sm_remote_audio_duration' );
		body.set( 'nonce', cfg.nonce );
		body.set( 'url', url );

		return fetch( cfg.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString()
		} ).then( function ( response ) {
			return response.ok ? response.json() : null;
		} ).then( function ( json ) {
			if ( json && json.success && json.data && json.data.duration ) {
				return parseHms( json.data.duration );
			}
			return null;
		} ).catch( function () {
			return null;
		} );
	}

	/**
	 * Parse an "H:i:s" string back into seconds.
	 *
	 * @param {string} hms
	 * @return {number|null}
	 */
	function parseHms( hms ) {
		var parts = String( hms ).split( ':' ).map( Number );

		if ( parts.length !== 3 || parts.some( isNaN ) ) {
			return null;
		}

		return parts[ 0 ] * 3600 + parts[ 1 ] * 60 + parts[ 2 ];
	}

	ready( function () {
		var urlInput      = document.getElementById( 'sermon_audio' );
		var durationInput = document.getElementById( '_wpfc_sermon_duration' );

		if ( ! urlInput || ! durationInput ) {
			return;
		}

		var fillToken = 0;

		function isDurationEmpty() {
			return '' === durationInput.value.trim();
		}

		function attemptFill() {
			var url = urlInput.value.trim();

			if ( ! url || ! isDurationEmpty() ) {
				return;
			}

			var token = ++fillToken;

			// Browser first (instant for local/small files); server fallback for
			// large remote files the browser stalls on.
			computeDuration( url ).then( function ( duration ) {
				if ( null !== duration ) {
					return duration;
				}
				return computeDurationServer( url );
			} ).then( function ( duration ) {
				// Stale response, or nothing found - do nothing.
				if ( token !== fillToken || null === duration ) {
					return;
				}

				// The user may have typed a value while we were waiting.
				if ( ! isDurationEmpty() ) {
					return;
				}

				durationInput.value = formatDuration( duration );
				durationInput.dispatchEvent( new Event( 'input', { bubbles: true } ) );
			} );
		}

		function debounce( fn, wait ) {
			var timer = null;

			return function () {
				var context = this;
				var args    = arguments;

				clearTimeout( timer );
				timer = setTimeout( function () {
					fn.apply( context, args );
				}, wait );
			};
		}

		var debouncedAttemptFill = debounce( attemptFill, 400 );

		// (a) Fill on load if the URL is already populated but duration isn't.
		attemptFill();

		// (b) Fill when the URL field changes or loses focus (debounced).
		urlInput.addEventListener( 'change', debouncedAttemptFill );
		urlInput.addEventListener( 'blur', debouncedAttemptFill );

		// (c) Fallback poll: in case a media-modal selection doesn't fire a
		// native 'change' event, poll briefly after the upload button is
		// used so the field still gets filled without extra wiring.
		var container    = urlInput.closest( '.cmb-td' ) || urlInput.parentElement;
		var uploadButton = container ? container.querySelector( '.cmb2-upload-button' ) : null;

		if ( uploadButton ) {
			uploadButton.addEventListener( 'click', function () {
				var elapsed      = 0;
				var pollInterval = setInterval( function () {
					elapsed += 2000;

					if ( ! isDurationEmpty() || elapsed >= 30000 ) {
						clearInterval( pollInterval );
						return;
					}

					attemptFill();
				}, 2000 );
			} );
		}
	} );
} )();
