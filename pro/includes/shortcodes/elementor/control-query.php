<?php
/**
 * Query control implementation from Elementor Pro.
 *
 * @since   2.0.4
 *
 * @package SMP\Shortcodes\Elementor
 */

namespace SMP\Shortcodes\Elementor;

use Elementor\Control_Select2;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Query control init.
 *
 * @package SMP\Shortcodes\Elementor
 */
class Query extends Control_Select2 {
	/**
	 * Returns the type of control.
	 *
	 * @return string The type.
	 */
	public function get_type() {
		return 'query';
	}
}
