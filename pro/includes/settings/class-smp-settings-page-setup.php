<?php
/**
 * Adds a setting page for page setup for views.
 *
 * @since   2.0.4
 *
 * @package SMP\Settings
 */

namespace SMP\Settings;

use SM_Settings_Page;

/**
 * Class SMP_Settings_Page_Setup
 *
 * @package SMP\Settings
 */
class SMP_Settings_Page_Setup extends SM_Settings_Page {
	/**
	 * SM_Settings_Page_Setup constructor.
	 */
	public function __construct() {
		$this->id    = 'page_setup';
		$this->label = __( 'Page Setup', 'sermon-manager' );

		parent::__construct();
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = apply_filters( 'sm_page_setup_settings', array(
			array(
				'title' => __( 'Page Setup', 'sermon-manager' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'page_setup_settings',
			),
			array(
				'title'   => 'Archive page',
				'type'    => 'select',
				'options' => 'smp_get_pages_array',
				'id'      => 'smp_archive_page',
				'default' => 0,
			),
			array(
				'title'   => 'Taxonomy page',
				'type'    => 'select',
				'options' => 'smp_get_pages_array',
				'id'      => 'smp_tax_page',
				'default' => 0,
			),
			array(
				'title'    => 'Force page overrides',
				'type'     => 'checkbox',
				'id'       => 'force_page_overrides',
				'default'  => 'no',
				'desc_tip' => 'When this is checked, it will use page setup even when default (Sermon Manager) template is being used.',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'page_setup_settings',
			),
		) );

		return apply_filters( 'smp_get_settings_' . $this->id, $settings );
	}
}

return new SMP_Settings_Page_Setup();
