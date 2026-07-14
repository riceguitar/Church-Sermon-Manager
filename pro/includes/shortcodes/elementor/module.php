<?php
/**
 * Elementor Pro module class implementation.
 *
 * @since   2.0.4
 *
 * @package SMP\Shortcodes\Elementor
 */

namespace SMP\Shortcodes\Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Ajax_Manager;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Module definition.
 *
 * @package SMP\Shortcodes\Elementor
 */
class Module extends Module_Base {

	const QUERY_CONTROL_ID = 'query';

	/**
	 * Displayed IDs.
	 *
	 * @var array
	 */
	public static $displayed_ids = array();

	/**
	 * Module constructor.
	 *
	 * @throws \ReflectionException Ehh.
	 */
	public function __construct() {
		parent::__construct();

		$this->add_actions();
	}

	/**
	 * Adds required actions.
	 */
	protected function add_actions() {
		add_action( 'elementor/ajax/register_actions', array( $this, 'register_ajax_actions' ) );
		add_action( 'elementor/controls/register', array( $this, 'register_controls' ) );

		/**
		 * Fix query offset and pagination.
		 *
		 * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
		 */
		add_action( 'pre_get_posts', array( $this, 'fix_query_offset' ), 1 );
		add_filter( 'found_posts', array( $this, 'fix_query_found_posts' ), 1, 2 );
	}

	/**
	 * Add to avoid list.
	 *
	 * @param array $ids The IDs.
	 */
	public static function add_to_avoid_list( $ids ) {
		self::$displayed_ids = array_merge( self::$displayed_ids, $ids );
	}

	/**
	 * Get avoid list IDs.
	 */
	public static function get_avoid_list_ids() {
		return self::$displayed_ids;
	}

