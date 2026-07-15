<?php
/**
 * Sermon Manager's Sermon Archive widget for Elementor.
 *
 * @since   2.0.4
 * @package SMP\Shortcodes\Elementor
 */

namespace SMP\Shortcodes\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Widget_Base;
use SMP\Plugin;

defined( 'ABSPATH' ) or exit;

/**
 * Init class.
 */
class Sermon_Archive extends Widget_Base {
	/**
	 * The query.
	 *
	 * @var \WP_Query
	 */
	protected $query = null;

	/**
	 * Disables "Default" skin.
	 *
	 * @var bool
	 */
	protected $_has_template_content = false;

	/**
	 * Returns widget icon.
	 *
	 * @return string Icon slug.
	 */
	public function get_icon() {
		return 'eicon-post-list';
	}

	/**
	 * Returns the scripts the widget depends on.
	 *
	 * @return array The list of scripts.
	 */
	public function get_script_depends() {
		return array( 'imagesloaded' );
	}

	/**
	 * Gets the query.
	 *
	 * @return \WP_Query
	 */
	public function get_query() {
		return $this->query;
	}

	/**
	 * Returns widget name (slug).
	 *
	 * @return string
	 */
	public function get_name() {
		return 'sermon_archive';
	}

	/**
	 * Returns widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Sermons', 'sermon-manager' );
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
	 * Create the query.
	 */
	public function query_posts() {
		$query_args = Module::get_query_args( 'posts', $this->get_settings_for_display() );

		$query_args['posts_per_page'] = $this->get_current_skin()->get_instance_value( 'posts_per_page' );
		$query_args['paged']          = $this->get_current_page();
		$query_args['post_type']      = 'wpfc_sermon';

		$query_args = smp_add_taxonomy_to_query( $query_args );

		$this->query = new \WP_Query( apply_filters( 'smp/shortcodes/elementor/sermon_query', $query_args ) );
	}

	/**
	 * Gets the current page.
	 *
	 * @return int The page number.
	 */
	public function get_current_page() {
		if ( '' === $this->get_settings_for_display( 'pagination_type' ) ) {
			return 1;
		}

		return max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
	}

	/**
	 * Returns the posts navigation (pagination) links.
	 *
	 * @param int $page_limit The limit on how many pages to display.
	 *
	 * @return array The links.
	 */
	public function get_posts_nav_link( $page_limit = null ) {
		if ( ! $page_limit ) {
			$page_limit = $this->query->max_num_pages;
		}

		$return = array();

		$paged = $this->get_current_page();

		/* @noinspection HtmlUnknownTarget */
		$link_template     = '<a class="page-numbers %s" href="%s">%s</a>';
		$disabled_template = '<span class="page-numbers %s">%s</span>';

		if ( $paged > 1 ) {
			$next_page = intval( $paged ) - 1;
			if ( $next_page < 1 ) {
				$next_page = 1;
			}

			$return['prev'] = sprintf( $link_template, 'prev', get_pagenum_link( $next_page ), $this->get_settings_for_display( 'pagination_prev_label' ) );
		} else {
			$return['prev'] = sprintf( $disabled_template, 'prev', $this->get_settings_for_display( 'pagination_prev_label' ) );
		}

		$next_page = intval( $paged ) + 1;

		if ( $next_page <= $page_limit ) {
			$return['next'] = sprintf( $link_template, 'next', get_pagenum_link( $next_page ), $this->get_settings_for_display( 'pagination_next_label' ) );
		} else {
			$return['next'] = sprintf( $disabled_template, 'next', $this->get_settings_for_display( 'pagination_next_label' ) );
		}

		return $return;
	}

	/**
	 * Registers additional settings.
	 *
	 * @access protected
	 */
	protected function register_skins() {
		$this->add_skin( new Skin_Cards( $this ) );
	}

