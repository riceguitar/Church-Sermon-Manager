<?php
/**
 * Adds a setting page for Podcasting override.
 *
 * @since   1.0.0-beta.8
 *
 * @package SMP\Settings
 */

namespace SMP\Settings;

use SM_Settings_Page;

/**
 * Class SMP_Settings_Podcasting
 *
 * @package SMP\Settings
 */
class SMP_Settings_Podcasting extends SM_Settings_Page {
	/**
	 * SM_Settings_Pro constructor.
	 */
	public function __construct() {
		$this->id    = 'podcasting';
		$this->label = __( 'Podcast', 'sermon-manager' );

		parent::__construct();
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = apply_filters( 'sm_podcasting_settings', array(
			array(
				'title' => __( 'Podcast', 'sermon-manager' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'podcasting_settings',
			),
			array(
				'type' => 'description',
				'desc' => '<p>The podcasting settings have moved to a separate menu item, which can be found <a href="' . admin_url( 'edit.php?post_type=wpfc_sm_podcast' ) . '">here</a>.</p><style>p.submit{display:none}</style>',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'podcasting_settings',
			),
		) );

		return apply_filters( 'smp_get_settings_' . $this->id, $settings );
	}
}

return new SMP_Settings_Podcasting();
