<?php
/**
 * Used to initialize and render archive shortcode.
 *
 * @since   2.0.4
 *
 * @package SMP\Shortcodes\WordPress
 */

namespace SMP\Shortcodes;

/**
 * Class SMP_Shortcode_Archive
 */
class WP_Archive extends WP_Shortcode {
	/**
	 * Shortcode type.
	 *
	 * @var   string
	 */
	protected $type = 'sermons';

	/**
	 * Initialize shortcode.
	 *
	 * @param array  $attributes Shortcode attributes.
	 * @param string $type       Shortcode type.
	 */
	public function __construct( $attributes = array(), $type = 'sermons' ) {
		parent::__construct( $attributes, $type );
	}

	/**
	 * Get shortcode content.
	 *
	 * @return string
	 */
	public function get_content() {
		$archive_page_id     = (int) \SermonManager::getOption( 'smp_archive_page', 0 );
		$is_archive_page     = $archive_page_id ? is_page( $archive_page_id ) : false;
		$archive_page_slug   = $archive_page_id ? get_post_field( 'post_name', $archive_page_id ) : '';
		$sermon_archive_slug = str_replace( '/', '', \SermonManager::getOption( 'archive_slug' ) );

		if ( $is_archive_page && $sermon_archive_slug !== $archive_page_slug ) {
			ob_start();
			?>
			<h3>All good!</h3>
			<p>Page assignment is successfully set up. This is the page which will be used for
				sermons <?php echo $is_archive_page ? 'archive' : 'taxonomy'; ?> views.</p>
			<p>To try it out, just go to the <a href="<?php echo get_post_type_archive_link( 'wpfc_sermon' ); ?>">sermons <?php echo $archive_page ? 'archive' : 'taxonomy'; ?>
					page</a>, as usual.</p>
			<?php
			return ob_get_clean();
		} else {
			return $this->the_loop();
		}
	}

	/**
	 * Loop over found sermons.
	 *
	 * @return string
	 */
	protected function the_loop() {
		if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
			define( 'SM_ENQUEUE_SCRIPTS_STYLES', true );
		}

		global $wp_query, $post;
		$original_query = $wp_query;
		$original_post  = $post;
		$wp_query       = $this->get_query_results();
		$post           = $wp_query->post;
		$attributes     = $this->get_attributes();

		ob_start();
		?>
		<div class="smpro-items">
			<?php
			if ( $attributes['filtering'] ) {
				echo render_wpfc_sorting( $attributes['filtering_args'] );
			}

			if ( $attributes['columns'] ) {
				echo '<style>', wp_sprintf( '.smpro-items-container, .smpro-items {--smpro-layout-columns: %s !important}', $attributes['columns'] ), '</style>';
			}

			$args = array(
				'attributes' => $attributes,
			);

			if ( have_posts() ) :
				echo apply_filters( 'smp/shortcodes/wordpress/archive/before_loop', '' );

				while ( have_posts() ) :
					the_post();
					wpfc_sermon_excerpt_v2( false, $args ); // You can edit the content of this function in `partials/content-sermon-archive.php`.
				endwhile;

				echo apply_filters( 'smp/shortcodes/wordpress/archive/after_loop', '' );
			else :
				echo __( 'Sorry, but there aren\'t any posts matching your query.' );
			endif;
			?>
		</div>
		<?php

		if ( $attributes['paginate'] ) {
			echo '<div class="sm-pagination ast-pagination">';
			sm_pagination();
			echo '</div>';
		}

		$wp_query = $original_query;
		$post     = $original_post;

