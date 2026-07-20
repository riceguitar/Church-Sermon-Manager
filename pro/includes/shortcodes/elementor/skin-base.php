<?php
/**
 * Base skin for extending.
 *
 * @since   2.0.4
 *
 * @package SMP\Shortcodes\Elementor\Skins
 */

namespace SMP\Shortcodes\Elementor;

// Elementor scheme class compatibility for all versions.
if (class_exists('Elementor\\Core\\Schemes\\Color')) {
    class_alias('Elementor\\Core\\Schemes\\Color', 'SMP_Elementor_Color');
    class_alias('Elementor\\Core\\Schemes\\Typography', 'SMP_Elementor_Typography');
} elseif (class_exists('Elementor\\Scheme_Color')) {
    class_alias('Elementor\\Scheme_Color', 'SMP_Elementor_Color');
    class_alias('Elementor\\Scheme_Typography', 'SMP_Elementor_Typography');
} else {
    // Fallback: define dummy classes to avoid fatal errors if neither exists.
    if (!class_exists('SMP_Elementor_Color')) {
        class SMP_Elementor_Color {}
    }
    if (!class_exists('SMP_Elementor_Typography')) {
        class SMP_Elementor_Typography {}
    }
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
// use SMP_Elementor_Color as Color;
// use SMP_Elementor_Typography as Typography;
use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Defines a base skin for use in free Elementor.
 *
 * Class Skin_Base
 *
 * @package SMP\Modules\Posts\Skins
 */
abstract class Skin_Base extends Elementor_Skin_Base {

	/**
	 * Style settings section.
	 *
	 * @param Widget_Base $widget The class instance.
	 */
	public function register_style_sections( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->register_design_controls();
	}

	/**
	 * Design controls.
	 */
	public function register_design_controls() {
		$this->register_design_layout_controls();
		$this->register_design_image_controls();
		$this->register_design_content_controls();
	}

	/**
	 * Style Tab
	 */
	protected function register_design_layout_controls() {
		$this->start_controls_section(
			'section_design_layout',
			array(
				'label' => __( 'Layout', 'elementor-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'column_gap',
			array(
				'label'     => __( 'Columns Gap', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 30,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post'            => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .elementor-posts-container' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				),
			)
		);

		$this->add_control(
			'row_gap',
			array(
				'label'     => __( 'Rows Gap', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 35,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'alignment',
			array(
				'label'        => __( 'Alignment', 'elementor-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'options'      => array(
					'left'   => array(
						'title' => __( 'Left', 'elementor-pro' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'elementor-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'elementor-pro' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'prefix_class' => 'elementor-posts--align-',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Design image controls.
	 */
	protected function register_design_image_controls() {
		$this->start_controls_section(
			'section_design_image',
			array(
				'label'     => __( 'Image', 'elementor-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'thumbnail!' ) => 'none',
				),
			)
		);

		$this->add_control(
			'img_border_radius',
			array(
				'label'      => __( 'Border Radius', 'elementor-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-post__thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					$this->get_control_id( 'thumbnail!' ) => 'none',
				),
			)
		);

		$this->add_control(
			'image_spacing',
			array(
				'label'     => __( 'Spacing', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__thumbnail__link'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'default'   => array(
					'size' => 5,
				),
				'condition' => array(
					$this->get_control_id( 'thumbnail!' ) => 'none',
				),
			)
		);
		
		$this->add_control(
			'heading_badge_style',
			array(
				'label'     => __( 'Badge', 'elementor-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'show_badge' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'badge_position',
			array(
				'label'       => 'Badge Position',
				'label_block' => false,
				'type'        => Controls_Manager::CHOOSE,
				'options'     => array(
					'left'  => array(
						'title' => __( 'Left', 'elementor-pro' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'elementor-pro' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'     => 'right',
				'selectors'   => array(
					'{{WRAPPER}} .elementor-post__badge' => '{{VALUE}}: 0',
				),
				'condition'   => array(
					$this->get_control_id( 'show_badge' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'badge_bg_color',
			array(
				'label'     => __( 'Background Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'     => '#818a91',
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__card .elementor-post__badge' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_badge' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'badge_color',
			array(
				'label'     => __( 'Text Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__card .elementor-post__badge' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_badge' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'badge_radius',
			array(
				'label'     => __( 'Border Radius', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__card .elementor-post__badge' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_badge' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'badge_size',
			array(
				'label'     => __( 'Size', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 5,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__card .elementor-post__badge' => 'font-size: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					$this->get_control_id( 'show_badge' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'badge_margin',
			array(
				'label'     => __( 'Margin', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 50,
					),
				),
				'default'   => array(
					'size' => 20,
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__card .elementor-post__badge' => 'margin: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					$this->get_control_id( 'show_badge' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'badge_typography',
				'selector'  => '{{WRAPPER}} .elementor-post__card .elementor-post__badge',
				'exclude'   => array( 'font_size', 'line-height' ),
				'condition' => array(
					$this->get_control_id( 'show_badge' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_avatar_style',
			array(
				'label'     => __( 'Avatar', 'elementor-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'thumbnail!' )  => 'none',
					$this->get_control_id( 'show_avatar' ) => 'show-avatar',
				),
			)
		);

		$this->add_control(
			'avatar_size',
			array(
				'label'     => __( 'Size', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 20,
						'max' => 90,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__avatar'          => 'top: calc(-{{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .elementor-post__avatar img'      => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-post__thumbnail__link' => 'margin-bottom: calc({{SIZE}}{{UNIT}} / 2)',
				),
				'condition' => array(
					$this->get_control_id( 'show_avatar' ) => 'show-avatar',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Design content controls.
	 */
	protected function register_design_content_controls() {

		$this->start_controls_section(
			'section_design_content',
			array(
				'label' => __( 'Content', 'elementor-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_title_style',
			array(
				'label'     => __( 'Title', 'elementor-pro' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__title, {{WRAPPER}} .elementor-post__title a' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'title_typography',
				'selector'  => '{{WRAPPER}} .elementor-post__title, {{WRAPPER}} .elementor-post__title a',
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'title_spacing',
			array(
				'label'     => __( 'Spacing', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_meta_style',
			array(
				'label'     => __( 'Meta', 'elementor-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'meta_data!' ) => array(),
				),
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => __( 'Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__meta-data' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'meta_data!' ) => array(),
				),
			)
		);

		$this->add_control(
			'meta_separator_color',
			array(
				'label'     => __( 'Separator Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__meta-data span:before' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'meta_data!' ) => array(),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'meta_typography',
				'selector'  => '{{WRAPPER}} .elementor-post__meta-data',
				'condition' => array(
					$this->get_control_id( 'meta_data!' ) => array(),
				),
			)
		);

		$this->add_control(
			'meta_spacing',
			array(
				'label'     => __( 'Spacing', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__meta-data' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'meta_data!' ) => array(),
				),
			)
		);

		$this->add_control(
			'heading_excerpt_style',
			array(
				'label'     => __( 'Excerpt', 'elementor-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'excerpt_color',
			array(
				'label'     => __( 'Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__excerpt p' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'excerpt_typography',
				'selector'  => '{{WRAPPER}} .elementor-post__excerpt p',
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'excerpt_spacing',
			array(
				'label'     => __( 'Spacing', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'heading_readmore_style',
			array(
				'label'     => __( 'Read More', 'elementor-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'show_read_more' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'read_more_color',
			array(
				'label'     => __( 'Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__read-more' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_read_more' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'read_more_typography',
				'selector'  => '{{WRAPPER}} .elementor-post__read-more',
				'condition' => array(
					$this->get_control_id( 'show_read_more' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'read_more_spacing',
			array(
				'label'     => __( 'Spacing', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__text' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_read_more' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Main controls register.
	 *
	 * @param Widget_Base $widget The class instance.
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->register_filter_controls();
		$this->register_columns_controls();
		$this->register_post_count_control();
		$this->register_thumbnail_controls();
		$this->register_title_controls();
		$this->register_excerpt_controls();
		$this->register_meta_data_controls();
		$this->register_read_more_controls();
	}
	
	/**
	 * Filter controls.
	 */
	protected function register_filter_controls() {
		$this->add_control(
			'show_filter',
			array(
				'label'        => __( 'Filters', 'elementor-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-pro' ),
				'label_off'    => __( 'Hide', 'elementor-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);
		
		$this->add_control(
			'show_preachers',
			array(
				'label'   => __( 'Preachers Dropdown', 'church-sermon-manager' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					$this->get_control_id( 'show_filter' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'show_series',
			array(
				'label'   => __( 'Series Dropdown', 'church-sermon-manager' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					$this->get_control_id( 'show_filter' ) => 'yes',
				),
			)
		);
		
		$this->add_control(
			'show_topics',
			array(
				'label'   => __( 'Topics Dropdown', 'church-sermon-manager' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					$this->get_control_id( 'show_filter' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'show_book',
			array(
				'label'   => __( 'Book Dropdown', 'church-sermon-manager' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					$this->get_control_id( 'show_filter' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'show_service_type',
			array(
				'label'   => __( 'Service Type Dropdown', 'church-sermon-manager' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					$this->get_control_id( 'show_filter' ) => 'yes',
				),
			)
		);

	}
	
	/**
	 * Column controls.
	 */
	protected function register_columns_controls() {
		$this->add_responsive_control(
			'columns',
			array(
				'label'              => __( 'Columns', 'elementor-pro' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '3',
				'tablet_default'     => '2',
				'mobile_default'     => '1',
				'options'            => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'prefix_class'       => 'elementor-grid%s-',
				'frontend_available' => true,
                'render_type'        => 'template',
				'separator'    => 'before',
			)
		);
	}

	/**
	 * Post count controls.
	 */
	protected function register_post_count_control() {
		$this->add_control(
			'posts_per_page',
			array(
				'label'   => __( 'Posts Per Page', 'elementor-pro' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
			)
		);
		
		$this->add_control(
			'masonry',
			array(
				'label'              => __( 'Masonry', 'elementor-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_off'          => __( 'Off', 'elementor-pro' ),
				'label_on'           => __( 'On', 'elementor-pro' ),
				'condition'          => array(
					$this->get_control_id( 'columns!' )  => '1',
				),
			)
		);
	
	}

	/**
	 * Thumbnail controls.
	 */
	protected function register_thumbnail_controls() {
		$this->add_control(
			'show_image_video',
			array(
				'label'              => __( 'Show Image/Video', 'elementor-pro' ),
				'type'               => Controls_Manager::SWITCHER,
				'default' 			 => 'yes',
				'separator' 		 => 'before',
			)
		);
		
		$this->add_control(
			'thumbnail',
			array(
				'label'        => __( 'Featured Type', 'elementor-pro' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'image',
				'options'      => array(
					'image'   => __( 'Image', 'elementor-pro' ),
					'video'   => __( 'Video', 'elementor-pro' ),
				),
				'condition'      => array(
					$this->get_control_id( 'show_image_video' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'         => 'thumbnail_size',
				'default'      => 'medium',
				'exclude'      => array( 'custom' ),
				'condition'    => array(
					$this->get_control_id( 'thumbnail' ) => 'image',
					$this->get_control_id( 'show_image_video' ) => 'yes',
				),
				'prefix_class' => 'elementor-posts--thumbnail-size-',
			)
		);

		$this->add_responsive_control(
			'item_ratio',
			array(
				'label'          => __( 'Image Ratio', 'elementor-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'default'        => array(
					'size' => 0.66,
				),
				'tablet_default' => array(
					'size' => '',
				),
				'mobile_default' => array(
					'size' => 0.5,
				),
				'range'          => array(
					'px' => array(
						'min'  => 0.1,
						'max'  => 2,
						'step' => 0.01,
					),
				),
				'selectors'      => array(
					'{{WRAPPER}} .elementor-posts-container .elementor-post__thumbnail' => 'padding-bottom: calc( {{SIZE}} * 100% );',
					'{{WRAPPER}}:after'                                                 => 'content: "{{SIZE}}"; position: absolute; color: transparent;',
				),
				'condition'      => array(
					$this->get_control_id( 'thumbnail' ) => 'image',
					$this->get_control_id( 'show_image_video' ) => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label'          => __( 'Image Width', 'elementor-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => array(
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
					'px' => array(
						'min' => 10,
						'max' => 600,
					),
				),
				'default'        => array(
					'size' => 100,
					'unit' => '%',
				),
				'tablet_default' => array(
					'size' => '',
					'unit' => '%',
				),
				'mobile_default' => array(
					'size' => 100,
					'unit' => '%',
				),
				'size_units'     => array( '%', 'px' ),
				'selectors'      => array(
					'{{WRAPPER}} .elementor-post__thumbnail__link' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'      => array(
					$this->get_control_id( 'thumbnail' ) => 'image',
					$this->get_control_id( 'show_image_video' ) => 'yes',
				),
			)
		);
	}

	/**
	 * Title controls.
	 */
	protected function register_title_controls() {
		$this->add_control(
			'show_title',
			array(
				'label'        => __( 'Title', 'elementor-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-pro' ),
				'label_off'    => __( 'Hide', 'elementor-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'     => __( 'Title HTML Tag', 'elementor-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'default'   => 'h3',
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

	}

	/**
	 * Excerpt controls.
	 */
	protected function register_excerpt_controls() {
		$this->add_control(
			'show_excerpt',
			array(
				'label'        => __( 'Excerpt', 'elementor-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-pro' ),
				'label_off'    => __( 'Hide', 'elementor-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label'     => __( 'Excerpt Length', 'elementor-pro' ),
				'type'      => Controls_Manager::NUMBER,
				/** This filter is documented in wp-includes/formatting.php */
				'default'   => apply_filters( 'excerpt_length', 25 ),
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);
		
	}

	/**
	 * Meta data controls.
	 */
	protected function register_meta_data_controls() {
		$this->add_control(
			'meta_data',
			array(
				'label'       => __( 'Meta Data', 'elementor-pro' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'default'     => array( 'date', 'comments' ),
				'multiple'    => true,
				'options'     => array(
					'author'   => __( 'Author', 'elementor-pro' ),
					'date'     => __( 'Date', 'elementor-pro' ),
					'time'     => __( 'Time', 'elementor-pro' ),
					'comments' => __( 'Comments', 'elementor-pro' ),
				),
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'meta_separator',
			array(
				'label'     => __( 'Separator Between', 'elementor-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '///',
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__meta-data span + span:before' => 'content: "{{VALUE}}"',
				),
				'condition' => array(
					$this->get_control_id( 'meta_data!' ) => array(),
				),
			)
		);
	}

	/**
	 * Read more controls.
	 */
	protected function register_read_more_controls() {
		$this->add_control(
			'show_read_more',
			array(
				'label'        => __( 'Read More', 'elementor-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-pro' ),
				'label_off'    => __( 'Hide', 'elementor-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'read_more_text',
			array(
				'label'     => __( 'Read More Text', 'elementor-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Read More »', 'elementor-pro' ),
				'condition' => array(
					$this->get_control_id( 'show_read_more' ) => 'yes',
				),
			)
		);
	}

	/**
	 * Render the view.
	 */
	public function render() {
		if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
			define( 'SM_ENQUEUE_SCRIPTS_STYLES', true );
		}

		// Use get_settings_for_display for latest Elementor compatibility
		$this->parent->query_posts();
		$query = $this->parent->get_query();

		if ( ! $query->found_posts ) {
			return;
		}

		add_filter( 'excerpt_more', array( $this, 'filter_excerpt_more' ), 20 );
		add_filter( 'excerpt_length', array( $this, 'filter_excerpt_length' ), 20 );

		$this->render_filters_header();
		$this->render_loop_header();

		if ( $query->in_the_loop ) {
			$this->render_post();
		} else {
			while ( $query->have_posts() ) {
				$query->the_post();
				$this->render_post();
			}
		}

		$this->render_loop_footer();
		wp_reset_postdata();

		remove_filter( 'excerpt_length', array( $this, 'filter_excerpt_length' ), 20 );
		remove_filter( 'excerpt_more', array( $this, 'filter_excerpt_more' ), 20 );
	}

	/**
	 * Render filters header.
	 */
	protected function render_filters_header() {
		
		$settings = array(
			'show_filter'  		=> $this->get_instance_value( 'show_filter' ),
			'show_topics'  		=> $this->get_instance_value( 'show_topics' ),
			'show_series'  		=> $this->get_instance_value( 'show_series' ),
			'show_preachers'   	=> $this->get_instance_value( 'show_preachers' ),
			'show_book'   		=> $this->get_instance_value( 'show_book' ),
			'show_service_type' => $this->get_instance_value( 'show_service_type' ),
		);
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
		if ( $settings['show_filter'] == 'yes' ) {
			echo apply_filters( 'smp/shortcodes/elementor/sermon_filtering', $content );
		}
		
	}	
	
	/**
	 * Render header.
	 */
	protected function render_loop_header() {
		$this->parent->add_render_attribute( 'container', array(
			'class' => array(
				'elementor-posts-container',
				'elementor-posts',
				'elementor-grid',
				$this->get_container_class(),
			),
		) );
		echo '<div' . $this->parent->get_render_attribute_string( 'container' ) . '>';
		
	}

	/**
	 * Get container class.
	 *
	 * @return string The class.
	 */
	public function get_container_class() {
        
        $settings = array(
			'masonry'  		=> $this->get_instance_value( 'masonry' ),
            'columns'       => $this->get_instance_value( 'columns' )
		);
        
        $test='';
        
        if (( $settings['masonry'] == 'yes' ) and ($settings['columns'] != 1)) {
            $test=' elementor-posts-masonry ';
        }
		return 'elementor-posts--skin-' . $this->get_id() . $test;
	}

	/**
	 * Render the post.
	 */
	protected function render_post() {
		$this->render_post_header();
		$this->render_thumbnail();
		$this->render_text_header();
		$this->render_title();
		$this->render_meta_data();
		$this->render_excerpt();
		$this->render_read_more();
		$this->render_text_footer();
		$this->render_post_footer();
	}

	/**
	 * Render post header.
	 */
	protected function render_post_header() {
		echo '<article' . post_class( array( 'elementor-post elementor-grid-item' ) ) . '>';
	}

	/**
	 * Render thumbnail.
	 */
	protected function render_thumbnail() {
		$thumbnail = $this->get_instance_value( 'thumbnail' );

		if ( 'video' === $thumbnail ) {
			return;
		}

		$settings                 = $this->parent->get_settings();
		$setting_key              = $this->get_control_id( 'thumbnail_size' );
		$settings[ $setting_key ] = array(
			'id' => get_post_thumbnail_id(),
		);
		$thumbnail_html           = Group_Control_Image_Size::get_attachment_image_html( $settings, $setting_key );

		if ( empty( $thumbnail_html ) ) {
			return;
		}
		?>
		<a class="elementor-post__thumbnail__link" href="<?php echo get_permalink(); ?>">
			<div class="elementor-post__thumbnail"><?php echo $thumbnail_html; ?></div>
		</a>
		<?php
	}

	/**
	 * Render text header.
	 */
	protected function render_text_header() {
		?>
		<div class="elementor-post__text">
		<?php
	}

	/**
	 * Render the title.
	 */
	protected function render_title() {
		if ( ! $this->get_instance_value( 'show_title' ) ) {
			return;
		}

		$tag = $this->get_instance_value( 'title_tag' );
		?>
		<<?php echo $tag; ?> class="elementor-post__title">
		<a href="<?php echo get_permalink(); ?>">
			<?php the_title(); ?>
		</a>
		</<?php echo $tag; ?>>
		<?php
	}

	/**
	 * Render the meta data.
	 */
	protected function render_meta_data() {
		/**
		 * The settings.
		 *
		 * @var array $settings . e.g. [ 'author', 'date', ... ]
		 */
		$settings = $this->get_instance_value( 'meta_data' );
		if ( empty( $settings ) ) {
			return;
		}
		?>
		<div class="elementor-post__meta-data">
			<?php
			if ( in_array( 'author', $settings ) ) {
				$this->render_author();
			}

			if ( in_array( 'date', $settings ) ) {
				$this->render_date();
			}

			if ( in_array( 'time', $settings ) ) {
				$this->render_time();
			}

			if ( in_array( 'comments', $settings ) ) {
				$this->render_comments();
			}
			?>
		</div>
		<?php
	}

	/**
	 * Render the author.
	 */
	protected function render_author() {
		?>
		<span class="elementor-post-author">
			<?php the_author(); ?>
		</span>
		<?php
	}

	/**
	 * Render the date.
	 */
	protected function render_date() {
		?>
		<span class="elementor-post-date">
			<?php
			/** This filter is documented in wp-includes/general-template.php */
			echo apply_filters( 'the_date', get_the_date(), get_option( 'date_format' ), '', '' );
			?>
		</span>
		<?php
	}

	/**
	 * Render the time.
	 */
	protected function render_time() {
		?>
		<span class="elementor-post-time">
			<?php the_time(); ?>
		</span>
		<?php
	}

	/**
	 * Render the comments.
	 */
	protected function render_comments() {
		?>
		<span class="elementor-post-avatar">
			<?php comments_number(); ?>
		</span>
		<?php
	}

	/**
	 * Render the excerpt.
	 */
	protected function render_excerpt() {
		if ( ! $this->get_instance_value( 'show_excerpt' ) ) {
			return;
		}
		?>
		<div class="elementor-post__excerpt">
			<?php the_excerpt(); ?>
		</div>
		<?php
	}

	/**
	 * Render the read more.
	 */
	protected function render_read_more() {
		if ( ! $this->get_instance_value( 'show_read_more' ) ) {
			return;
		}
		?>
		<a class="elementor-post__read-more" href="<?php echo get_permalink(); ?>">
			<?php echo $this->get_instance_value( 'read_more_text' ); ?>
		</a>
		<?php
	}

	/**
	 * Render the text footer.
	 */
	protected function render_text_footer() {
		echo '</div>';
	}

	/**
	 * Render the post footer.
	 */
	protected function render_post_footer() {
		echo '</article>';
	}

	/**
	 * Render the post footer.
	 */
	protected function render_loop_footer() {
		echo '</div>';

		$parent_settings = $this->parent->get_settings();
		if ( '' === $parent_settings['pagination_type'] ) {
			return;
		}

		/* @noinspection PhpUndefinedMethodInspection */
		$page_limit = $this->parent->get_query()->max_num_pages;
		if ( '' !== $parent_settings['pagination_page_limit'] ) {
			$page_limit = min( $parent_settings['pagination_page_limit'], $page_limit );
		}

		if ( 2 > $page_limit ) {
			return;
		}

		$this->parent->add_render_attribute( 'pagination', 'class', 'elementor-pagination' );

		$has_numbers   = in_array( $parent_settings['pagination_type'], array( 'numbers', 'numbers_and_prev_next' ) );
		$has_prev_next = in_array( $parent_settings['pagination_type'], array( 'prev_next', 'numbers_and_prev_next' ) );

		$links = array();

		if ( $has_numbers ) {
			/* @noinspection PhpUndefinedMethodInspection */
			$links = paginate_links( array(
				'type'               => 'array',
				'current'            => $this->parent->get_current_page(),
				'total'              => $page_limit,
				'prev_next'          => false,
				'show_all'           => 'yes' !== $parent_settings['pagination_numbers_shorten'],
				'before_page_number' => '<span class="elementor-screen-only">' . __( 'Page', 'elementor-pro' ) . '</span>',
			) );
		}

		if ( $has_prev_next ) {
			/* @noinspection PhpUndefinedMethodInspection */
			$prev_next = $this->parent->get_posts_nav_link( $page_limit );
			array_unshift( $links, $prev_next['prev'] );
			$links[] = $prev_next['next'];
		}

		?>
		<nav class="elementor-pagination" role="navigation" aria-label="<?php _e( 'Pagination', 'elementor-pro' ); ?>">
			<?php echo implode( PHP_EOL, $links ); ?>
		</nav>
		<?php
	}

	/**
	 * The excerpt length.
	 *
	 * @return Widget_Base The class instance.
	 */
	public function filter_excerpt_length() {
		return $this->get_instance_value( 'excerpt_length' );
	}

	/**
	 * Excerpt more. Not done.
	 *
	 * @param string $more Argument.
	 *
	 * @return string
	 */
	public function filter_excerpt_more( $more ) {
		return '';
	}

	/**
	 * Render AMP.
	 */
	public function render_amp() {

	}

	/**
	 * Register controls and actions (modern Elementor compatibility).
	 */
	protected function _register_controls_actions() {
		add_action( 'elementor/element/posts/section_layout/before_section_end', array( $this, 'register_controls' ) );
		add_action( 'elementor/element/posts/section_query/after_section_end', array( $this, 'register_style_sections' ) );
	}

	/**
	 * Get the correct Elementor Color scheme class, or false if not found.
	 */
	protected function get_color_class() {
		if (class_exists('Elementor\\Core\\Schemes\\Color')) {
			return 'Elementor\\Core\\Schemes\\Color';
		}
		if (class_exists('Elementor\\Scheme_Color')) {
			return 'Elementor\\Scheme_Color';
		}
		return false;
	}

	/**
	 * Get the correct Elementor Typography scheme class, or false if not found.
	 */
	protected function get_typography_class() {
		if (class_exists('Elementor\\Core\\Schemes\\Typography')) {
			return 'Elementor\\Core\\Schemes\\Typography';
		}
		if (class_exists('Elementor\\Scheme_Typography')) {
			return 'Elementor\\Scheme_Typography';
		}
		return false;
	}

	/**
	 * Get settings for display (Elementor 3.5+ compatibility).
	 */
	public function get_instance_value( $key ) {
		$settings = $this->parent->get_settings_for_display();

		// Skin controls are stored under a skin-prefixed key (e.g.
		// "wpfc_cards_show_title"), matching Elementor's own Skin_Base. Resolve
		// that first; fall back to the bare key for any non-skin control.
		$control_id = $this->get_control_id( $key );
		if ( isset( $settings[ $control_id ] ) ) {
			return $settings[ $control_id ];
		}

		return isset( $settings[ $key ] ) ? $settings[ $key ] : null;
	}
}