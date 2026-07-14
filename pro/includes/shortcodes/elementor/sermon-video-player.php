<?php
/**
 * Sermon Manager's template part - video player.
 *
 * @since   2.0.4
 * @package SMP\Shortcodes\Elementor
 */

namespace SMP\Shortcodes\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use SMP\Plugin;
use SMP\Shortcodes\Template_Tags;

defined( 'ABSPATH' ) or exit;

/**
 * Init class.
 */
class Sermon_Video_Player extends Widget_Base {
	/**
	 * Returns widget name (slug).
	 *
	 * @return string
	 */
	public function get_name() {
		return 'sermon_video_player';
	}

	/**
	 * Returns widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Video Player', 'sermon-manager' );
	}

	/**
	 * Returns widget categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'sermon-manager-pro-elements' );
	}

	/**
	 * Returns widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-play';
	}

	/**
	 * Returns required scripts.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		wp_enqueue_style( 'wpfc-sm-plyr-css' );

		return array( 'wpfc-sm-plyr' );
	}

	/**
	 * Register controls.
	 *
	 * @access protected
	 */
	public function register_controls() {
		$this->start_controls_section(
			'section_settings',
			array(
				'label' => __( 'Settings', 'sermon-manager' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'player',
			array(
				'label'   => __( 'Type', 'sermon-manager' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'plyr',
				'options' => array(
					'plyr'         => 'Plyr',
					'mediaelement' => 'MediaElement',
					'wordpress'    => 'Old WordPress player',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Renders the widget content.
	 *
	 * @access protected
	 */
	protected function render() {
		if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
			define( 'SM_ENQUEUE_SCRIPTS_STYLES', true );
		}

		$settings      = $this->get_settings_for_display();
		$template_tags = new Template_Tags();

		$template_tags->the_video_player( $settings );
	}
}

try {
	\Elementor\Plugin::instance()->widgets_manager->register( new Sermon_Video_Player() );
} catch ( \Exception $e ) {
	Plugin::instance()->notice_manager->add_error( 'elementor_element_init_error_video', $e->getMessage(), 10, 'elementor' );
}

