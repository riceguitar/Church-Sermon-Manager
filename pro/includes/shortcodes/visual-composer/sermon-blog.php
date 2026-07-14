<?php
/**
 * Adds the Visual Composer element.
 *
 * @since   1.0.0-beta.3
 * @package SMP\Shortcodes
 */

namespace SMP\Shortcodes;

defined( 'ABSPATH' ) or exit;

/**
 * Main class.
 */
class VC_Blog {
	/**
	 * Main constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Registers the shortcode in WordPress.
		add_shortcode( 'sermon_blog', array( $this, 'render' ) );

		// Map shortcode to Visual Composer.
		if ( function_exists( 'vc_lean_map' ) ) {
			vc_lean_map( 'sermon_blog', array( $this, 'map' ) );
		}
	}

	/**
	 * Shortcode output
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string The output.
	 *
	 * @since 1.0.0
	 */
	public static function render( $atts = array() ) {
		global $paged;

		if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
			define( 'SM_ENQUEUE_SCRIPTS_STYLES', true ); // phpcs:ignore
		}

		// Set the default attributes.
		$args = array(
			'title'                    => __( 'Sermons', 'sermon-manager' ),
			'show_grid'                => true,
			'grid_columns'             => 3,
			'spacing_columns'          => 15,
			'show_filters'             => true,
			'show_filter_preacher'     => true,
			'show_filter_series'       => true,
			'show_filter_book'         => true,
			'show_filter_service_type' => true,
			'show_filter_topics'       => true,
			'show_filter_date'         => true,
			'sermons_number'           => 9,
			'sermons_order'            => 'DESC',
			'show_image'               => true,
			'featured_type'            => 'image',
			'show_series'              => true,
			'show_title'               => true,
			'title_padding'            => 0,
			'show_date'                => true,
			'date_format'              => null,
			'show_excerpt'             => true,
			'excerpt_length'           => 30,
			'show_readmore'            => true,
			'read_more_text'           => __( 'Read More', 'sermon-manager' ),
			'description_padding'      => 0,
			'show_sermon_audio'        => true,
			'show_preacher'            => true,
			'show_passage'             => true,
			'show_service_type'        => true,
			'show_pagination'          => true,
			'pagination_total_num'     => 5,
			'show_prev_next'           => true,
			'previous_label'           => '&laquo; Previous',
			'next_label'               => 'Next &raquo;',
			'pagination_alignment'     => 'left',
			'el_id'                    => '',
			'el_class'                 => '',
			'css'                      => '',
		);

		// Get the saved attributes and merge with default.
		$args = vc_map_get_attributes( 'sermon_blog', $atts ) + $args;

		// Fix some attributes if they have invalid value.
		$args['spacing_columns'] = intval( $args['spacing_columns'] );
		$args['spacing_columns'] = $args['spacing_columns'] > 0 ? $args['spacing_columns'] : 15;
		$args['grid_columns']    = intval( $args['grid_columns'] );
		$args['grid_columns']    = $args['grid_columns'] > 0 ? $args['grid_columns'] : 3;

		// Get the current page.
		if ( empty( $paged ) || ! $paged ) {
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		}

		// Add the taxonomy filtering.
		$query_args = smp_add_taxonomy_to_query(
			array(
				'post_type'      => 'wpfc_sermon',
				'posts_per_page' => $args['sermons_number'],
				'paged'          => $paged,
				'order'          => $args['sermons_order'],
				'orderby'        => 'meta_value_num',
				'meta_compare'   => '<=',
				'meta_value'     => time(),
				'meta_key'       => 'sermon_date',
			)
		);

		// Start collecting output.
		ob_start();

		// Do the query.
		$the_query = new \WP_Query( apply_filters( 'smp/shortcodes/visual-composer/sermon_query', $query_args ) ); // phpcs:ignore

		// Render filtering.
		if ( $args['show_filters'] ) {
			echo render_wpfc_sorting(
				array(
					'hide_topics'        => $args['show_filter_topics'] ? false : 'yes',
					'hide_series'        => $args['show_filter_series'] ? false : 'yes',
					'hide_preachers'     => $args['show_filter_preacher'] ? false : 'yes',
					'hide_books'         => $args['show_filter_book'] ? false : 'yes',
					'hide_service_types' => $args['show_filter_service_type'] ? false : 'yes',
					'hide_dates'         => $args['show_filter_dates'] ? false : 'yes',
					'classes'            => 'no-spacing',
				)
			);
		}