	/**
	 * Add exclude controls.
	 *
	 * @param Widget_Base $widget The widget instance.
	 */
	public static function add_exclude_controls( $widget ) {
		$widget->add_control(
			'exclude',
			array(
				'label'       => __( 'Exclude', 'elementor-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'options'     => array(
					'current_post'     => __( 'Current Post', 'elementor-pro' ),
					'manual_selection' => __( 'Manual Selection', 'elementor-pro' ),
				),
				'label_block' => true,
			)
		);

		$widget->add_control(
			'exclude_ids',
			array(
				'label'       => __( 'Search & Select', 'elementor-pro' ),
				'type'        => self::QUERY_CONTROL_ID,
				'post_type'   => '',
				'options'     => array(),
				'label_block' => true,
				'multiple'    => true,
				'filter_type' => 'by_id',
				'condition'   => array(
					'exclude' => 'manual_selection',
				),
			)
		);

		$widget->add_control(
			'avoid_duplicates',
			array(
				'label'       => __( 'Avoid Duplicates', 'elementor-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => '',
				'description' => __( 'Set to Yes to avoid duplicate posts from showing up, This only effects the frontend.', 'elementor-pro' ),
			)
		);

	}

	/**
	 * Get query args.
	 *
	 * @param string $control_id The control ID.
	 * @param array  $settings   The settings.
	 *
	 * @return array|mixed
	 */
	public static function get_query_args( $control_id, $settings ) {
		$defaults = array(
			$control_id . '_post_type' => 'post',
			$control_id . '_posts_ids' => array(),
			'orderby'                  => 'date',
			'order'                    => 'desc',
			'posts_per_page'           => 3,
			'offset'                   => 0,
		);

		$settings = wp_parse_args( $settings, $defaults );

		$post_type = $settings[ $control_id . '_post_type' ];

		if ( 'current_query' === $post_type ) {
			$current_query_vars = $GLOBALS['wp_query']->query_vars;

			return $current_query_vars;
		}

		$query_args = array(
			'orderby'             => $settings['orderby'],
			'order'               => $settings['order'],
			'ignore_sticky_posts' => 1,
			'post_status'         => 'publish', // Hide drafts/private posts for admins.
		);

		if ( 'preached_date' === $query_args['orderby'] ) {
			$query_args['meta_key']       = 'sermon_date';
			$query_args['meta_value_num'] = time();
			$query_args['meta_compare']   = '<=';
			$query_args['orderby']        = 'meta_value_num';
			$query_args['order']          = 'DESC';
		}

		if ( 'by_id' === $post_type ) {
			$query_args['post_type']      = 'any';
			$query_args['posts_per_page'] = - 1;

			$query_args['post__in'] = $settings[ $control_id . '_posts_ids' ];

			if ( empty( $query_args['post__in'] ) ) {
				// If no selection - return an empty query.
				$query_args['post__in'] = array( 0 );
			}
		} else {
			$query_args['post_type']      = $post_type;
			$query_args['posts_per_page'] = $settings['posts_per_page'];
			$query_args['tax_query']      = array();

			if ( 0 < $settings['offset'] ) {
				/**
				 * Due to a WordPress bug, the offset will be set later, in $this->fix_query_offset()
				 *
				 * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
				 */
				$query_args['offset_to_fix'] = $settings['offset'];
			}

			$taxonomies = get_object_taxonomies( $post_type, 'objects' );

			foreach ( $taxonomies as $object ) {
				$setting_key = $control_id . '_' . $object->name . '_ids';

				if ( ! empty( $settings[ $setting_key ] ) ) {
					$query_args['tax_query'][] = array(
						'taxonomy' => $object->name,
						'field'    => 'term_id',
						'terms'    => $settings[ $setting_key ],
					);
				}
			}
		}

		if ( ! empty( $settings[ $control_id . '_authors' ] ) ) {
			$query_args['author__in'] = $settings[ $control_id . '_authors' ];
		}

		$post__not_in = array();
		if ( ! empty( $settings['exclude'] ) ) {
			if ( in_array( 'current_post', $settings['exclude'] ) ) {
				if ( Utils::is_ajax() && ! empty( $_REQUEST['post_id'] ) ) {
					$post__not_in[] = $_REQUEST['post_id'];
				} elseif ( is_singular() ) {
					$post__not_in[] = get_queried_object_id();
				}
			}

			if ( in_array( 'manual_selection', $settings['exclude'] ) && ! empty( $settings['exclude_ids'] ) ) {
				$post__not_in = array_merge( $post__not_in, $settings['exclude_ids'] );
			}
		}

		if ( ! empty( $settings['avoid_duplicates'] ) && 'yes' === $settings['avoid_duplicates'] ) {
			$post__not_in = array_merge( $post__not_in, self::$displayed_ids );
		}

		$query_args['post__not_in'] = $post__not_in;

		return $query_args;
	}

	/**
	 * Get the name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'query-control';
	}

	/**
	 * Post titles ajax.
	 *
	 * @param array $request The request.
	 *
	 * @return array
	 */
	public function ajax_posts_control_value_titles( $request ) {
		$ids = (array) $request['id'];

		$results = array();

		if ( 'taxonomy' === $request['filter_type'] ) {

			$terms = get_terms(
				array(
					'include'    => $ids,
					'hide_empty' => false,
				)
			);

			foreach ( $terms as $term ) {
				$results[ $term->term_id ] = $term->name;
			}
		} elseif ( 'by_id' === $request['filter_type'] || 'post' === $request['filter_type'] ) {
			$query = new \WP_Query(
				array(
					'post_type'      => 'any',
					'post__in'       => $ids,
					'posts_per_page' => - 1,
				)
			);

			foreach ( $query->posts as $post ) {
				$results[ $post->ID ] = $post->post_title;
			}
		} elseif ( 'author' === $request['filter_type'] ) {
			$query_params = array(
				'who'                 => 'authors',
				'has_published_posts' => true,
				'fields'              => array(
					'ID',
					'display_name',
				),
				'include'             => $ids,
			);

			$user_query = new \WP_User_Query( $query_params );

			foreach ( $user_query->get_results() as $author ) {
				$results[ $author->ID ] = $author->display_name;
			}
		}

		return $results;
	}

	/**
	 * Registers controls.
	 */
	public function register_controls() {
		$controls_manager = Plugin::instance()->controls_manager;

		/* @noinspection PhpParamsInspection */
		$controls_manager->add_group_control( Group_Control_Sermons::get_type(), new Group_Control_Sermons() );
	}

	/**
	 * Fix query offset.
	 *
	 * @param \WP_Query $query The query.
	 */
	public function fix_query_offset( &$query ) {
		if ( ! empty( $query->query_vars['offset_to_fix'] ) ) {
			if ( $query->is_paged ) {
				$query->query_vars['offset'] = $query->query_vars['offset_to_fix'] + ( ( $query->query_vars['paged'] - 1 ) * $query->query_vars['posts_per_page'] );
			} else {
				$query->query_vars['offset'] = $query->query_vars['offset_to_fix'];
			}
		}
	}

	/**
	 * Fix query's found posts.
	 *
	 * @param int       $found_posts The number of found posts.
	 * @param \WP_Query $query       The query.
	 *
	 * @return mixed
	 */
	public function fix_query_found_posts( $found_posts, $query ) {
		$offset_to_fix = $query->get( 'fix_pagination_offset' );

		if ( $offset_to_fix ) {
			$found_posts -= $offset_to_fix;
		}

		return $found_posts;
	}

	/**
	 * Registers ajax actions.
	 *
	 * @param Ajax_Manager $ajax_manager The ajax manager.
	 */
	public function register_ajax_actions( $ajax_manager ) {
		$ajax_manager->register_ajax_action( 'query_control_value_titles', array(
			$this,
			'ajax_posts_control_value_titles',
		) );
	}
}

try {
	new Module();
} catch ( \ReflectionException $e ) {
	return;
}
