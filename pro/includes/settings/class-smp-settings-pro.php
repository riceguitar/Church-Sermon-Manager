<?php
/**
 * Adds a setting page for Pro stuff.
 *
 * @since   2.0.4
 *
 * @package SMP\Settings
 */

namespace SMP\Settings;

use SM_Settings_Page;
use SMP\Plugin;

/**
 * Class SMP_Settings_Pro
 *
 * @package SMP\Settings
 */
class SMP_Settings_Pro extends SM_Settings_Page {
	/**
	 * SM_Settings_Pro constructor.
	 */
	public function __construct() {
		$this->id    = 'pro';
		$this->label = __( 'Pro', 'sermon-manager' );

		parent::__construct();

		$this->_maybe_load_intercom();
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = apply_filters( 'sm_pro_settings', array(
			array(
				'title' => __( 'Sermon Manager Pro', 'sermon-manager' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'pro_settings',
			),
			array(
				'title'    => __( 'Blubrry PowerPress', 'sermon-manager' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Use Blubrry PowerPress player, instead of Sermon Manager\'s one.', 'sermon-manager' ),
				'desc_tip' => __( 'This is like adding <code>[powerpress]</code> shortcode at the end, except that we do it for you. Default unchecked.', 'sermon-manager' ),
				'id'       => 'blubrry_powerpress_player',
				'default'  => 'no',
			),
			array(
				'title'   => __( 'Update branch', 'sermon-manager' ),
				'type'    => 'select',
				'id'      => 'update_branch',
				'options' => array(
					'release' => __( 'Release', 'sermon-manager' ),
					'nightly' => __( 'Nightly', 'sermon-manager' ),
				),
				'desc'    => 'This option allows you to select from what source you want to get your updates. Explanation of sources: <br><ul><li><strong>Release</strong> - The default update source. Plugin is updated roughly every week to the latest stable release.</li><li><strong>Nightly</strong> - Latest untested and unstable changes. Updates happen often.</li></ul>',
				'default' => 'release',
			),
			array(
				'type' => 'sectionend',
				'id'   => 'pro_settings',
			),
		) );

		return apply_filters( 'smp_get_settings_' . $this->id, $settings );
	}

	/**
	 * Load Intercom in Sermon Manager Pro settings.
	 *
	 * @todo  - move the code to the proper place.
	 *
	 * @since 2.0.42
	 */
	protected function _maybe_load_intercom() {
		if (isset($_GET['page']) && 'sm-settings' !== $_GET['page'] ) {
			return;
		}

		// Get some data so we don't have to ask for it later.
		$current_user       = wp_get_current_user();
		$user_name          = $current_user->data->user_nicename;
		$user_email         = $current_user->data->user_email;
		$first_name         = get_user_meta( $current_user->ID, 'first_name', true );
		$last_name          = get_user_meta( $current_user->ID, 'last_name', true );
		$smp_version        = SMP_VERSION;
		$sm_version         = SM_VERSION;
		$wp_version         = $GLOBALS['wp_version'];
		$smp_license        = get_option( 'sermonmanager_license_key' ) ?: 'n/a';
		$smp_templating     = smp_is_templating_being_used() ? 'yes' : 'no';
		$templating_manager = Plugin::instance()->templating_manager;
		$active_template    = $templating_manager::get_active_template();
		$smp_template       = $active_template ? ( $active_template->name . ' (' . $active_template->version . ')' ) : 'n/a';

		if ( $first_name || $last_name ) {
			$user_name = $first_name . ( $last_name ? ' ' . $last_name : '' );
		}

		echo "
            <script>
                window.intercomSettings = {
	                app_id: 'r3kwi851',
	                name: '${user_name}',
	                email: '${user_email}',
	                smp_version: '${smp_version}',
	                sm_version: '${sm_version}',
	                wp_version: '${wp_version}',
	                smp_license: '${smp_license}',
	                smp_templating: '${smp_templating}',
	                smp_template: '${smp_template}',
                };
            </script>
        ";

		echo '
            <script>(function(){var w=window;var ic=w.Intercom;if(typeof ic===\'function\'){ic(\'reattach_activator\');ic(\'update\',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement(\'script\');s.type=\'text/javascript\';s.async=true;s.src=\'https://widget.intercom.io/widget/r3kwi851\';var x=d.getElementsByTagName(\'script\')[0];x.parentNode.insertBefore(s,x);};if(w.attachEvent){w.attachEvent(\'onload\',l);}else{w.addEventListener(\'load\',l,false);}}})();</script>
        ';
	}
}

return new SMP_Settings_Pro();
