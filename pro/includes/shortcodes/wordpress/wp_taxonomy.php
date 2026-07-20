<?php
/**
 * Used to initialize and render taxonomy shortcode.
 *
 * @since   2.0.4
 *
 * @package SMP\Shortcodes\WordPress
 */

namespace SMP\Shortcodes;

use SMP\Templating\Settings;

/**
 * Class SMP_Shortcode_Taxonomy
 */
class WP_Taxonomy extends WP_Archive {
	/**
	 * Shortcode type.
	 *
	 * @var   string
	 */
	protected $type = 'sermons_tax';

	/**
	 * Initialize shortcode.
	 *
	 * @param array  $attributes Shortcode attributes.
	 * @param string $type       Shortcode type.
	 */
	public function __construct( array $attributes = array(), $type = 'sermons_tax' ) {
		$this->init_archive();
		parent::__construct( $attributes, $type );
	}

	/**
	 * Initializes archive shortcode, but changes some parameters.
	 */
	protected function init_archive() {
		add_filter( 'smp/shortcodes/wordpress/archive_query', array( $this, 'modify_query' ) );
	}

	/**
	 * Renders the view.
	 *
	 * @return string
	 */
	public function get_content() {
		if ( is_page( \SermonManager::getOption( 'smp_tax_page', 0 ) ) ) {
			ob_start();

			$taxonomies = sm_get_taxonomies();

			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomies[ wp_rand( 0, count( $taxonomies ) ) ],
					'hide_empty' => false,
					'number'     => 1,
					'order'      => 'RAND',
				)
			);

			$term_url = ! empty( $terms ) ? get_term_link( $terms[0] ) : '';

			?>
			<h3>All good!</h3>
			<p>Page assignment is successfully set up. This is the page which will be used for sermons taxonomy
				views.</p>
			<p>To try it out, just go to <a href="<?php echo esc_url( $term_url ?: '#' ); ?>">any sermons taxonomy page</a>, as
				usual.</p>
			<?php
			return ob_get_clean();
		}

		if ( defined( 'SMP_SHORTCODE_NO_TAXONOMY' ) ) {
			return __( 'Error, not in a taxonomy.', 'church-sermon-manager' );
		}

		if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
			define( 'SM_ENQUEUE_SCRIPTS_STYLES', true ); // phpcs:ignore
		}

		global $wp_query, $post;
		$original_query = $wp_query;
		$original_post  = $post;
		$wp_query       = $this->get_query_results();
		$post           = $wp_query->post;
		$attributes     = $this->get_attributes();
		$settings       = Settings::get_settings();

		$settings += array(
			'masonry_layout' => '',
		);

		ob_start();
		?>
		<div class="smpro-items smpro-items-container <?php echo 'yes' === $settings['masonry_layout'] ? 'smpro-masonry-layout grid js-masonry' : ''; ?>" <?php echo 'yes' === $settings['masonry_layout'] ? 'data-masonry="{ \'gutter\': 24 }"' : ''; ?>>
			<?php
			if ( $attributes['filtering'] ) {
				echo render_wpfc_sorting( $attributes['filtering_args'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtering UI markup composed by the plugin.
			}

			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					wpfc_sermon_excerpt_v2(); // You can edit the content of this function in `partials/content-sermon-archive.php`.
				endwhile;
			else :
				echo esc_html__( "Sorry, but there aren't any sermons matching your query.", 'church-sermon-manager' );
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
	 * Modifies the query.
	 *
	 * @param array $query_args The existing args.
	 *
	 * @return array Modified args.
	 */
	public function modify_query( array $query_args ) {
		global $smp_query_vars;

		// Get registered Sermon Manager taxonomies.
		$taxonomies = get_object_taxonomies( 'wpfc_sermon' );

		// Init variables.
		$taxonomy = null;
		$term     = null;

		// Find which taxonomy to render.
		foreach ( $smp_query_vars as $variable => $value ) {
			if ( in_array( $variable, $taxonomies ) ) {
				$taxonomy = $variable;
				$term     = $value;
				break;
			}
		}

		if ( $taxonomy && $term ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => $term,
				),
			);
		} else {
			define( 'SMP_SHORTCODE_NO_TAXONOMY', true );
		}

		return $query_args;
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
				'limit'              => '10', // Results limit.
				'columns'            => '', // Number of columns.
				'rows'               => '', // Number of rows. If defined, limit will be ignored.
				'orderby'            => 'date_preached', // menu_order, title, date, date_preached, rand.
				'order'              => 'DESC', // ASC or DESC.
				'class'              => '', // HTML class.
				'page'               => 1, // Page for pagination.
				'paginate'           => true, // Should results be paginated.
				'cache'              => true, // Should shortcode output be cached.
				'filtering'          => true, // Should we show the filtering.
				'hide_topics'        => '',
				'hide_series'        => '',
				'hide_preachers'     => '',
				'hide_books'         => '',
				'hide_dates'         => '',
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
			'hide_dates'            => $attributes['hide_dates'],
			'smp_override_settings' => true,
		);

		return $attributes;
	}
}