		return ob_get_clean();
	}

	/**
	 * Parse attributes.
	 *
	 * @param  array $attributes Shortcode attributes.
	 *
	 * @return array
	 */
	protected function parse_attributes( $attributes ) {
		// Legacy convert.
		$old_options = array(
			'count'      => 'limit',
			'per_page'   => 'limit',
			'pagination' => 'paginate',
		);

		foreach ( $old_options as $old_option => $new_option ) {
			if ( ! empty( $attributes[ $old_option ] ) ) {
				$attributes[ $new_option ] = $attributes[ $old_option ];
			}
			unset( $attributes[ $old_option ] );
		}
		$attributes = shortcode_atts(
			
			array(
				'limit'              => get_option( 'posts_per_page' ) ?: 10, // Results limit.
				'count'              => '', // the same thing as limit. Deprecated.
				'per_page'           => '', // the same thing as limit. Deprecated.
				'columns'            => '', // Number of columns.
				'rows'               => '', // Number of rows. If defined, limit will be ignored.
				'orderby'            => 'date_preached', // menu_order, title, date, date_preached, rand.
				'order'              => 'DESC', // ASC or DESC.
				'ids'                => '', // Comma separated IDs.
				'terms'              => '', // Comma separated term slugs or ids.
				'taxonomy'           => '', // The terms taxonomy.
				'terms_operator'     => 'IN', // Operator to compare terms. Possible values are 'IN', 'NOT IN', 'AND'.
				'class'              => '', // HTML class.
				'page'               => 1, // Page for pagination.
				'paginate'           => 'yes', // Should results be paginated.
				'cache'              => 'yes', // Should shortcode output be cached.
				'filtering'          => 'yes', // Should we show filtering.
				'hide_topics'        => '',
				'hide_series'        => '',
				'hide_preachers'     => '',
				'hide_books'         => '',
				'hide_service_types' => \SermonManager::getOption( 'service_type_filtering' ) ? '' : 'yes',
			),
			$attributes,
			$this->type
		);

		// Convert values.
		foreach ( $attributes as $name => &$value ) {
			if ( is_numeric( $value ) ) {
				$value = intval( $value );
				continue;
			}

			$value = smp_string_to_bool( $value, false );
		}

		if ( ! absint( $attributes['columns'] ) ) {
			$attributes['columns'] = smp_get_sermons_per_row();
		}

		$attributes['filtering_args'] = array(
			'hide_topics'           => $attributes['hide_topics'],
			'hide_series'           => $attributes['hide_series'],
			'hide_preachers'        => $attributes['hide_preachers'],
			'hide_books'            => $attributes['hide_books'],
			'hide_service_types'    => $attributes['hide_service_types'],
			'smp_override_settings' => true,
		);

		return $attributes;
	}

	/**
	 * Parse query args.
	 *
	 * @return array
	 */
	protected function parse_query_args() {
		$query_args = array(
			'post_type'           => 'wpfc_sermon',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => false === smp_string_to_bool( $this->attributes['paginate'] ),
			'orderby'             => $this->attributes['orderby'],
			'order'               => strtoupper( $this->attributes['order'] ),
		);

		if ( smp_string_to_bool( $this->attributes['paginate'] ) ) {
			$this->attributes['page'] = absint( empty( $GLOBALS['paged'] ) ? 1 : $GLOBALS['paged'] );
		}

		if ( ! empty( $this->attributes['rows'] ) ) {
			$this->attributes['limit'] = $this->attributes['columns'] * $this->attributes['rows'];
		}

		if ( 'date_preached' === $query_args['orderby'] ) {
			$query_args['meta_key']       = 'sermon_date';
			$query_args['meta_value_num'] = time();
			$query_args['meta_compare']   = ' <= ';
			$query_args['orderby']        = 'meta_value_num';
		}

		$query_args['posts_per_page']         = intval( $this->attributes['limit'] );
		$query_args['posts_per_archive_page'] = $query_args['posts_per_page'];
		if ( 1 < $this->attributes['page'] ) {
			$query_args['paged'] = absint( $this->attributes['page'] );
		}
		$query_args['tax_query'] = array();

		// Set specific types query args.
		if ( method_exists( $this, "set_{$this->type}_query_args" ) ) {
			$this->{"set_{$this->type}_query_args"}( $query_args );
		}

		$query_args = smp_add_taxonomy_to_query( $query_args );

		$query_args = apply_filters( 'smp/shortcodes/wordpress/archive_query', $query_args, $this->attributes, $this->type );

		return $query_args;
	}
}
