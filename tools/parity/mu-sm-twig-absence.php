<?php

defined( 'ABSPATH' ) or exit;
/**
 * Parity instrumentation: logs whether any pro/vendor (Twig) file was
 * included for each front-end request. Install by copying into
 * wp-content/mu-plugins/ on the test site; REMOVE when done.
 */
add_action( 'shutdown', function () {
	$hits = 0;
	foreach ( get_included_files() as $f ) {
		if ( false !== strpos( $f, 'pro/vendor/' ) ) {
			$hits ++;
		}
	}
	$line = sprintf( "%s\t%s\t%d\n", gmdate( 'c' ), $_SERVER['REQUEST_URI'] ?? 'cli', $hits );
	file_put_contents( WP_CONTENT_DIR . '/twig-absence.log', $line, FILE_APPEND );
} );