	/**
	 * Register controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'elementor-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->end_controls_section();

		$this->register_query_section_controls();
		$this->register_pagination_section_controls();
	}

	/**
	 * Register query controls.
	 *
	 * @access protected
	 */
	protected function register_query_section_controls() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Query', 'sermon-manager' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_group_control(
			Group_Control_Sermons::get_type(),
			array(
				'name' => 'posts',
			)
		);

		$this->add_control(
			'advanced',
			array(
				'label'     => __( 'Advanced', 'sermon-manager' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'posts_post_type!' => 'current_query',
				),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'     => __( 'Order By', 'sermon-manager' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'post_date',
				'options'   => array(
					'post_date'     => __( 'Published Date', 'sermon-manager' ),
					'preached_date' => __( 'Preached Date', 'sermon-manager' ),
					'post_title'    => __( 'Title', 'sermon-manager' ),
					'menu_order'    => __( 'Menu Order', 'sermon-manager' ),
					'rand'          => __( 'Random', 'sermon-manager' ),
				),
				'condition' => array(
					'posts_post_type!' => 'current_query',
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'     => __( 'Order', 'sermon-manager' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'desc',
				'options'   => array(
					'asc'  => __( 'ASC', 'sermon-manager' ),
					'desc' => __( 'DESC', 'sermon-manager' ),
				),
				'condition' => array(
					'posts_post_type!' => 'current_query',
				),
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'       => __( 'Offset', 'sermon-manager' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'condition'   => array(
					'posts_post_type!' => array(
						'by_id',
						'current_query',
					),
				),
				'description' => __( 'Use this setting to skip over sermons (e.g. \'2\' to skip over 2 sermons).', 'sermon-manager' ),
			)
		);

		Module::add_exclude_controls( $this );

		$this->end_controls_section();
	}

	/**
	 * Register controls for pagination.
	 */
	public function register_pagination_section_controls() {
		$this->start_controls_section(
			'section_pagination',
			array(
				'label' => __( 'Pagination', 'elementor-pro' ),
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'   => __( 'Pagination', 'elementor-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''                      => __( 'None', 'elementor-pro' ),
					'numbers'               => __( 'Numbers', 'elementor-pro' ),
					'prev_next'             => __( 'Previous/Next', 'elementor-pro' ),
					'numbers_and_prev_next' => __( 'Numbers', 'elementor-pro' ) . ' + ' . __( 'Previous/Next', 'elementor-pro' ),
				),
			)
		);

		$this->add_control(
			'pagination_page_limit',
			array(
				'label'     => __( 'Page Limit', 'elementor-pro' ),
				'default'   => '5',
				'condition' => array(
					'pagination_type!' => '',
				),
			)
		);

		$this->add_control(
			'pagination_numbers_shorten',
			array(
				'label'        => __( 'Shorten', 'elementor-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'condition'    => array(
					'pagination_type' => array(
						'numbers',
						'numbers_and_prev_next',
					),
				),
			)
		);

		$this->add_control(
			'pagination_prev_label',
			array(
				'label'     => __( 'Previous Label', 'elementor-pro' ),
				'default'   => __( '&laquo; Previous', 'elementor-pro' ),
				'condition' => array(
					'pagination_type' => array(
						'prev_next',
						'numbers_and_prev_next',
					),
				),
			)
		);

		$this->add_control(
			'pagination_next_label',
			array(
				'label'     => __( 'Next Label', 'elementor-pro' ),
				'default'   => __( 'Next &raquo;', 'elementor-pro' ),
				'condition' => array(
					'pagination_type' => array(
						'prev_next',
						'numbers_and_prev_next',
					),
				),
			)
		);

		$this->add_control(
			'pagination_align',
			array(
				'label'     => __( 'Alignment', 'elementor-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
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
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .elementor-pagination' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'pagination_type!' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pagination_style',
			array(
				'label'     => __( 'Pagination', 'elementor-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pagination_type!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pagination_typography',
				'selector' => '{{WRAPPER}} .elementor-pagination',
				'scheme'   => 'TYPOGRAPHY_2', //Typography::TYPOGRAPHY_2,
			)
		);

		$this->add_control(
			'pagination_color_heading',
			array(
				'label'     => __( 'Colors', 'elementor-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'pagination_colors' );

		$this->start_controls_tab(
			'pagination_color_normal',
			array(
				'label' => __( 'Normal', 'elementor-pro' ),
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => __( 'Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-pagination .page-numbers:not(.dots)' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_color_hover',
			array(
				'label' => __( 'Hover', 'elementor-pro' ),
			)
		);

		$this->add_control(
			'pagination_hover_color',
			array(
				'label'     => __( 'Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-pagination a.page-numbers:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_color_active',
			array(
				'label' => __( 'Active', 'elementor-pro' ),
			)
		);

		$this->add_control(
			'pagination_active_color',
			array(
				'label'     => __( 'Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-pagination .page-numbers.current' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'pagination_spacing',
			array(
				'label'     => __( 'Space Between', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'separator' => 'before',
				'default'   => array(
					'size' => 10,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'body:not(.rtl) {{WRAPPER}} .elementor-pagination .page-numbers:not(:first-child)' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'body:not(.rtl) {{WRAPPER}} .elementor-pagination .page-numbers:not(:last-child)'  => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'body.rtl {{WRAPPER}} .elementor-pagination .page-numbers:not(:first-child)'       => 'margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'body.rtl {{WRAPPER}} .elementor-pagination .page-numbers:not(:last-child)'        => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 );',
				),
			)
		);

		$this->end_controls_section();
	}
}

try {
	\Elementor\Plugin::instance()->widgets_manager->register( new Sermon_Archive() );
} catch ( \Exception $e ) {
	Plugin::instance()->notice_manager->add_error( 'elementor_element_init_error_archive', $e->getMessage(), 10, 'elementor' );
}
