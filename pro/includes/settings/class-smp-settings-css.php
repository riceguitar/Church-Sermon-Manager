<?php
/**
 * Adds a setting page for Pro licensing.
 *
 * @since   1.0.0-beta.2
 *
 * @package SMP\Settings
 */

namespace SMP\Settings;

defined( 'ABSPATH' ) or die;

use SM_Settings_Page;

/**
 * Class SMP_Settings_css
 *
 * @package SMP\Settings
 */
class SMP_Settings_css extends SM_Settings_Page {
	/**
	 * SM_Settings_Pro constructor.
	 */
	public function __construct() {
		$this->id    = 'smprocss';
		$this->label = __( 'CSS', 'church-sermon-manager' );

		parent::__construct();
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = apply_filters( 'sm_css_settings', array(
			array(
				'title' => __( 'CSS', 'church-sermon-manager' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'smprocss_settings',
			),
			array(
				'title'       => __( 'Image Banner Background', 'church-sermon-manager' ),
				'type'        => 'smcolor',
				'desc'        => __( 'Please select Image Banner Background color.', 'church-sermon-manager' ),
				'id'          => 'smpro_banner_backgroud',
				'default'     => '',
				'placeholder' => ''
			),
			array(
				'title'       => __( 'CSS', 'church-sermon-manager' ),
				'type'        => 'textarea',
				'desc'        => __( 'Enter your css for plugin design.', 'church-sermon-manager' ),
				'id'          => 'additional_css',
				'default'     => '',
				'placeholder' => '',
				'css'         => 'width:100%;padding:5px 10px;font-family:monospace, sans-serif;min-height:300px'
			),
			array(
				'type' => 'sectionend',
				'id'   => 'smprocss_settings',
			),
		) );

		return apply_filters( 'smp_get_settings_' . $this->id, $settings );
	}
}

return new SMP_Settings_css();