		// Get the additional sermon class.
		$post_classes = 'on' === $args['show_grid'] ? 'wpfc-sermon sermon_grid_column sermon_grid_column_' . $args['grid_columns'] : 'wpfc-sermon';

		echo 'on' === $args['show_grid'] ? '<div class="sm-inner-grid">' : '';

		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( $post_classes ); ?>
					<?php echo 'on' === $args['show_grid'] ? 'style="margin-right:' . $args['spacing_columns'] . 'px;width: calc((100% - ' . $args['spacing_columns'] * ( $args['grid_columns'] - 1 ) . 'px) / ' . $args['grid_columns'] . ');"' : ''; ?>>
					<div class="wpfc-sermon-inner">
						<?php if ( $args['show_image'] ) : ?>
							<?php if ( 'image' === $args['featured_type'] ) : ?>
								<div class="wpfc-sermon-image">
									<a href="<?php the_permalink(); ?>">
										<div class="wpfc-sermon-image-img"
												style="background-image: url(<?php echo get_sermon_image_url( true ); ?>)"></div>
									</a>
								</div>
							<?php endif; ?>
							<?php if ( 'video' === $args['featured_type'] ) : ?>
								<?php if ( get_wpfc_sermon_meta( 'sermon_video_link' ) ) : ?>
									<div class="wpfc-sermon-video wpfc-sermon-video-link">
										<?php echo wpfc_render_video( get_wpfc_sermon_meta( 'sermon_video_link' ) ); ?>
									</div>
								<?php endif; ?>
								<?php if ( get_wpfc_sermon_meta( 'sermon_video' ) ) : ?>
									<div class="wpfc-sermon-video wpfc-sermon-video-embed">
										<?php echo do_shortcode( get_wpfc_sermon_meta( 'sermon_video' ) ); ?>
									</div>
								<?php endif; ?>
							<?php endif; ?>
						<?php endif; ?>
						<div class="wpfc-sermon-main <?php echo get_sermon_image_url() ? '' : 'no-image'; ?>">
							<div class="wpfc-sermon-header">
								<div class="wpfc-sermon-header-main">
									<?php if ( has_term( '', 'wpfc_sermon_series', get_the_ID() ) && $args['show_series'] ) : ?>
										<div class="wpfc-sermon-meta-item wpfc-sermon-meta-series">
											<?php the_terms( get_the_ID(), 'wpfc_sermon_series' ); ?>
										</div>
									<?php endif; ?>
									<?php if ( $args['show_title'] ) : ?>
										<h3 class="wpfc-sermon-title" <?php echo 'style="padding-bottom:' . $args['title_padding'] . 'px;"'; ?>>
											<a class="wpfc-sermon-title-text"
													href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
										</h3>
									<?php endif; ?>
									<?php if ( $args['show_date'] ) : ?>
										<div class="wpfc-sermon-meta-item wpfc-sermon-meta-date">
											<?php if ( \SermonManager::getOption( 'use_published_date' ) ) : ?>
												<?php the_date( $args['date_format'] ); ?>
											<?php else : ?>
												<?php echo \SM_Dates::get( $args['date_format'] ); ?>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
							<div class="wpfc-sermon-description">
								<?php if ( $args['show_excerpt'] ) : ?>
									<div class="sermon-description-content" <?php echo 'style="padding-bottom:' . $args['description_padding'] . 'px;"'; ?>>
										<?php if ( has_excerpt() ) : ?>
											<?php echo get_the_excerpt(); ?>
										<?php else : ?>
											<?php echo wp_trim_words( get_wpfc_sermon_meta( 'sermon_description' ), $args['excerpt_length'] ); ?>
										<?php endif; ?>
									</div>
									<?php if ( ( str_word_count( get_wpfc_sermon_meta( 'sermon_description' ) ) > $args['excerpt_length'] ) && $args['show_readmore'] ) : ?>
										<div class="wpfc-sermon-description-read-more">
											<a href="<?php echo get_permalink(); ?>"><?php echo $args['read_more_text']; ?></a>
										</div>
									<?php endif; ?>
								<?php endif; ?>
							</div>
							<?php if ( $args['show_sermon_audio'] && ( get_wpfc_sermon_meta( 'sermon_audio' ) || get_wpfc_sermon_meta( 'sermon_audio_id' ) ) ) : ?>
								<?php
								$sermon_audio_id     = get_wpfc_sermon_meta( 'sermon_audio_id' );
								$sermon_audio_url_wp = $sermon_audio_id ? wp_get_attachment_url( intval( $sermon_audio_id ) ) : false;
								$sermon_audio_url    = $sermon_audio_id && $sermon_audio_url_wp ? $sermon_audio_url_wp : get_wpfc_sermon_meta( 'sermon_audio' );
								?>
								<div class="wpfc-sermon-audio">
									<?php echo wpfc_render_audio( $sermon_audio_url ); ?>
								</div>
							<?php endif; ?>
							<?php if ( ( $args['show_preacher'] && ( has_term( '', 'wpfc_preacher', get_the_ID() ) ) ) || ( $args['show_passage'] && ( get_wpfc_sermon_meta( 'bible_passage' ) ) ) || ( $args['show_service_type'] && ( has_term( '', 'wpfc_service_type', get_the_ID() ) ) ) ) : ?>
								<div class="wpfc-sermon-footer">
									<?php if ( $args['show_preacher'] && ( has_term( '', 'wpfc_preacher', get_the_ID() ) ) ) : ?>
										<div class="wpfc-sermon-meta-item wpfc-sermon-meta-preacher">
											<?php
											echo apply_filters( 'sermon-images-list-the-terms', '', // phpcs:ignore
												array(
													'taxonomy'     => 'wpfc_preacher', // phpcs:ignore
													'after'        => '', // phpcs:ignore
													'after_image'  => '', // phpcs:ignore
													'before'       => '', // phpcs:ignore
													'before_image' => '', // phpcs:ignore
												)
											);
											?>
											<span class="wpfc-sermon-meta-prefix"><?php echo __( 'Preacher', 'sermon-manager' ); ?>
													:</span>
											<span class="wpfc-sermon-meta-text"><?php the_terms( get_the_ID(), 'wpfc_preacher' ); ?></span>
										</div>
									<?php endif; ?>
									<?php if ( $args['show_passage'] && ( get_wpfc_sermon_meta( 'bible_passage' ) ) ) : ?>
										<div class="wpfc-sermon-meta-item wpfc-sermon-meta-passage">
												<span class="wpfc-sermon-meta-prefix"><?php echo __( 'Passage', 'sermon-manager' ); ?>
													:</span>
											<span class="wpfc-sermon-meta-text"><?php wpfc_sermon_meta( 'bible_passage' ); ?></span>
										</div>
									<?php endif; ?>
									<?php if ( $args['show_service_type'] && ( has_term( '', 'wpfc_service_type', get_the_ID() ) ) ) : ?>
										<div class="wpfc-sermon-meta-item wpfc-sermon-meta-service">
												<span class="wpfc-sermon-meta-prefix"><?php echo __( 'Service Type', 'sermon-manager' ); ?>
													:</span>
											<span class="wpfc-sermon-meta-text"><?php the_terms( get_the_ID(), 'wpfc_service_type' ); ?></span>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</article>

