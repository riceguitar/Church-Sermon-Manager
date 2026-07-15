<?php
/**
 * Helper functions for Beaver Builder page builder.
 *
 * @since   1.0.0-beta.2
 *
 * @package SMP\Shortcodes\Beaver
 */

defined( 'ABSPATH' ) or exit;

/**
 * Modifies default Beaver Builder settings.
 *
 * @param stdClass $defaults  The existing default settings.
 * @param string   $form_type The form type.
 *
 * @since 1.0.0-beta.2
 *
 * @return stdClass Modified default settings.
 */
function smp_settings_form_defaults( $defaults, $form_type ) {
	if ( 'global' == $form_type ) {
		$defaults->module_margins = '0';
	}

	return $defaults;
}

add_filter( 'fl_builder_settings_form_defaults', 'smp_settings_form_defaults', 10, 2 );

/**
 * Loads Sermon Manager Pro modules for Beaver Builder.
 *
 * @since 1.0.0-beta.2
 */
function smp_load_modules() {
	require_once __DIR__ . '/sermon-blog.php';
	require_once __DIR__ . '/sermon-taxonomy.php';
}

add_action( 'init', 'smp_load_modules' );
