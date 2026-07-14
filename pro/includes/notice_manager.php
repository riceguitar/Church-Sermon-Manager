<?php // phpcs:ignore

/**
 * This is the handler for notices, errors, warnings, etc...
 *
 * Notice is universal term for any type. Types are: Error, Warning, Info.
 *
 * @since   1.0.0-beta.0
 *
 * @package SMP
 */

namespace SMP;

/**
 * Class Notice_Manager
 *
 * @package SMP
 */
class Notice_Manager {

	/**
	 * Should we save after adding each error or no. Defaults to false, unless we don't have action to hook to at the
	 * end of WordPress execution.
	 *
	 * @since 1.0.0-beta.0 Temporarily true.
	 *
	 * @var bool
	 */
	protected $save_instantly = true;

	/**
	 * The notices.
	 *
	 * @var array
	 */
	protected $notices = array();

	/**
	 * Notice_Manager constructor.
	 */
	public function __construct() {
		if ( ! get_option( 'smp_notices_removed' ) ) {
			delete_option( 'smp_notices' );
			update_option( 'smp_notices_removed', true, true );
		}

		// Load notices into the manager.
		$this->_load_notices();

		// Save notices once at shutdown, instead of on every change.
		if ( ! did_action( 'shutdown' ) ) {
			add_action( 'shutdown', array( $this, '_save_notices' ) );
			$this->save_instantly = false;
		}
	}

	/**
	 * Loads the notices into class.
	 */
	protected function _load_notices() {
		$this->notices = get_option( 'smp_notices', array() );
	}

	/**
	 * Adds a notice to the stack.
	 *
	 * @param string $id               The notice ID (required).
	 * @param string $message          The notice message or function to call (required).
	 * @param int    $priority         The notice priority. Default 10.
	 * @param string $context          The notice context. Default empty string (no context).
	 * @param bool   $preserve         Should it stay after reload or not. Default false.
	 * @param bool   $hide_plugin_name Should plugin name be shown or not.
	 * @param string $type             The notice type. Default "error".
	 */
	protected function _add_notice( $id, $message, $priority = 10, $context = '', $preserve = false, $hide_plugin_name = false, $type = 'error' ) {
		// Fill out missing data/sanitize.
		$notice = $this->_fill_out_missing_data(
			array(
				'id'               => $id,
				'message'          => $message,
				'priority'         => $priority,
				'context'          => $context,
				'preserve'         => $preserve,
				'hide_plugin_name' => $hide_plugin_name,
				'type'             => $type,
			)
		);

		// Do not add the notice if data failed validation and sanitation.
		if ( false === $notice ) {
			return;
		}

		// Add the priority array if not set.
		if ( ! isset( $this->notices[ $notice['priority'] ] ) ) {
			$this->notices[ $notice['priority'] ] = array();
			ksort( $this->notices );
		}

		// Add the notice to the stack.
		$this->notices[ $notice['priority'] ][ $notice['id'] ] = $notice;

		if ( $this->save_instantly ) {
			$this->_save_notices();
		}
	}

	/**
	 * Adds missing data, and sanitizes it.
	 *
	 * @param array $notice_data The notice.
	 *
	 * @return array|false Cleaned notice or false if required data is bad.
	 *
	 * @throws \InvalidArgumentException If the notice is of invalid type (not array).
	 */
	protected function _fill_out_missing_data( $notice_data ) {
		if ( ! is_array( $notice_data ) ) {
			throw new \InvalidArgumentException( 'Notice is not array. Unacceptable.', 1 );
		}

		// Allowed notice types.
		$allowed_types = array(
			'error',
			'success',
			'info',
			'warning',
		);

		// Default notice data.
		$default_data = array(
			'priority'         => 10,
			'context'          => null,
			'preserve'         => false,
			'seen'             => false,
			'hide_plugin_name' => false,
			'type'             => 'error',
		);

		// Add the missing fields.
		$notice_data += $default_data;

		// Sanitize the array.
		foreach ( $notice_data as $item => $value ) {
			switch ( $item ) {
				case 'id':
					$notice_data[ $item ] = isset( $value ) ? sanitize_title( $value ) : null;
					break;
				case 'message':
					$notice_data[ $item ] = isset( $value ) ? wp_kses( trim( $value ), wp_kses_allowed_html() ) : null;
					break;
				case 'context':
					$notice_data[ $item ] = isset( $value ) ? sanitize_text_field( $value ) : $default_data[ $item ];
					break;
				case 'preserve':
				case 'seen':
				case 'hide_plugin_name':
					$notice_data[ $item ] = isset( $value ) ? ! ! $value : false; // Bool values.
					break;
				case 'priority':
					$notice_data[ $item ] = isset( $value ) ? intval( $value ) : $default_data[ $item ]; // Int values.
					break;
				case 'type':
					$notice_data[ $item ] = isset( $value ) ? ( in_array( $value, $allowed_types ) ? $value : $default_data[ $item ] ) : $default_data[ $item ];
					break;
			}
		}

		// Do not return the notice if there is no ID.
		if ( empty( $notice_data['id'] ) || ! $notice_data['id'] ) {
			return false;
		}

		// Use existing notice data if available.
		$existing_notice_data = $this->get_notice( $notice_data['id'] );

		if ( $existing_notice_data ) {
			$notice_data['seen'] = $existing_notice_data['seen'];
		}

		// Do not return the notice if there is no message.
		if ( empty( $notice_data['message'] ) || ! $notice_data['message'] ) {
			return false;
		}


		return $notice_data;
	}

