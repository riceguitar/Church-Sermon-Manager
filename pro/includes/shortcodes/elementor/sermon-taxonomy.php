<?php
/**
 * Sermon Manager's Sermon Taxonomy widget for Elementor.
 *
 * @since   2.0.4
 * @package SMP\Shortcodes\Elementor
 */

namespace SMP\Shortcodes\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use SMP\Plugin;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;

defined( 'ABSPATH' ) or exit;

/**
 * Init class.
 */
class Sermon_Taxonomy extends Widget_Base {

	/**
	 * Disable "Default" skin option, since it does not work because we don't have a third, skin-less view.
	 *
	 * @var bool
	 */
	protected $_has_template_content = false;

	/**
	 * Returns widget name (slug).
	 *
	 * @return string
	 */
	public function get_name() {
		return 'sermon_taxonomy';
	}

	/**
	 * Returns the styles the widget depends on.
	 *
	 * The taxonomy skins reuse Elementor Pro's Posts widget markup (elementor-post,
	 * elementor-grid, elementor-post__card), whose grid and card layout live in the
	 * "widget-posts" stylesheet. Elementor only enqueues that on the front end when
	 * a widget declares it as a dependency, so without this the cards render
	 * unstyled outside the editor (the editor loads every widget's CSS).
	 *
	 * @return array The list of style handles.
	 */
	public function get_style_depends() {
		return array( 'widget-posts' );
	}

