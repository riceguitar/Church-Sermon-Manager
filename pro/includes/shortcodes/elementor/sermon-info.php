<?php
/**
 * Sermon Manager's template part - info metadata.
 *
 * @since   2.0.4
 * @package SMP\Shortcodes\Elementor
 */

namespace SMP\Shortcodes\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Widget_Base;
use SMP\Plugin;
use SMP\Shortcodes\Template_Tags;

defined( 'ABSPATH' ) or exit;

/**
 * Init class.
 */
class Sermon_Info extends Widget_Base {
	/**
	 * Returns widget name (slug).
	 *
	 * @return string
	 */
	public function get_name() {
		return 'sermon_info';
	}

	/**
	 * Returns widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Sermon Info', 'sermon-manager' );
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
		return 'eicon-post-info';
	}

	/**
	 * Returns the required scripts.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'wpfc-sm-verse-script' );
	}

	/**
	 * Register controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_meta_data',
			array(
				'label' => __( 'Meta Data', 'sermon-manager' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'type',
			array(
				'label'   => __( 'Type', 'sermon-manager' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date preached',
				'options' => array(
					'preachers'     => __( 'Preachers', 'sermon-manager' ),
					'series'        => __( 'Series', 'sermon-manager' ),
					'service type'  => __( 'Service Type', 'sermon-manager' ),
					'bible book'    => __( 'Bible Book', 'sermon-manager' ),
					'date preached' => __( 'Date Preached', 'sermon-manager' ),
					'passage'       => __( 'Passage', 'sermon-manager' ),
				),
			)
		);

		$repeater->add_control(
			'date_format',
			array(
				'label'       => __( 'Date Format', 'elementor-pro' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => false,
				'default'     => 'default',
				'options'     => array(
					'default' => 'Default',
					'0'       => _x( 'March 6, 2018 (F j, Y)', 'Date Format', 'elementor-pro' ),
					'1'       => '2018-03-06 (Y-m-d)',
					'2'       => '03/06/2018 (m/d/Y)',
					'3'       => '06/03/2018 (d/m/Y)',
					'custom'  => __( 'Custom', 'elementor-pro' ),
				),
				'condition'   => array(
					'type' => 'date preached',
				),
			)
		);

		$repeater->add_control(
			'custom_date_format',
			array(
				'label'       => __( 'Custom Date Format', 'elementor-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'F j, Y',
				'label_block' => false,
				'condition'   => array(
					'type'        => 'date preached',
					'date_format' => 'custom',
				),
				/* translators: %s: Allowed data letters (see: http://php.net/manual/en/function.date.php). */
				'description' => sprintf( __( 'Use the letters: %s', 'elementor-pro' ),
					'l D d j S F m M n Y y'
				),
			)
		);

		$repeater->add_control(
			'time_format',
			array(
				'label'       => __( 'Time Format', 'elementor-pro' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => false,
				'default'     => 'default',
				'options'     => array(
					'default' => 'Default',
					'0'       => '3:31 pm (g:i a)',
					'1'       => '3:31 PM (g:i A)',
					'2'       => '15:31 (H:i)',
					'custom'  => __( 'Custom', 'elementor-pro' ),
				),
				'condition'   => array(
					'type' => 'time preached',
				),
			)
		);
		$repeater->add_control(
			'custom_time_format',
			array(
				'label'       => __( 'Custom Time Format', 'elementor-pro' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'g:i a',
				'placeholder' => 'g:i a',
				'label_block' => false,
				'condition'   => array(
					'type'        => 'time',
					'time_format' => 'custom',
				),
				/* translators: %s: Allowed time letters (see: http://php.net/manual/en/function.time.php). */
				'description' => sprintf( __( 'Use the letters: %s', 'elementor-pro' ),
					'g G H i a A'
				),
			)
		);

		$repeater->add_control(
			'text_prefix',
			array(
				'label'       => __( 'Before', 'elementor-pro' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'        => __( 'Link', 'elementor-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_off'    => __( 'No', 'elementor-pro' ),
				'label_on'     => __( 'Yes', 'elementor-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'type!' => array( 'time preached', 'date preached' ),
				),
			)
		);

		$repeater->add_control(
			'show_icon',
			array(
				'label'   => __( 'Icon', 'elementor-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'none'    => __( 'None', 'elementor-pro' ),
					'default' => __( 'Default', 'elementor-pro' ),
					'custom'  => __( 'Custom', 'elementor-pro' ),
				),
				'default' => 'default',
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label'       => __( 'Choose Icon', 'elementor-pro' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => false,
				'condition'   => array(
					'show_icon'    => 'custom',
					'show_avatar!' => 'yes',
				),
			)
		);

		$this->add_control(
			'icon_list',
			array(
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'default'     => array(
					array(
						'type' => 'preachers',
						'icon' => 'fa fa-user-circle-o',
					),
					array(
						'type' => 'date preached',
						'icon' => 'fa fa-calendar',
					),
					array(
						'type' => 'time preached',
						'icon' => 'fa fa-clock-o',
					),
				),
				'fields'      => array_values( $repeater->get_controls() ),
				'title_field' => '<i class="{{ icon }}" aria-hidden="true"></i> <span style="text-transform: capitalize;">{{{ type }}}</span>',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_list',
			array(
				'label' => __( 'List', 'elementor-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'space_between',
			array(
				'label'     => __( 'Space Between', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-icon-list-items:not(.elementor-inline-items) .elementor-icon-list-item:not(:last-child)'  => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-icon-list-items:not(.elementor-inline-items) .elementor-icon-list-item:not(:first-child)' => 'margin-top: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-icon-list-items.elementor-inline-items .elementor-icon-list-item'                         => 'margin-right: calc({{SIZE}}{{UNIT}}/2); margin-left: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .elementor-icon-list-items.elementor-inline-items'                                                   => 'margin-right: calc(-{{SIZE}}{{UNIT}}/2); margin-left: calc(-{{SIZE}}{{UNIT}}/2)',
					'body.rtl {{WRAPPER}} .elementor-icon-list-items.elementor-inline-items .elementor-icon-list-item:after'          => 'left: calc(-{{SIZE}}{{UNIT}}/2)',
					'body:not(.rtl) {{WRAPPER}} .elementor-icon-list-items.elementor-inline-items .elementor-icon-list-item:after'    => 'right: calc(-{{SIZE}}{{UNIT}}/2)',
				),
			)
		);

		$this->add_responsive_control(
			'icon_align',
			array(
				'label'        => __( 'Alignment', 'elementor-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'left'   => array(
						'title' => __( 'Start', 'elementor-pro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'elementor-pro' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'End', 'elementor-pro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'prefix_class' => 'elementor%s-align-',
			)
		);

		$this->add_control(
			'divider',
			array(
				'label'     => __( 'Divider', 'elementor-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'elementor-pro' ),
				'label_on'  => __( 'On', 'elementor-pro' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'content: ""',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'divider_style',
			array(
				'label'     => __( 'Style', 'elementor-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'solid'  => __( 'Solid', 'elementor-pro' ),
					'double' => __( 'Double', 'elementor-pro' ),
					'dotted' => __( 'Dotted', 'elementor-pro' ),
					'dashed' => __( 'Dashed', 'elementor-pro' ),
				),
				'default'   => 'solid',
				'condition' => array(
					'divider' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'border-top-style: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'divider_weight',
			array(
				'label'     => __( 'Weight', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 1,
				),
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'condition' => array(
					'divider' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-icon-list-items:not(.elementor-inline-items) .elementor-icon-list-item:not(:last-child):after' => 'border-top-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-inline-items .elementor-icon-list-item:not(:last-child):after'                                 => 'border-left-width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'divider_height',
			array(
				'label'      => __( 'Height', 'elementor-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'default'    => array(
					'unit' => '%',
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
					'%'  => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'condition'  => array(
					'divider' => 'yes',
					'view'    => 'inline',
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'divider_color',
			array(
				'label'     => __( 'Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ddd',
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_3,
				),
				'condition' => array(
					'divider' => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-icon-list-item:not(:last-child):after' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			array(
				'label' => __( 'Icon', 'elementor-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => __( 'Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .elementor-icon-list-icon i' => 'color: {{VALUE}};',
				),
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text_style',
			array(
				'label' => __( 'Text', 'elementor-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_indent',
			array(
				'label'     => __( 'Text Indent', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 50,
					),
				),
				'selectors' => array(
					'body:not(.rtl) {{WRAPPER}} .elementor-icon-list-text' => 'padding-left: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}} .elementor-icon-list-text'       => 'padding-right: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => __( 'Text Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .elementor-icon-list-text, {{WRAPPER}} .elementor-icon-list-text a' => 'color: {{VALUE}}',
				),
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'icon_typography',
				'selector' => '{{WRAPPER}} .elementor-icon-list-item',
				'scheme'   => Typography::TYPOGRAPHY_3,
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

		$settings      = $this->get_settings();
		$template_tags = new Template_Tags();

		if ( ! empty( $settings['icon_list'] ) ) {
			foreach ( $settings['icon_list'] as $field ) {
				switch ( $field['type'] ) {
					case 'date preached':
						$type = 'preached_date';

						$custom_date_format = empty( $field['custom_date_format'] ) ? 'F j, Y' : $field['custom_date_format'];
						$format_options     = array(
							'default' => 'F j, Y',
							'0'       => 'F j, Y',
							'1'       => 'Y-m-d',
							'2'       => 'm/d/Y',
							'3'       => 'd/m/Y',
							'custom'  => $custom_date_format,
						);
						$date_format        = $format_options[ $field['date_format'] ];
						break;
					case 'service type':
						$type = 'service_type';
						break;
					case 'bible book':
						$type = 'books';
						break;
					default:
						$type = $field['type'];
				}

				$template_tags->the_metadata(
					array(
						'meta_data'   => array( $type ),
						'inline'      => true,
						'before'      => $field['text_prefix'],
						'link'        => 'yes' === $field['link'],
						'date_format' => ! empty( $date_format ) ? $date_format : '',
						'verse_init'  => 'passage' === $field['type'] && $field['link'],
					)
				);
			}
		}
	}
}

try {
	\Elementor\Plugin::instance()->widgets_manager->register( new Sermon_Info() );
} catch ( \Exception $e ) {
	Plugin::instance()->notice_manager->add_error( 'elementor_element_init_error_info', $e->getMessage(), 10, 'elementor' );
}
