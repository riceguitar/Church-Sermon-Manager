<?php
/**
 * All the code required for creating a sermons grid block.
 *
 * @since   2.0.40
 * @package SMP\Shortcodes\Gutenberg
 */

namespace SMP\Shortcodes\Gutenberg;

defined( 'ABSPATH' ) or exit;

/**
 * Class Sermons_Grid
 */
class Sermons_Grid {
	/**
	 * The block name that is being used in Gutenberg.
	 *
	 * @var string
	 */
	protected $block_name = 'smp/sermons-grid';

	/**
	 * Sermons_Grid constructor.
	 */
	public function __construct() {
		$this->_register_scripts_styles();
		$this->_register_block();
		$this->_register_ajax_callbacks();
	}

	/**
	 * Registers required scrips and styles. Both backend and frontend.
	 */
	protected function _register_scripts_styles() {
		wp_register_script(
			'editor-' . $this->block_name,
			plugins_url( 'sermons-grid.js', __FILE__ ),
			array( 'wp-blocks', 'wp-element' ),
			SMP_VERSION
		);
	}

	/**
	 * Registers the block.
	 */
	protected function _register_block() {
		register_block_type(
			$this->block_name,
			array(
				'editor_script'   => 'editor-' . $this->block_name,
				'render_callback' => array( $this, 'render_frontend' ),
			)
		);
	}

	/**
	 * Registers Ajax callbacks.
	 */
	protected function _register_ajax_callbacks() {
		add_action( 'wp_ajax_' . $this->block_name . '_render', array( $this, 'render_editor' ) );
	}

	/**
	 * Render the block in the editor.
	 */
	public function render_editor() {
		echo 'Hello world.';

		exit;
	}

	/**
	 * Renders the block on frontend.
	 */
	public function render_frontend() {
		return 'Hello front world.';
	}
}

return new Sermons_Grid();