				<?php
			}

			echo 'on' === $args['show_grid'] ? '</div>' : '';

			// Define pagination arguments.
			$pagination_args = array(
				'base'         => get_pagenum_link( 1 ) . '%_%',
				'format'       => '?paged=%#%',
				'total'        => $the_query->max_num_pages,
				'current'      => $paged,
				'show_all'     => true,
				'end_size'     => 1,
				'prev_text'    => $args['previous_label'],
				'next_text'    => $args['next_label'],
				'type'         => 'plain',
				'add_args'     => false,
				'add_fragment' => '',
				'prev_next'    => $args['show_prev_next'] ? true : false,
			);

			// Get the pagination.
			$paginate_links = paginate_links( $pagination_args );
			if ( ( $paginate_links ) && $args['show_pagination'] ) {
				?>
				<nav class="custom-pagination" style="text-align:<?php echo $args['pagination_alignment']; ?>;">
					<p><?php echo $paginate_links; ?></p>
				</nav>
				<?php
			}
		}

		wp_reset_query();

		// Get the output.
		$output = ob_get_clean();

		// Format the output.
		$output = sprintf(
			'<div class="wpb_sermons wpb_content_element %1$s%2$s" id="%3$s">
					%4$s
				</div>',
			$args['el_class'],
			apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $args['css'], ' ' ), 'sermon_blog', $atts ), // phpcs:ignore
			$args['el_id'],
			$output
		);

		return $output;
	}

	/**
	 * Map shortcode to VC.
	 *
	 * This is an array of all your settings which become the shortcode attributes ($atts)
	 * for the output.
	 *
	 * @return array The parameters.
	 */
	public static function map() {
		/* @noinspection HtmlUnknownTarget */
		return array(
			'name'        => __( 'Sermons', 'sermon-manager' ),
			'description' => __( 'Display Sermons', 'sermon-manager' ),
			'base'        => 'sermon_blog',
			'icon'        => 'icon-wpb-ui-accordion',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Widget title', 'sermon-manager' ),
					'param_name'  => 'title',
					'description' => __( 'The widget title. Leave blank to use the default widget title.', 'sermon-manager' ),
					'value'       => __( 'Sermons', 'sermon-manager' ),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Layout', 'sermon-manager' ),
					'param_name'  => 'show_grid',
					'value'       => array(
						__( 'Fullwidth', 'sermon-manager' ) => 'off',
						__( 'Grid', 'sermon-manager' )      => 'on', // phpcs:ignore
					),
					'description' => __( 'Toggle between the different sermons layout types.', 'sermon-manager' ),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Grid Columns', 'sermon-manager' ),
					'param_name'  => 'grid_columns',
					'value'       => array(
						1 => 1,
						2 => 2,
						3 => 3,
						4 => 4,
						5 => 5,
						6 => 6,
					),
					'description' => __( 'How many columns to display.', 'sermon-manager' ),
					'std'         => 3,
					'dependency'  => array(
						'element' => 'show_grid',
						'value'   => 'on',

					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Spacing Between Columns', 'sermon-manager' ),
					'param_name' => 'spacing_columns',
					'value'      => array(
						__( 'None', 'sermon-manager' ) => 0,
						'1px'                              => 1,
						'2px'                              => 2,
						'3px'                              => 3,
						'4px'                              => 4,
						'5px'                              => 5,
						'10px'                             => 10,
						'15px'                             => 15,
						'20px'                             => 20,
						'25px'                             => 25,
						'30px'                             => 30,
						'35px'                             => 35,
					),
					'std'        => 15,
					'dependency' => array(
						'element' => 'show_grid',
						'value'   => 'on',

					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Filters', 'sermon-manager' ),
					'param_name' => 'show_filters',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Filter Preacher', 'sermon-manager' ),
					'param_name' => 'show_filter_preacher',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
					'dependency' => array(
						'element'   => 'show_filters',
						'not_empty' => true,

					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Filter Series', 'sermon-manager' ),
					'param_name' => 'show_filter_series',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
					'dependency' => array(
						'element'   => 'show_filters',
						'not_empty' => true,

					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Filter Book', 'sermon-manager' ),
					'param_name' => 'show_filter_book',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
					'dependency' => array(
						'element'   => 'show_filters',
						'not_empty' => true,

					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Filter Service Type', 'sermon-manager' ),
					'param_name' => 'show_filter_service_type',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
					'dependency' => array(
						'element'   => 'show_filters',
						'not_empty' => true,

					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Filter Topics', 'sermon-manager' ),
					'param_name' => 'show_filter_topics',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
					'dependency' => array(
						'element'   => 'show_filters',
						'not_empty' => true,

					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Filter Dates', 'sermon-manager' ),
					'param_name' => 'show_filter_dates',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
					'dependency' => array(
						'element'   => 'show_filters',
						'not_empty' => true,

					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Number of sermons', 'sermon-manager' ),
					'description' => __( 'Enter the number of sermons to display.', 'sermon-manager' ),
					'param_name'  => 'sermons_number',
					'value'       => 9,
					'admin_label' => true,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Sermons Order', 'sermon-manager' ),
					'param_name' => 'sermons_order',
					'value'      => array(
						esc_html__( 'Descending', 'sermon-manager' ) => 'DESC',
						esc_html__( 'Ascending', 'sermon-manager' )  => 'ASC',
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Featured Image/Video', 'sermon-manager' ),
					'param_name' => 'show_image',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Featured Type', 'sermon-manager' ),
					'param_name'  => 'featured_type',
					'value'       => array(
						esc_html__( 'Image', 'sermon-manager' ) => 'image',
						esc_html__( 'Video', 'sermon-manager' ) => 'video',
					),
					'description' => __( 'Toggle between the featured types.', 'sermon-manager' ),
					'dependency'  => array(
						'element'   => 'show_image',
						'not_empty' => true,

					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Series', 'sermon-manager' ),
					'param_name' => 'show_series',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Title', 'sermon-manager' ),
					'param_name' => 'show_title',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Title Padding', 'sermon-manager' ),
					'param_name' => 'title_padding',
					'value'      => array(
						__( 'None', 'sermon-manager' ) => 0,
						'1px'                              => 1,
						'2px'                              => 2,
						'3px'                              => 3,
						'4px'                              => 4,
						'5px'                              => 5,
						'10px'                             => 10,
						'15px'                             => 15,
						'20px'                             => 20,
						'25px'                             => 25,
						'30px'                             => 30,
						'35px'                             => 35,
					),
					'dependency' => array(
						'element'   => 'show_title',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Date', 'sermon-manager' ),
					'param_name' => 'show_date',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Date Format', 'sermon-manager' ),
					'param_name' => 'date_format',
					'value'      => array(
						__( 'Default', 'sermon-manager' ) => 0,

						date( 'M j, Y' ) => 'M j, Y',
						date( 'F j, Y' ) => 'F j, Y',
						date( 'm/d/Y' )  => 'm/d/Y',
						date( 'm-d-Y' )  => 'm-d-Y',
						date( 'd M Y' )  => 'd M Y',
						date( 'd F Y' )  => 'd F Y',
						date( 'Y-m-d' )  => 'Y-m-d',
						date( 'Y/m/d' )  => 'Y/m/d',
					),
					'dependency' => array(
						'element'   => 'show_date',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Excerpt', 'sermon-manager' ),
					'param_name' => 'show_excerpt',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Excerpt Length', 'sermon-manager' ),
					'param_name' => 'excerpt_length',
					'value'      => 30,
					'dependency' => array(
						'element'   => 'show_excerpt',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Read More Button', 'sermon-manager' ),
					'param_name' => 'show_readmore',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
					'dependency' => array(
						'element'   => 'show_excerpt',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Read More Text', 'sermon-manager' ),
					'param_name' => 'read_more_text',
					'value'      => 'Read More',
					'dependency' => array(
						'element'   => 'show_readmore',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Description Padding', 'sermon-manager' ),
					'param_name' => 'description_padding',
					'value'      => array(
						__( 'None', 'sermon-manager' ) => 0,
						'1px'                              => 1,
						'2px'                              => 2,
						'3px'                              => 3,
						'4px'                              => 4,
						'5px'                              => 5,
						'10px'                             => 10,
						'15px'                             => 15,
						'20px'                             => 20,
						'25px'                             => 25,
						'30px'                             => 30,
						'35px'                             => 35,
					),
					'dependency' => array(
						'element'   => 'show_excerpt',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Sermon Audio', 'sermon-manager' ),
					'param_name' => 'show_sermon_audio',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Preacher', 'sermon-manager' ),
					'param_name' => 'show_preacher',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Passage', 'sermon-manager' ),
					'param_name' => 'show_passage',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Service Type', 'sermon-manager' ),
					'param_name' => 'show_service_type',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Pagination', 'sermon-manager' ),
					'param_name' => 'show_pagination',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Page Limit', 'sermon-manager' ),
					'param_name' => 'pagination_total_num',
					'value'      => 5,
					'dependency' => array(
						'element'   => 'show_pagination',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Prev/Next Links', 'sermon-manager' ),
					'param_name' => 'show_prev_next',
					'value'      => array( __( 'Yes', 'sermon-manager' ) => true ),
					'std'        => 'value',
					'dependency' => array(
						'element'   => 'show_pagination',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Previous Label', 'sermon-manager' ),
					'param_name' => 'previous_label',
					'value'      => '&laquo; Previous',
					'dependency' => array(
						'element'   => 'show_prev_next',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Next Label', 'sermon-manager' ),
					'param_name' => 'next_label',
					'value'      => 'Next &raquo;',
					'dependency' => array(
						'element'   => 'show_prev_next',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => esc_html__( 'Pagination Alignment', 'sermon-manager' ),
					'param_name' => 'pagination_alignment',
					'value'      => array(
						__( 'Left', 'sermon-manager' )   => 'left', // phpcs:ignore
						__( 'Center', 'sermon-manager' ) => 'center',
						__( 'Right', 'sermon-manager' )  => 'right', // phpcs:ignore
					),
					'dependency' => array(
						'element'   => 'show_pagination',
						'not_empty' => true,
					),
				),
				array(
					'type'        => 'el_id',
					'heading'     => __( 'Custom HTML element ID', 'sermon-manager' ),
					'param_name'  => 'el_id',
					// translators: %s: The link to the W3C spec.
					'description' => sprintf( __( 'Enter element ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">W3C specification</a>).', 'sermon-manager' ), 'http://www.w3schools.com/tags/att_global_id.asp' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Custom CSS class', 'sermon-manager' ),
					'param_name'  => 'el_class',
					'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'sermon-manager' ),
				),
				array(
					'type'       => 'css_editor',
					'heading'    => __( 'Custom CSS', 'sermon-manager' ),
					'param_name' => 'css',
					'group'      => __( 'Design options', 'sermon-manager' ),
				),
			),
		);
	}

}

new VC_Blog();
