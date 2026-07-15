<?php
/**
 * Init file for all Gutenberg blocks.
 *
 * @since   2.0.40
 * @package SMP\Shortcodes
 */

namespace SMP\Shortcodes;

defined( 'ABSPATH' ) or die;

/**
 * Class Gutenberg
 */
class Gutenberg {
	/**
	 * Gutenberg constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init_blocks' ) );
	}

	/**
	 * Includes required files.
	 */
	public function init_blocks() {
		require_once SMP_PATH_SHORTCODES . 'gutenberg-v2/blocks/sermons-grid/sermons-grid.php';
	}
}

return new Gutenberg();
