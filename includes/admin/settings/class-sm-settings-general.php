<?php
/**
 * General settings page.
 *
 * @package SM/Core/Admin/Settings
 */

defined( 'ABSPATH' ) or die;

/**
 * Initialize settings
 */
class SM_Settings_General extends SM_Settings_Page {
	/**
	 * SM_Settings_General constructor.
	 */
	public function __construct() {
		$this->id    = 'general';
		$this->label = __( 'General', 'church-sermon-manager' );

		parent::__construct();
	}

	/**
	 * Get settings array.
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = apply_filters( 'sm_general_settings', array(

			array(
				'title' => __( 'General Settings', 'church-sermon-manager' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'general_settings',
			),
			array(
				'title'   => __( 'Audio & Video Player', 'church-sermon-manager' ),
				'type'    => 'select',
				'desc'    => __( 'Select which player to use for playing Sermons.', 'church-sermon-manager' ),
				'id'      => 'player',
				'options' => array(
					'plyr'         => 'Plyr',
					'mediaelement' => 'Mediaelement',
					'WordPress'    => 'Old WordPress player',
					'none'         => 'Browser HTML5',
				),
				'default' => 'plyr',
			),
			array(
				'title'   => __( 'Sermon Date Format', 'church-sermon-manager' ),
				'type'    => 'select',
				'desc'    => __( '(used only in admin area, when creating a new Sermon)', 'church-sermon-manager' ),
				'id'      => 'date_format',
				'options' => array(
					'0' => 'mm/dd/YY',
					'1' => 'dd/mm/YY',
					'2' => 'YY/mm/dd',
					'3' => 'YY/dd/mm',
				),
				'default' => '0',
			),
			array(
				'title'   => __( 'Sermons Per Page (default)', 'church-sermon-manager' ),
				'type'    => 'number',
				'desc'    => __( '(Affects only the default number, other settings will override it)', 'church-sermon-manager' ),
				'id'      => 'sermon_count',
				'default' => get_option( 'posts_per_page' ),
			),
			array(
				'title' => __( 'Links', 'church-sermon-manager' ),
				'type'  => 'separator_title',
			),
			array(
				'title'       => __( 'Archive Page Slug', 'church-sermon-manager' ),
				'type'        => 'text',
				'id'          => 'archive_slug',
				// translators: %s: Archive page title, default: "Sermons".
				'placeholder' => wp_sprintf( __( 'e.g. %s', 'church-sermon-manager' ), sanitize_title( __( 'Sermons', 'church-sermon-manager' ) ) ),
				// translators: %1$s Default archive path, effectively <code>/sermons</code>.
				// translators: %2$s Example single sermon path, effectively <code>/sermons/god</code>.
				'desc'        => wp_sprintf( __( 'This controls the page where sermons will be located, which includes single sermons. For example, by default, all sermons would be located under %1$s, and a single sermon with slug “god” would be under %2$s. Does not apply if "pretty permalinks" are not turned on.', 'church-sermon-manager' ), '<code>' . __( '/sermons', 'church-sermon-manager' ) . '</code>', '<code>' . __( '/sermons/god', 'church-sermon-manager' ) . '</code>' ),
				'default'     => 'sermons',
			),
			array(
				'title'    => __( 'Common Base Slug', 'church-sermon-manager' ),
				'type'     => 'checkbox',
				'desc'     => __( 'Enable a common base slug across all taxonomies.', 'church-sermon-manager' ),
				// translators: %1$s Example series path, effectively <code>/sermons/series/jesus</code>.
				// translators: %2$s Example preacher path, effectively <code>/sermons/preacher/mark</code>.
				'desc_tip' => wp_sprintf( __( 'If this option is checked, the taxonomies would also be under the slug set above, for example, by default, series named “Jesus” would be under %1$s, preacher “Mark” would be under %2$s, and so on.', 'church-sermon-manager' ), '<code>' . __( '/sermons/series/jesus', 'church-sermon-manager' ) . '</code>', '<code>' . __( '/sermons/preacher/mark', 'church-sermon-manager' ) . '</code>' ),
				'id'       => 'common_base_slug',
				'default'  => 'no',
			),
			array(
				'title'       => __( '&ldquo;Preacher&rdquo; Label', 'church-sermon-manager' ),
				'type'        => 'text',
				'placeholder' => 'Preacher', // Do not use translation here.
				// translators: %1$s Default preacher slug/path. Effectively <code>/preacher/mark</code>.
				// translators: %2$s Example changed slug/path. Effectively <code>/speaker/mark</code>.
				'desc'        => wp_sprintf( __( 'Put the label in singular form. It will change the default Preacher to anything you wish. ("Speaker", for example). Note: it will also change the slugs. For example, %1$s would become %2$s.', 'church-sermon-manager' ), '<code>' . __( '/preacher/mark', 'church-sermon-manager' ) . '</code>', '<code>' . __( '/speaker/mark', 'church-sermon-manager' ) . '</code>' ),
				'id'          => 'preacher_label',
				'default'     => '',
			),
			array(
				'title'       => __( '&ldquo;Service Type&rdquo; Label', 'church-sermon-manager' ),
				'type'        => 'text',
				'placeholder' => 'Service Type', // Do not use translation here.
				// translators: %1$s Default slug/path. Effectively <code>/service-type/mark</code>.
				// translators: %2$s Example changed slug/path. Effectively <code>/service-type/mark</code>.
				'desc'        => wp_sprintf( __( 'Put the label in singular form. It will change the default Service Type label to anything you wish. ("Congregation", for example). Note: it will also change the slugs. For example, %1$s would become %2$s.', 'church-sermon-manager' ), '<code>' . __( '/service-type/mark', 'church-sermon-manager' ) . '</code>', '<code>' . __( '/congregation/mark', 'church-sermon-manager' ) . '</code>' ),
				'id'          => 'service_type_label',
				'default'     => '',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'general_settings',
			),
		) );

		return apply_filters( 'sm_get_settings_' . $this->id, $settings );
	}
}

return new SM_Settings_General();