	/**
	 * Saves notices into the database.
	 */
	public function _save_notices() {
		update_option( 'smp_notices', $this->notices );
	}

	/**
	 * Adds an error to the stack.
	 *
	 * @param string $id               The notice ID (required).
	 * @param string $message          The notice message or function to call (required).
	 * @param int    $priority         The notice priority. Default 10.
	 * @param string $context          The notice context. Default empty string (no context).
	 * @param bool   $preserve         Should it stay after reload or not. Default false.
	 * @param bool   $hide_plugin_name Should plugin name be shown or not.
	 */
	public function add_error( $id, $message, $priority = 10, $context = '', $preserve = false, $hide_plugin_name = false ) {
		$this->_add_notice( $id, $message, $priority, $context, $preserve, $hide_plugin_name, 'error' );
	}

	/**
	 * Adds an information to the stack.
	 *
	 * @param string $id               The notice ID (required).
	 * @param string $message          The notice message or function to call (required).
	 * @param int    $priority         The notice priority. Default 10.
	 * @param string $context          The notice context. Default empty string (no context).
	 * @param bool   $preserve         Should it stay after reload or not. Default false.
	 * @param bool   $hide_plugin_name Should plugin name be shown or not. Default false.
	 */
	public function add_info( $id, $message, $priority = 10, $context = '', $preserve = false, $hide_plugin_name = false ) {
		$this->_add_notice( $id, $message, $priority, $context, $preserve, $hide_plugin_name, 'info' );
	}

	/**
	 * Adds an warning to the stack.
	 *
	 * @param string $id               The notice ID (required).
	 * @param string $message          The notice message or function to call (required).
	 * @param int    $priority         The notice priority. Default 10.
	 * @param string $context          The notice context. Default empty string (no context).
	 * @param bool   $preserve         Should it stay after reload or not. Default false.
	 * @param bool   $hide_plugin_name Should plugin name be shown or not. Default false.
	 */
	public function add_warning( $id, $message, $priority = 10, $context = '', $preserve = false, $hide_plugin_name = false ) {
		$this->_add_notice( $id, $message, $priority, $context, $preserve, $hide_plugin_name, 'warning' );
	}

	/**
	 * Adds an success to the stack.
	 *
	 * @param string $id               The notice ID (required).
	 * @param string $message          The notice message or function to call (required).
	 * @param int    $priority         The notice priority. Default 10.
	 * @param string $context          The notice context. Default empty string (no context).
	 * @param bool   $preserve         Should it stay after reload or not. Default false.
	 * @param bool   $hide_plugin_name Should plugin name be shown or not. Default false.
	 */
	public function add_success( $id, $message, $priority = 10, $context = '', $preserve = false, $hide_plugin_name = false ) {
		$this->_add_notice( $id, $message, $priority, $context, $preserve, $hide_plugin_name, 'success' );
	}

	/**
	 * Sets notice as "seen".
	 *
	 * @param string $id   The ID of the notice.
	 * @param bool   $seen Set as seen or no. Default true.
	 *
	 * @return bool True if the notice was found and was set, false otherwise.
	 */
	public function set_seen( $id, $seen = true ) {
		$did_set = false;

		foreach ( $this->notices as $priority => $notice_group ) {
			foreach ( $notice_group as $notice ) {
				if ( $notice['id'] === $id ) {
					$this->notices[ $priority ][ $notice['id'] ]['seen'] = $seen;

					$did_set = true;
				}
			}
		}

		if ( $this->save_instantly ) {
			$this->_save_notices();
		}

		return $did_set;
	}

	/**
	 * Gets current notices.
	 *
	 * @return array The notices.
	 */
	public function get_notices() {
		return $this->notices;
	}

	/**
	 * Gets a single notice.
	 *
	 * @param string $id The notice ID.
	 *
	 * @return array|null The notice data or null if not found.
	 */
	public function get_notice( $id ) {
		$notices = $this->get_notices();

		foreach ( $notices as $priority => $notice_group ) {
			foreach ( $notice_group as $notice ) {
				if ( $id === $notice['id'] ) {
					return $notice;
				}
			}
		}

		return null;
	}
}
