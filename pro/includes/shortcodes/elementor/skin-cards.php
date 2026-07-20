<?php
/**
 * Registers a Cards-layout skin.
 *
 * @since   2.0.4
 * @package SMP\Shortcodes\Elementor
 */

namespace SMP\Shortcodes\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Widget_Base;
use SMP\Templating\Templating_Manager;
use SMP\Shortcodes\Template_Tags;

defined( 'ABSPATH' ) or exit;

/**
 * Class Skin_Cards, copies Elementor's Skin_Cards.
 *
 * @package SMP\Shortcodes\Elementor
 */
class Skin_Cards extends Skin_Base {
	/**
	 * Returns the skin title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Cards', 'church-sermon-manager' );
	}

	/**
	 * Start tabs controls.
	 *
	 * @param string $id   The ID.
	 * @param array  $args Args.
	 */
	public function start_controls_tab( $id, $args ) {
		$args['condition']['_skin'] = $this->get_id();
		$this->parent->start_controls_tab( $this->get_control_id( $id ), $args );
	}

	/**
	 * Returns the skin ID. (slug)
	 *
	 * @return string
	 */
	public function get_id() {
		return 'wpfc-cards';
	}

	/**
	 * End tabs controls.
	 */
	public function end_controls_tab() {
		$this->parent->end_controls_tab();
	}

	/**
	 * Start control tabs.
	 *
	 * @param string $id The ID.
	 */
	public function start_controls_tabs( $id ) {
		$args['condition']['_skin'] = $this->get_id();
		$this->parent->start_controls_tabs( $this->get_control_id( $id ) );
	}

	/**
	 * End control tabs.
	 */
	public function end_controls_tabs() {
		$this->parent->end_controls_tabs();
	}

	/**
	 * Register controls.
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
		$this->register_badge_controls();
		$this->register_avatar_controls();
	}

	/**
	 * Override the default function.
	 *
	 * - Change the label.
	 */
	protected function register_post_count_control() {
		parent::register_post_count_control();
		$this->update_control(
			'posts_per_page',
			array(
				'label' => __( 'Sermons Per Page', 'church-sermon-manager' ),
			)
		);
	}

