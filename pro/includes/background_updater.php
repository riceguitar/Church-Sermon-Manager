<?php // phpcs:ignore

/**
 * Background (cron) updater.
 *
 * @since 1.0.0-beta.2
 */

namespace SMP;

defined( 'ABSPATH' ) or exit;

/**
 * Class Background_Updater.
 */
class Background_Updater extends \SM_WP_Background_Process {

	/**
	 * Action name.
	 *
	 * @var string
	 */
	protected $action = 'smp_updater';

	/**
	 * Is the updater running?
	 *
	 * @return boolean
	 */
	public function is_updating() {
		return false === $this->is_queue_empty();
	}

	/**
	 * Task.
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param string $callback Update callback function.
	 *
	 * @return mixed
	 */
	protected function task( $callback ) {
		if ( ! defined( 'SMP_UPDATING' ) ) {
			define( 'SMP_UPDATING', true );
		}

		if ( is_callable( $callback ) ) {
			call_user_func( $callback );
		}

		return false;
	}
}
