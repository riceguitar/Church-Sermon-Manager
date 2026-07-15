<?php
/**
 * Registers custom categories for Elementor widgets.
 *
 * @since   2.0.4
 * @package SMP\Shortcodes
 */

// Modern Elementor category registration.
add_action(
	'elementor/elements/categories_registered',
	function ( $elements_manager ) {
		$elements_manager->add_category(
			'sermon-manager-pro-elements',
			[
				'title' => __( 'Church Sermon Manager', 'sermon-manager' ),
				'icon'  => 'fa fa-plug',
			]
		);
		$elements_manager->add_category(
			'sermon-manager-pro-theme-elements',
			[
				'title' => __( 'Church Sermon Manager (Theme Builder)', 'sermon-manager' ),
				'icon'  => 'fa fa-plug',
			]
		);
	}
);