	/**
	 * Thumbnail controls.
	 */
	protected function register_thumbnail_controls() {
		parent::register_thumbnail_controls();
		$this->remove_responsive_control( 'image_width' );
		        
        $this->add_control(
			'image_position',
			array(
				'label'       => __( 'Image Position', 'church-sermon-manager' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => array(
					'left'          => __( 'Left', 'church-sermon-manager' ),
					'right'         => __( 'Right', 'church-sermon-manager' ),
					'top'           => __( 'Top', 'church-sermon-manager' ),
				),
                'default'     => 'left',
				'condition'   => array(
					$this->get_control_id( 'columns' ) => '1',
                
				),
                'render_type'        => 'template',
				'prefix_class' => 'elementor-posts--thumbnail-',
			)
		);
	}

	/**
	 * Override the default function.
	 *
	 * - Add more meta fields.
	 */
	protected function register_meta_data_controls() {
		$this->add_control(
			'meta_data_header',
			array(
				'label'       => __( 'Meta Data - Header', 'church-sermon-manager' ),
				'label_block' => true,
				'type'        => 'choices',
				'default'     => array(),
				'multiple'    => true,
				'options'     => array(
					'date'          => __( 'Publish Date', 'church-sermon-manager' ),
					'time'          => __( 'Publish Time', 'church-sermon-manager' ),
					'comments'      => __( 'Comments', 'church-sermon-manager' ),
					'preached_date' => __( 'Preached Date', 'church-sermon-manager' ),
					'preachers'     => __( 'Preachers', 'church-sermon-manager' ),
					'passage'       => __( 'Passage', 'church-sermon-manager' ),
					'series'        => __( 'Series', 'church-sermon-manager' ),
					'service_type'  => __( 'Service Type', 'church-sermon-manager' ),
					'books'         => __( 'Bible Books', 'church-sermon-manager' ),
				),
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'meta_data_footer',
			array(
				'label'       => __( 'Meta Data - Footer', 'church-sermon-manager' ),
				'label_block' => true,
				'type'        => 'choices',
				'default'     => array( 'date', 'comments' ),
				'multiple'    => true,
				'options'     => array(
					'date'          => __( 'Publish Date', 'church-sermon-manager' ),
					'time'          => __( 'Publish Time', 'church-sermon-manager' ),
					'comments'      => __( 'Comments', 'church-sermon-manager' ),
					'preached_date' => __( 'Preached Date', 'church-sermon-manager' ),
					'preachers'     => __( 'Preachers', 'church-sermon-manager' ),
					'passage'       => __( 'Passage', 'church-sermon-manager' ),
					'series'        => __( 'Series', 'church-sermon-manager' ),
					'service_type'  => __( 'Service Type', 'church-sermon-manager' ),
					'books'         => __( 'Bible Books', 'church-sermon-manager' ),
				),
			)
		);

		parent::register_meta_data_controls();
		$this->update_control(
			'meta_separator',
			array(
				'default' => '|',
			)
		);

		$this->remove_control( 'meta_data' );
	}

	/**
	 * Badge controls
	 */
	public function register_badge_controls() {
		$this->add_control(
			'show_badge',
			array(
				'label'     => __( 'Badge', 'church-sermon-manager' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'church-sermon-manager' ),
				'label_off' => __( 'Hide', 'church-sermon-manager' ),
				'default'   => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'badge_taxonomy',
			array(
				'label'       => __( 'Badge Taxonomy', 'church-sermon-manager' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'default'     => 'wpfc_sermon_series',
				'options'     => $this->get_taxonomies(),
				'condition'   => array(
					$this->get_control_id( 'show_badge' ) => 'yes',
				),
			)
		);
	}

	/**
	 * Get the taxonomies.
	 *
	 * @return array The taxonomies.
	 */
	protected function get_taxonomies() {
		$taxonomies = array(
			'wpfc_preacher',
			'wpfc_sermon_series',
			'wpfc_sermon_topics',
			'wpfc_bible_book',
			'wpfc_service_type',
		);

		$options = array( '' => '' );

		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy                   = get_taxonomy( $taxonomy );
			$options[ $taxonomy->name ] = $taxonomy->label;
		}

		return $options;
	}

	/**
	 * Override the default function.
	 *
	 * - Change label from "Avatar" to "Preacher Avatar" (Preacher will follow the setting of Sermon Manager "Preacher
	 * Label")
	 */
	public function register_avatar_controls() {
		$this->add_control(
			'show_avatar',
			array(
				'label'        => \SermonManager::getOption( 'preacher_label', __( 'Preacher', 'church-sermon-manager' ) ) . ' ' . __( 'Avatar', 'church-sermon-manager' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'church-sermon-manager' ),
				'label_off'    => __( 'Hide', 'church-sermon-manager' ),
				'return_value' => 'show-avatar',
				'default'      => 'show-avatar',
				'separator'    => 'before',
				'prefix_class' => 'elementor-posts--',
				'render_type'  => 'template',
				'condition'    => array(
					$this->get_control_id( 'thumbnail!' ) => 'none',
				),
			)
		);
	}

	/**
	 * Design controls.
	 */
	public function register_design_controls() {
		$this->register_design_layout_controls();
		$this->register_design_card_controls();
		$this->register_design_filters_controls();
		$this->register_design_image_controls();
		$this->register_design_content_controls();
	}

	/**
	 * Card design controls.
	 */
	public function register_design_card_controls() {
		$this->start_controls_section(
			'section_design_card',
			array(
				'label' => __( 'Card', 'church-sermon-manager' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_bg_color',
			array(
				'label'     => __( 'Background Color', 'church-sermon-manager' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__card' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'card_border_color',
			array(
				'label'     => __( 'Border Color', 'church-sermon-manager' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__card' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'card_border_width',
			array(
				'label'      => __( 'Border Width', 'church-sermon-manager' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 15,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-post__card' => 'border-width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'card_border_radius',
			array(
				'label'      => __( 'Border Radius', 'church-sermon-manager' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-post__card' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'card_padding',
			array(
				'label'      => __( 'Horizontal Padding', 'church-sermon-manager' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-post__text'      => 'padding: 0 {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-post__meta-data' => 'padding: 10px {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-post__avatar'    => 'padding-right: {{SIZE}}{{UNIT}}; padding-left: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'card_vertical_padding',
			array(
				'label'      => __( 'Vertical Padding', 'church-sermon-manager' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-post__card' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'box_shadow_box_shadow_type', // The name of this control is like that, for future extensibility to group_control box shadow.
			array(
				'label'        => __( 'Box Shadow', 'church-sermon-manager' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'elementor-card-shadow-',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'hover_effect',
			array(
				'label'        => __( 'Hover Effect', 'church-sermon-manager' ),
				'type'         => Controls_Manager::SELECT,
				'label_block'  => false,
				'options'      => array(
					'none'     => __( 'None', 'church-sermon-manager' ),
					'gradient' => __( 'Gradient', 'church-sermon-manager' ),
				),
				'default'      => 'gradient',
				'separator'    => 'before',
				'prefix_class' => 'elementor-posts__hover-',
			)
		);

		$this->add_control(
			'meta_border_color',
			array(
				'label'     => __( 'Meta Border Color', 'church-sermon-manager' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__card .elementor-post__meta-data' => 'border-top-color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_id( 'meta_data!' ) => array(),
				),
			)
		);

		$this->end_controls_section();
	}
	
	/**
	 * Filters design controls.
	 */
	public function register_design_filters_controls() {
		$this->start_controls_section(
			'section_design_filters',
			array(
				'label' => __( 'Filters', 'church-sermon-manager' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'show_filter' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'filter_spacing',
			array(
				'label'     => __( 'Spacing', 'church-sermon-manager' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} #wpfc_sermon_sorting'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'default'   => array(
					'size' => 25,
				),
			)
		);
		
		$this->end_controls_section();
	}

	/**
	 * Override the default function.
	 *
	 * - Fix double spacing slider issue.
	 * - Fix meta typography condition.
	 */
	protected function register_design_content_controls() {
		$this->start_controls_section(
			'section_design_content',
			array(
				'label' => __( 'Content', 'church-sermon-manager' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_title_style',
			array(
				'label'     => __( 'Title', 'church-sermon-manager' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'church-sermon-manager' ),
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
				'label'     => __( 'Spacing', 'church-sermon-manager' ),
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
				'label'     => __( 'Meta', 'church-sermon-manager' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_control_id( 'meta_data_header!' ) => array(),
					$this->get_control_id( 'meta_data_footer!' ) => array(),
				),
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => __( 'Color', 'church-sermon-manager' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__meta-data' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'meta_data_header!' ) => array(),
					$this->get_control_id( 'meta_data_footer!' ) => array(),
				),
			)
		);

		$this->add_control(
			'meta_separator_color',
			array(
				'label'     => __( 'Separator Color', 'church-sermon-manager' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__meta-data span:before' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'meta_data_header!' ) => array(),
					$this->get_control_id( 'meta_data_footer!' ) => array(),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'meta_typography',
				'selector'  => '{{WRAPPER}} .elementor-post__meta-data',
				'condition' => array(
					$this->get_control_id( 'meta_data_header!' ) => array(),
					$this->get_control_id( 'meta_data_footer!' ) => array(),
				),
			)
		);

		$this->add_control(
			'heading_excerpt_style',
			array(
				'label'     => __( 'Excerpt', 'church-sermon-manager' ),
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
				'label'     => __( 'Color', 'church-sermon-manager' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__excerpt' => 'color: {{VALUE}};',
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
				'selector'  => '{{WRAPPER}} .elementor-post__excerpt',
				'condition' => array(
					$this->get_control_id( 'show_excerpt' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'excerpt_spacing',
			array(
				'label'     => __( 'Spacing', 'church-sermon-manager' ),
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
				'label'     => __( 'Read More', 'church-sermon-manager' ),
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
				'label'     => __( 'Color', 'church-sermon-manager' ),
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
				'label'     => __( 'Spacing', 'church-sermon-manager' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__read-more' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_read_more' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Additional design image controls.
	 */
	public function register_additional_design_image_controls() {

		$this->update_control(
			'section_design_image',
			array(
				'condition' => array(
					$this->get_control_id( 'thumbnail!' ) => 'none',
				),
			)
		);

		$this->update_control(
			'image_spacing',
			array(
				'selectors' => array(
					'{{WRAPPER}} .elementor-post__text' => 'margin-top: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					$this->get_control_id( 'thumbnail!' ) => 'none',
				),
			)
		);

		$this->remove_control( 'img_border_radius' );

	}

	/**
	 * Enqueue the scripts and styles.
	 */
	public function render() {
		if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
			define( 'SM_ENQUEUE_SCRIPTS_STYLES', true );
		}

		parent::render();
	}

	/**
	 * Renders sermons in classic layout on the frontend.
	 */
	protected function render_post() {
		if ( defined( 'SMPRO_RENDER_ERROR' ) ) {
			echo '<div class="notice notice-warning"><p>Rendering of the next item has been canceled, due to an error in the rendering of the previous item.</p></div>';

			return;
		}

		$template_tags = new Template_Tags();

		$thumbnail_key                        = $this->get_control_id( 'thumbnail_size' );
		$thumbnail_settings                   = $this->parent->get_settings_for_display();
		$thumbnail_settings[ $thumbnail_key ] = array(
			'id' => get_post_thumbnail_id(),
		);

		// Get badge term.
		$taxonomy   = $this->get_instance_value( 'badge_taxonomy' );
		$badge_term = null;
		if ( ! empty( $taxonomy ) ) {
			$terms = get_the_terms( get_the_ID(), $taxonomy );
			if ( ! empty( $terms[0] ) ) {
				$badge_term = $terms[0]->name;
			}
		}

		// Get preacher avatar.
		$preachers = wp_get_post_terms( get_the_ID(), 'wpfc_preacher' );
		$avatar    = $template_tags->get_the_avatar( array(
			'id'      => ! empty( $preachers[0] ) ? $preachers[0]->term_id : 0,
			'size'    => 128,
			'default' => '',
			'alt'     => ! empty( $preachers[0] ) ? $preachers[0]->name : '',
		) );

		$settings = array(
			// Sermon columns.
			'columns'            => $this->get_instance_value( 'columns' ),
			// Thumbnail related settings.
			'show_image_video' 	 => $this->get_instance_value( 'show_image_video' ),
			'thumbnail' 		 => $this->get_instance_value( 'thumbnail' ),
			'thumbnail_size'     => $this->get_control_id( 'thumbnail_size' ),
            'image_position'     => $this->get_instance_value( 'image_position' ),
			// Title related settings.
			'show_title'         => $this->get_instance_value( 'show_title' ),
			'title_tag'          => $this->get_instance_value( 'title_tag' ),
			// Metadata related settings.
			'meta_data_header'   => $this->get_instance_value( 'meta_data_header' ),
			'meta_data_footer'   => $this->get_instance_value( 'meta_data_footer' ),
			'meta_separator'     => $this->get_instance_value( 'meta_separator' ),
			// Excerpt related settings.
			'show_excerpt'       => $this->get_instance_value( 'show_excerpt' ),
			'excerpt_length'     => $this->get_instance_value( 'excerpt_length' ),
			// Read more.
			'show_read_more'     => $this->get_instance_value( 'show_read_more' ),
			'read_more_text'     => $this->get_instance_value( 'read_more_text' ),
			// Badge.
			'show_badge'         => $this->get_instance_value( 'show_badge' ),
			'badge_term'         => $badge_term,
			// Avatar.
			'show_avatar'        => $this->get_instance_value( 'show_avatar' ),
			'avatar'             => $avatar,
		);

		$args = array(
			'thumbnail_html' => Group_Control_Image_Size::get_attachment_image_html( $thumbnail_settings, $thumbnail_key ),
			'settings'       => $settings,
			'elementor'      => true,
		);

		/**
		 * Allows to filter Elementor settings and other arguments.
		 *
		 * @since 2.0.4
		 */
		$args = apply_filters( 'smp/shortcodes/archive/skin_cards/render_args', $args );

		try {
			echo Templating_Manager::render( 'archive-elementor', null, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered view markup from the templating pipeline.
		} catch ( \RuntimeException $e ) {
			define( 'SMPRO_RENDER_ERROR', true );
			echo '<div class="notice notice-error"><p><strong>Church Sermon Manager</strong>: Error in rendering the view, error message: "' . esc_html( $e->getMessage() ) . '"</p></div>';
		}
	}

	/**
	 * Register settings for SM's element as well.
	 *
	 * @access protected
	 */
	protected function _register_controls_actions() {
		parent::_register_controls_actions();

		add_action( 'elementor/element/sermon_archive/section_layout/before_section_end', array(
			$this,
			'register_controls',
		) );
		add_action( 'elementor/element/sermon_archive/section_query/after_section_end', array(
			$this,
			'register_style_sections',
		) );
		add_action( 'elementor/element/sermon_archive/cards_section_design_image/before_section_end', array(
			$this,
			'register_additional_design_image_controls',
		) );
	}

	/**
	 * Override the default function.
	 *
	 * - Force cards layout.
	 */
	protected function render_loop_header() {
		$this->parent->add_render_attribute( 'container', array(
			'class' => array(
				'elementor-posts-container',
				'elementor-posts',
				'elementor-grid',
				'elementor-posts--skin-cards',
				'elementor-has-item-ratio',
               	$this->get_container_class(),
			),
		) );
        
        $settings = array(
			'masonry'            => $this->get_instance_value( 'masonry' ),
        );
        if ( $settings['masonry'] == 'yes' ) {
            echo '<div ' . $this->parent->get_render_attribute_string( 'container' ) . ' data-masonry=\'{ "gutter": 0 }\' >'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute string built and escaped by Elementor.
        } else {
            echo '<div ' . $this->parent->get_render_attribute_string( 'container' ) . ' >'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute string built and escaped by Elementor.
        }
	}

	/**
	 * Render post header.
	 */
	protected function render_post_header() {
		echo '<article' . post_class( array( 'elementor-post elementor-grid-item' ) ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core post_class() output.
		echo '<div class="elementor-post__card">';

	}

	/**
	 * Render post footer.
	 */
	protected function render_post_footer() {
		echo '</div>';
		echo '</article>';
	}

	/**
	 * Render the thumbnail.
	 */
	protected function render_thumbnail() {
		if ( 'video' === $this->get_instance_value( 'thumbnail' ) ) {
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
       	<a class="elementor-post__thumbnail__link" href="<?php echo esc_url( get_permalink() ); ?>">
			<div class="elementor-post__thumbnail"><?php echo $thumbnail_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- image markup from Elementor Group_Control_Image_Size. ?></div>
		</a>
		<?php
		if ( $this->get_instance_value( 'show_badge' ) ) {
			$this->render_badge();
		}

		if ( $this->get_instance_value( 'show_avatar' ) ) {
			$this->render_avatar();
		} 
	}

	/**
	 * Render the badge.
	 */
	protected function render_badge() {
		$taxonomy = $this->get_instance_value( 'badge_taxonomy' );
		if ( empty( $taxonomy ) ) {
			return;
		}

		$terms = get_the_terms( get_the_ID(), $taxonomy );
		if ( empty( $terms[0] ) ) {
			return;
		}
		?>
		<div class="elementor-post__badge"><?php echo esc_html( $terms[0]->name ); ?></div>
		<?php
	}

	/**
	 * Render the avatar.
	 */
	protected function render_avatar() {
		?>
		<div class="elementor-post__avatar">
			<?php echo get_avatar( get_the_author_meta( 'ID' ), 128, '', get_the_author_meta( 'display_name' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core get_avatar() markup. ?>
		</div>
		<?php
	}
}
