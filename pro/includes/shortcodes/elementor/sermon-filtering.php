<?php
/**
 * Sermon Manager's Sermon filtering widget for Elementor.
 *
 * @since   2.0.4
 * @package SMP\Shortcodes\Elementor
 */

namespace SMP\Shortcodes\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use SMP\Plugin;

defined( 'ABSPATH' ) or exit;

/**
 * Init class.
 */
class Sermon_Filtering extends Widget_Base {
	/**
	 * Returns widget name (slug).
	 *
	 * @return string
	 */
	public function get_name() {
		return 'sermon_filtering';
	}

	/**
	 * Returns widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Sermon Filtering', 'church-sermon-manager' );
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
		return 'eicon-search';
	}

	/**
	 * Renders the element HTML.
	 */
	public function render() {
		if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
			define( 'SM_ENQUEUE_SCRIPTS_STYLES', true );
		}

		$settings = $this->get_settings_for_display();
		$content  = render_wpfc_sorting(
			array(
				'hide_topics'        => 'yes' === $settings['show_topics'] ? false : 'yes',
				'hide_series'        => 'yes' === $settings['show_series'] ? false : 'yes',
				'hide_preachers'     => 'yes' === $settings['show_preachers'] ? false : 'yes',
				'hide_books'         => 'yes' === $settings['show_book'] ? false : 'yes',
				'hide_service_types' => 'yes' === $settings['show_service_type'] ? false : 'yes',
				'classes'            => 'no-spacing',
			)
		);

		/**
		 * Allows to filter the filtering HTML.
		 *
		 * @since 2.0.4
		 *
		 * @param string $content The HTML.
		 */
		echo apply_filters( 'smp/shortcodes/elementor/sermon_filtering', $content );
	}

	/**
	 * Register controls.
	 *
	 * @access protected
	 */
	public function register_controls() {
		$this->start_controls_section(
			'section_display',
			array(
				'label'   => __( 'Display', 'church-sermon-manager' ),
				'tab'     => Controls_Manager::TAB_CONTENT,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_preachers',
			array(
				'label'   => __( 'Preachers Dropdown', 'church-sermon-manager' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_series',
			array(
				'label'   => __( 'Series Dropdown', 'church-sermon-manager' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_book',
			array(
				'label'   => __( 'Book Dropdown', 'church-sermon-manager' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_service_type',
			array(
				'label'   => __( 'Service Type Dropdown', 'church-sermon-manager' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_topics',
			array(
				'label'   => __( 'Topics Dropdown', 'church-sermon-manager' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->end_controls_section();
	}
}

try {
	\Elementor\Plugin::instance()->widgets_manager->register( new Sermon_Filtering() );
} catch ( \Exception $e ) {
	Plugin::instance()->notice_manager->add_error( 'elementor_element_init_filtering', $e->getMessage(), 10, 'elementor' );
}

