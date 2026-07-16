<?php
/**
 * Compatibility shim for Elementor scheme classes removed in Elementor 3.0.
 *
 * The bundled sermon widgets bind some controls to Elementor's old "scheme"
 * API — Elementor\Core\Schemes\Color and Elementor\Core\Schemes\Typography.
 * Those classes were removed in Elementor 3.0, so referencing them (e.g.
 * Color::get_type()) throws a fatal error the moment Elementor builds the
 * widget controls config in the editor. That fatal 500s the editor's config
 * request, which leaves the editor stuck on its loading spinner.
 *
 * Modern Elementor ignores the deprecated 'scheme' control argument entirely,
 * so these lightweight stand-ins simply let the widget definitions load
 * without error. They are defined only when the real classes are absent, and
 * the constant values are inert (never read by current Elementor).
 *
 * @package SMP\Shortcodes\Elementor
 */

namespace Elementor\Core\Schemes;

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( __NAMESPACE__ . '\\Color' ) ) {
	class Color {
		const COLOR_1 = '1';
		const COLOR_2 = '2';
		const COLOR_3 = '3';
		const COLOR_4 = '4';

		public static function get_type() {
			return 'color';
		}
	}
}

if ( ! class_exists( __NAMESPACE__ . '\\Typography' ) ) {
	class Typography {
		const TYPOGRAPHY_1 = '1';
		const TYPOGRAPHY_2 = '2';
		const TYPOGRAPHY_3 = '3';
		const TYPOGRAPHY_4 = '4';

		public static function get_type() {
			return 'typography';
		}
	}
}