	/**
	 * Returns widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Sermon Taxonomy', 'church-sermon-manager' );
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
	 * Renders the pagination.
	 */
	public function render_pagination() {
		$terms    = $this->query_terms();
		$settings = $this->get_settings();

		if ( ! empty( $terms ) ) {
			if ( $settings['pagination_type'] != '' ) {

				$page_limit = intval( $settings['terms_per_page'] ) ?: 6;

				switch ( $settings['taxonomy'] ) {
					case 'series':
						$taxonomy = 'wpfc_sermon_series';
						break;
					case 'preachers':
						$taxonomy = 'wpfc_preacher';
						break;
					case 'topics':
						$taxonomy = 'wpfc_sermon_topics';
						break;
					case 'books':
						$taxonomy = 'wpfc_bible_book';
						break;
					case 'service_types':
						$taxonomy = 'wpfc_service_type';
						break;
					default:
						$taxonomy = 'wpfc_sermon_series';
				}

				if ( 2 < $page_limit ) {
					$http_host   = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
					$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
					$url         = "//{$http_host}{$request_uri}";
					$url         = preg_replace( '/(\?|&)term_page=\d+/', '', $url );
					$base_url    = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );

					$pagination_args = array(
						'base'               => $base_url . '%_%',
						'format'             => ( strpos( $url, '?' ) !== false ? '&' : '?' ) . 'term_page=%#%',
						'type'               => 'array',
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only request state, no data mutation.
						'current'            => isset( $_GET['term_page'] ) ? intval( $_GET['term_page'] ) ?: 1 : 1,
						'total'              => min(round( ( wp_count_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => 'yes' === $settings['hide_empty'] ) ) - intval( $settings['offset'] ) ) / $page_limit, 0, PHP_ROUND_HALF_UP ), $settings['pagination_page_limit']),
						'show_all'           => 'yes' !== $settings['pagination_numbers_shorten'],
						'prev_text'    		 => $settings['pagination_prev_label'],
						'next_text'    		 => $settings['pagination_next_label'],
						'prev_next' 		 => false,
						'before_page_number' => '<span class="elementor-screen-only">' . __( 'Page', 'elementor-pro' ) . '</span>',
					);

					if ( ( 'prev_next' == $settings['pagination_type'] ) or ( 'numbers_and_prev_next' == $settings['pagination_type'] ) ) {
						$pagination_args['prev_next'] = true;
					}

					if ( 'prev_next' == $settings['pagination_type'] ) {
						echo '<style> .page-numbers{display:none}.next.page-numbers,.prev.page-numbers{display:inline-block}</style>';
					}
					$links = paginate_links( $pagination_args );

					?>
					<nav class="elementor-pagination" role="navigation"
							aria-label="<?php esc_attr_e( 'Pagination', 'elementor-pro' ); ?>">
						<?php echo implode( PHP_EOL, $links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core paginate_links() markup. ?>
					</nav>
					<?php
				}
			}
		}
	}

	/**
	 * Create the query.
	 */
	public function query_terms() {
		$settings = $this->get_settings();

		switch ( $settings['taxonomy'] ) {
			case 'series':
				$taxonomy = 'wpfc_sermon_series';
				break;
			case 'preachers':
				$taxonomy = 'wpfc_preacher';
				break;
			case 'topics':
				$taxonomy = 'wpfc_sermon_topics';
				break;
			case 'books':
				$taxonomy = 'wpfc_bible_book';
				break;
			case 'service_types':
				$taxonomy = 'wpfc_service_type';
				break;
			case 'books_order':
				$taxonomy   = 'wpfc_bible_book';
				$order_book = true;
		}

		if ( empty( $taxonomy ) ) {
			return array();
		}

		$show_all = $settings['show_alphabetically'] && 'wpfc-list-taxonomy' === $settings['_skin'];

		// Calculate the offset.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only request state, no data mutation.
		$current_page   = isset( $_GET['term_page'] ) ? intval( $_GET['term_page'] ) ?: 1 : 1; // 1-indexed.
		$terms_per_page = intval( $settings['terms_per_page'] );
		$offset         = intval( $settings['offset'] );
		if ( 1 === $current_page ) {
			$query_offset = $offset;
		} else {
			$query_offset = ( ( $current_page - 1 ) * $terms_per_page ) + $offset;
		}

		$query_args = array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => 'yes' === $settings['hide_empty'],
			'order'      => $settings['order'],
			'offset'     => $query_offset,
			'number'     => $show_all ? 0 : intval( $settings['terms_per_page'] ), // 0 = all terms.
		);

		if ( $show_all ) {
			$query_args += array(
				'orderby' => 'name',
			);
		} elseif ( 'latest_sermon' === $settings['orderby'] ) {
			$query_args += array(
				'orderby'        => 'meta_value_num',
				'meta_key'       => 'sermon_date',
				'meta_value_num' => time(),
				'meta_compare'   => '<=',
			);
		} else {
			$query_args += array(
				'orderby' => strtoupper( $settings['orderby'] ),
			);
		}

		if ( isset( $order_book ) && true === $order_book && ! $show_all ) {
			$query_args['number'] = 0; // All terms.
		}

		$terms = get_terms( $query_args );

		if ( isset( $order_book ) && true === $order_book && ! $show_all ) {
			// Book order.
			$books = array(
				'Genesis',
				'Exodus',
				'Leviticus',
				'Numbers',
				'Deuteronomy',
				'Joshua',
				'Judges',
				'Ruth',
				'1 Samuel',
				'2 Samuel',
				'1 Kings',
				'2 Kings',
				'1 Chronicles',
				'2 Chronicles',
				'Ezra',
				'Nehemiah',
				'Esther',
				'Job',
				'Psalm',
				'Proverbs',
				'Ecclesiastes',
				'Song of Songs',
				'Isaiah',
				'Jeremiah',
				'Lamentations',
				'Ezekiel',
				'Daniel',
				'Hosea',
				'Joel',
				'Amos',
				'Obadiah',
				'Jonah',
				'Micah',
				'Nahum',
				'Habakkuk',
				'Zephaniah',
				'Haggai',
				'Zechariah',
				'Malachi',
				'Matthew',
				'Mark',
				'Luke',
				'John',
				'Acts',
				'Romans',
				'1 Corinthians',
				'2 Corinthians',
				'Galatians',
				'Ephesians',
				'Philippians',
				'Colossians',
				'1 Thessalonians',
				'2 Thessalonians',
				'1 Timothy',
				'2 Timothy',
				'Titus',
				'Philemon',
				'Hebrews',
				'James',
				'1 Peter',
				'2 Peter',
				'1 John',
				'2 John',
				'3 John',
				'Jude',
				'Revelation',
				'Topical',
			);

			$ordered_terms = array();

			// Assign every book a number.
			foreach ( $terms as $term ) {
				$ordered_terms[ array_search( $term->name, $books ) ] = $term;
			}

			// Order the numbers (books).
			ksort( $ordered_terms );

			$terms = $ordered_terms;

			$terms = array_slice( $terms, $offset, $terms_per_page, false );
		}

		if ( $show_all ) {
			$ordered_terms = array();

			foreach ( $terms as $term ) {
				$term_name = trim( preg_replace( '/^\d+/', '', $term->name ) ) ?: $term->name;

				$ordered_terms[ $term_name ] = $term;
			}

			if ( defined( 'SORT_NATURAL' ) ) {
				ksort( $ordered_terms, SORT_NATURAL ); // phpcs:ignore
			} else {
				natsort( $ordered_terms );
			}

			if ( 'DESC' === $settings['order'] ) {
				$ordered_terms = array_reverse( $ordered_terms );
			}

			$terms = array();

			foreach ( $ordered_terms as $term ) {
				$terms[] = $term;
			}
		}

		return $terms;
	}

	/**
	 * Register controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'church-sermon-manager' ),
			)
		);

		$this->add_control(
			'terms_per_page',
			array(
				'label'      => __( 'Terms Per Page', 'church-sermon-manager' ),
				'type'       => Controls_Manager::NUMBER,
				'default'    => 6,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'show_alphabetically',
							'operator' => '==',
							'value'    => '',
						),
						array(
							'name'     => '_skin',
							'operator' => '!=',
							'value'    => 'wpfc-list-taxonomy',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'              => __( 'Columns', 'church-sermon-manager' ),
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
				'frontend_available' => false,
			)
		);

		$this->add_control(
			'show_alphabetically',
			array(
				'label'        => __( 'Show Alphabetical List', 'church-sermon-manager' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'church-sermon-manager' ),
				'label_off'    => __( 'Hide', 'church-sermon-manager' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
				'condition'    => array(
					'_skin' => 'wpfc-list-taxonomy',
				),
			)
		);

		$this->end_controls_section();

		$this->register_design_letter_controls();
		$this->register_query_section_controls();
		$this->register_pagination_section_controls();
	}

	/**
	 * Design content controls.
	 */

	protected function register_design_letter_controls() {

		$this->start_controls_section(
			'section_design_content',
			array(
				'label' => __( 'Letter', 'elementor-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'show_alphabetically',
							'operator' => '!=',
							'value'    => '',
						),
						array(
							'name'     => '_skin',
							'operator' => '==',
							'value'    => 'wpfc-list-taxonomy',
						),
					),
				),
			)
		);

		$this->add_control(
			'letter_top_padding',
			array(
				'label'     => __( 'Letter Top Padding', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
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
					'.wpfc-term-letter-section .wpfc-term-letter' => 'padding-top: {{SIZE}}px;',
				),
				'conditions' => array(
					'terms'    => array(
						array(
							'name'     => 'show_alphabetically',
							'operator' => '!=',
							'value'    => '',
						)
					)
				),
			)
		);

		$this->add_control(
			'letter_bottom_padding',
			array(
				'label'     => __( 'Letter Bottom Padding', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
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
					'.wpfc-term-letter-section .wpfc-term-letter' => 'padding-bottom: {{SIZE}}px;',
				),
				'conditions' => array(
					'terms'    => array(
						array(
							'name'     => 'show_alphabetically',
							'operator' => '!=',
							'value'    => '',
						)
					)
				),
			)
		);

		$this->add_control(
			'letter_color',
			array(
				'label'     => __( 'Letter Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'.wpfc-term-letter-section .wpfc-term-letter' => 'color: {{VALUE}};',
				)
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'letter_typography',
				'scheme'    => Typography::TYPOGRAPHY_1,
				'selector'  => '.wpfc-term-letter-section .wpfc-term-letter',
			)
		);

		$this->add_control(
			'letter_alignment',
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
				'selectors' => array(
					'.wpfc-term-letter-section .wpfc-term-letter' => 'text-align: {{VALUE}};',
				)
			)
		);

		$this->end_controls_section();
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
				'label' => __( 'Query', 'church-sermon-manager' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'taxonomy',
			array(
				'label'   => __( 'Source', 'church-sermon-manager' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'series',
				'options' => array(
					'series'        => __( 'Series', 'church-sermon-manager' ),
					'preachers'     => __( 'Preachers', 'church-sermon-manager' ),
					'topics'        => __( 'Topics', 'church-sermon-manager' ),
					'books'         => __( 'Books', 'church-sermon-manager' ),
					'books_order'   => __( 'Books (Book order)', 'church-sermon-manager' ),
					'service_types' => __( 'Service Types', 'church-sermon-manager' ),
				),
			)
		);

		$this->add_control(
			'advanced',
			array(
				'label'     => __( 'Advanced', 'church-sermon-manager' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'hide_empty',
			array(
				'label'   => __( 'Hide terms with no sermons', 'church-sermon-manager' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'options' => array(
					'yes' => __( 'Yes', 'church-sermon-manager' ),
					'no'  => __( 'No', 'church-sermon-manager' ),
				),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'      => __( 'Order By', 'church-sermon-manager' ),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'term_title',
				'options'    => array(
					'name'          => __( 'Title', 'church-sermon-manager' ),
					'latest_sermon' => __( 'Latest Sermon', 'church-sermon-manager' ),
					'count'         => __( 'Sermon Count', 'church-sermon-manager' ),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'show_alphabetically',
							'operator' => '==',
							'value'    => '',
						),
						array(
							'name'     => '_skin',
							'operator' => '!=',
							'value'    => 'wpfc-list-taxonomy',
						),
					),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => __( 'Order', 'church-sermon-manager' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'asc',
				'options' => array(
					'asc'  => __( 'ASC', 'church-sermon-manager' ),
					'desc' => __( 'DESC', 'church-sermon-manager' ),
				),
			)
		);

		$this->add_control(
			'offset',
			array(
				'label'       => __( 'Offset', 'church-sermon-manager' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'description' => __( 'Use this setting to skip over terms( e . g . \'2\' to skip over 2 terms).', 'church-sermon-manager' ),
			)
		);

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
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'     => 'show_alphabetically',
							'operator' => '==',
							'value'    => '',
						),
						array(
							'name'     => '_skin',
							'operator' => '!=',
							'value'    => 'wpfc-list-taxonomy',
						),
					),
				),
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'   => __( 'Pagination', 'elementor-pro' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'numbers_and_prev_next',
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
				'conditions' => array(
					'relation' => 'and',
					'terms'    => array(
						array(
							'name'     => 'show_alphabetically',
							'operator' => '==',
							'value'    => '',
						),
						array(
							'name'     => 'pagination_type',
							'operator' => '!=',
							'value'    => '',
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pagination_typography',
				'selector' => '{{WRAPPER}} .elementor-pagination',
				'scheme'   => Typography::TYPOGRAPHY_2,
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

	/**
	 * Registers additional settings.
	 *
	 * @access protected
	 */
	protected function register_skins() {
		$this->add_skin( new Skin_List_Taxonomy( $this ) );
		$this->add_skin( new Skin_Cards_Taxonomy( $this ) );
	}
}

try {
	\Elementor\Plugin::instance()->widgets_manager->register( new Sermon_Taxonomy() );
} catch ( \Exception $e ) {
	Plugin::instance()->notice_manager->add_error( 'elementor_element_init_error_taxonomy', $e->getMessage(), 10, 'elementor' );
}
