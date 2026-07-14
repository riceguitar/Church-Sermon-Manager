<?php // phpcs:ignore

/**
 * Install manager. Handles first time install, as well as update functions.
 *
 * @since 1.0.0-beta.2
 */

namespace SMP;

defined( 'ABSPATH' ) or exit;

/**
 * Class Install_Manager.
 */
class Install_Manager {
	/**
	 * Update functions go here.
	 *
	 * Array key is the version, and array value is an array of functions.
	 *
	 * @var array
	 */
	protected $update_callbacks = array(
		'1.0.0-beta.2' => array(
			'smp_update_100beta2_move_from_wpfcm',
		),
		'1.0.0-beta.8' => array(
			'smp_update_100beta8_convert_default_podcast',
		),
	);

	/**
	 * Currently active plugin version.
	 *
	 * @var string
	 */
	protected $local_version = '';

	/**
	 * Database version.
	 *
	 * @var string
	 */
	protected $saved_version = '';

	/**
	 * The background (cron) updater.
	 *
	 * @var Background_Updater
	 */
	protected $background_updater;

	/**
	 * Install_Manager constructor.
	 */
	public function __construct() {
		// Define variables.
		$this->local_version = SMP_VERSION;
		$this->saved_version = get_option( 'smp_version' );

		// Hook into WP.
		$this->_hook();
	}

	/**
	 * Hooks into WordPress filters and actions.
	 */
	protected function _hook() {
		add_action( 'init', array( $this, 'maybe_execute' ) );
		add_action( 'init', array( $this, 'init_cron_updater' ), 3 );
	}

	/**
	 * Executes defined update functions on update.
	 *
	 * @since 1.0.0-beta.2
	 */
	public function maybe_execute() {
		if ( $this->local_version === $this->saved_version ) {
			return;
		}

		// Do the update.
		$this->_do_update();

		// Update the saved plugin version.
		$this->_update_saved_version();

		/**
		 * Executes after an update of Sermon Manager Pro.
		 *
		 * @since 1.0.0-beta.2
		 */
		do_action( 'smp/updated' );
	}

	/**
	 * Push all needed DB updates to the queue for processing.
	 */
	protected function _do_update() {
		if ( $this->background_updater->is_updating() ) {
			Plugin::instance()->notice_manager->add_info( 'updater_working', 'Updater is working in background. Some functionality might be unavailable until process is done. This message will go away on its own.', 0 );

			return;
		}

		$update_queued = false;

		foreach ( $this->_get_db_update_callbacks() as $version => $update_callbacks ) {
			foreach ( $update_callbacks as $update_callback ) {
				if ( ! get_option( 'wp_smp_updater_' . $update_callback . '_done' ) ) {
					$this->background_updater->push_to_queue( $update_callback );
					$update_queued = true;
				}
			}
		}

		if ( $update_queued ) {
			$this->background_updater->save()->dispatch();
			Plugin::instance()->notice_manager->add_info( 'updater_started', 'Updater has started working in background. Some functionality might be unavailable until process is done. This message will go away on its own.', 0 );
		}
	}

	/**
	 * Get list of DB update callbacks.
	 *
	 * @return array
	 */
	protected function _get_db_update_callbacks() {
		return $this->update_callbacks;
	}

	/**
	 * Update DB version to current.
	 */
	protected function _update_saved_version() {
		update_option( 'smp_version', $this->local_version );
	}

	/**
	 * Init background updates.
	 */
	public function init_cron_updater() {
		$this->background_updater = new Background_Updater();
	}
}
