<?php
global $post, $taxonomy, $term;

if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
	define( 'SM_ENQUEUE_SCRIPTS_STYLES', true );
}

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$posts_per_page = empty( $settings->sermons_per_page ) ? 9 : $settings->sermons_per_page;
$post_type      = 'wpfc_sermon';
$order_by       = empty( $settings->order_by ) ? 'date' : $settings->order_by;
$order          = empty( $settings->order ) ? 'DESC' : $settings->order;
$users          = empty( $settings->users ) ? '' : $settings->users;
$fields         = empty( $settings->fields ) ? '' : $settings->fields;

// Get the offset.
if ( ! isset( $settings->offset ) || ! is_int( (int) $settings->offset ) ) {
	$offset = 0;
} else {
	$offset = $settings->offset;
}

// Get the paged offset.
if ( $paged < 2 ) {
	$paged_offset = $offset;
} else {
	$paged_offset = $offset + ( ( $paged - 1 ) * $posts_per_page );
}

// Build the query args.
$args = array(
	'paged'               => $paged,
	'posts_per_page'      => $posts_per_page,
	'post_type'           => $post_type,
	'orderby'             => $order_by,
	'order'               => $order,
	'tax_query'           => array(
		'relation' => 'AND',
	),
	'ignore_sticky_posts' => true,
	'offset'              => $paged_offset,
	'fl_original_offset'  => $offset,
	'fl_builder_loop'     => true,
	'fields'              => $fields,
	'settings'            => $settings,
);

// Order by meta value arg.
if ( strstr( $order_by, 'meta_value' ) ) {
	$args['meta_key'] = $settings->order_by_meta_key;
}

// Order by author.
if ( 'author' == $order_by ) {
	$args['orderby'] = array(
		'author' => $order,
		'date'   => $order,
	);
}

$taxonomies = get_object_taxonomies( $post_type, 'objects' );

foreach ( $taxonomies as $tax_slug => $tax ) {

	$tax_value = '';
	$term_ids  = array();
	$operator  = 'IN';

	// Get the value of the suggest field.
	if ( isset( $settings->{'tax_' . $post_type . '_' . $tax_slug} ) ) {
		// New style slug.
		$tax_value = $settings->{'tax_' . $post_type . '_' . $tax_slug};
	} elseif ( isset( $settings->{'tax_' . $tax_slug} ) ) {
		// Old style slug for backwards compat.
		$tax_value = $settings->{'tax_' . $tax_slug};
	}

	// Get the term IDs array.
	if ( ! empty( $tax_value ) ) {
		$term_ids = explode( ',', $tax_value );
	}

	// Handle matching settings.
	if ( isset( $settings->{'tax_' . $post_type . '_' . $tax_slug . '_matching'} ) ) {

		$tax_matching = $settings->{'tax_' . $post_type . '_' . $tax_slug . '_matching'};

		if ( ! $tax_matching ) {
			// Do not match these terms.
			$operator = 'NOT IN';
		} elseif ( 'related' === $tax_matching ) {
			// Match posts by related terms from the global post.
			global $post;
			$terms   = wp_get_post_terms( $post->ID, $tax_slug );
			$related = array();
			foreach ( $terms as $term ) {
				if ( ! in_array( $term->term_id, $term_ids ) ) {
					$related[] = $term->term_id;
				}
			}

			if ( empty( $related ) ) {
				// If no related terms, match all except those in the suggest field.
				$operator = 'NOT IN';
			} else {

				// Don't include posts with terms selected in the suggest field.
				$args['tax_query'][] = array(
					'taxonomy' => $tax_slug,
					'field'    => 'id',
					'terms'    => $term_ids,
					'operator' => 'NOT IN',
				);

				// Set the term IDs to the related terms.
				$term_ids = $related;
			}
		}
	}

	if ( ! empty( $term_ids ) ) {

		$args['tax_query'][] = array(
			'taxonomy' => $tax_slug,
			'field'    => 'id',
			'terms'    => $term_ids,
			'operator' => $operator,
		);
	}
}

$args = smp_add_taxonomy_to_query( $args );

// Post in/not in query.
if ( isset( $settings->{'posts_' . $post_type} ) ) {

	$ids = $settings->{'posts_' . $post_type};
	$arg = 'post__in';

	// Set to NOT IN if matching is present and set to 0.
	if ( isset( $settings->{'posts_' . $post_type . '_matching'} ) ) {
		if ( ! $settings->{'posts_' . $post_type . '_matching'} ) {
			$arg = 'post__not_in';
		}
	}

	// Add the args if we have IDs.
	if ( ! empty( $ids ) ) {
		$args[ $arg ] = explode( ',', $settings->{'posts_' . $post_type} );
	}
}

$related_query = new WP_Query( apply_filters( 'smp/shortcodes/beaver/sermon_query', $args ) );

global $paged;
if ( empty( $paged ) ) {
	$paged = 1;
}

$count_posts      = wp_count_posts( 'wpfc_sermon' );
$published_posts  = $count_posts->publish;
$total_posts      = ceil( $published_posts / $settings->sermons_per_page );
$total_pagination = min( $total_posts, $settings->pagination_total_num );

$pagination_args = array(
	'base'         => get_pagenum_link( 1 ) . '%_%',
	'format'       => '?paged=%#%',
	'total'        => $total_pagination,
	'current'      => $paged,
	'show_all'     => true,
	'end_size'     => 1,
	'prev_text'    => $settings->previous_label,
	'next_text'    => $settings->next_label,
	'type'         => 'plain',
	'add_args'     => false,
	'add_fragment' => '',
);

if ( '1' == $settings->show_prev_next ) {
	$pagination_args['prev_next'] = true;
} else {
	$pagination_args['prev_next'] = false;
}

if ( 'columns' == $settings->layout ) {
	$article_class   = 'fl-sermon-columns fl-sermon-columns-' . $settings->sermon_columns;
	$container_class = 'fl-sermon-container fl-sermon-container-columns';
	if ( '1' == $settings->match_height ) {
		$article_class = 'fl-sermon-columns-equal ' . $article_class;
	} elseif ( '1' == $settings->show_masonry ) {
		$article_class   = 'fl-sermon-columns-masonry ' . $article_class;
		$container_class = 'fl-sermon-container-masonry ' . $container_class;
	}
} else {
	$article_class   = 'fl-sermon-list';
	$container_class = 'fl-sermon-container fl-sermon-container-list';
}

if ( '1' == $settings->show_filters ) {
	$content = render_wpfc_sorting(
		array(
			'hide_topics'        => '1' === $settings->show_filter_topics ? false : 'yes',
			'hide_series'        => '1' === $settings->show_filter_series ? false : 'yes',
			'hide_preachers'     => '1' === $settings->show_filter_preacher ? false : 'yes',
			'hide_books'         => '1' === $settings->show_filter_book ? false : 'yes',
			'hide_service_types' => '1' === $settings->show_filter_service_type ? false : 'yes',
			'classes'            => 'no-spacing',
		)
	);

	echo $content;
}
?>

<div class="<?php echo $container_class; ?>" <?php if ( '1' == $settings->show_masonry ) { echo ' data-masonry=\'{ "gutter": 24 }\' '; } ?> >

<?php
if ( $related_query->have_posts() ) {
	while ( $related_query->have_posts() ) :
		$related_query->the_post();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( $article_class ); ?>>
			<div class="wpfc-sermon-inner">
				<?php if ( 'none' != $settings->featured_type ) : ?>
					<?php if ( 'image' == $settings->featured_type ) : ?>
						<div class="wpfc-sermon-image">
							<a href="<?php the_permalink(); ?>">
								<img src="<?php echo get_sermon_image_url( true ); ?>" alt="<?php the_title(); ?>">
								<div class="wpfc-sermon-image-img" <?php echo 'style="background-image: url(' . get_sermon_image_url( true ) . ')"'; ?>>
								</div>
							</a>
						</div>
					<?php endif; ?>
					<?php if ( 'video' == $settings->featured_type ) : ?>
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
							<?php if ( ( '1' == $settings->show_series ) && has_term( '', 'wpfc_sermon_series', get_the_ID() ) ) : ?>
								<div class="wpfc-sermon-meta-item wpfc-sermon-meta-series">
									<?php the_terms( get_the_ID(), 'wpfc_sermon_series' ); ?>
								</div>
							<?php endif; ?>
							<?php if ( '1' == $settings->show_title ) : ?>
								<h3 class="wpfc-sermon-title">
									<a class="wpfc-sermon-title-text"
											href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</h3>
							<?php endif; ?>
							<?php if ( '1' == $settings->show_date ) : ?>
								<div class="wpfc-sermon-meta-item wpfc-sermon-meta-date">
									<?php if (\SermonManager::getOption( 'use_published_date' ) ) : ?>
										<?php the_date( $settings->date_format ); ?>
									<?php else : ?>
										<?php echo \SM_Dates::get( $settings->date_format ); ?>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="wpfc-sermon-description">
						<?php if ( '1' == $settings->show_description ) : ?>
							<div class="sermon-description">
								<?php if ( has_excerpt() ) : ?>
									<?php echo get_the_excerpt(); ?>
								<?php else : ?>
									<?php echo wp_trim_words( get_wpfc_sermon_meta( 'sermon_description' ), $settings->description_length ); ?>
								<?php endif; ?>
							</div>
							<?php if ( ( ( str_word_count( get_wpfc_sermon_meta( 'sermon_description' ) ) > $settings->description_length ) ) && ( '1' == $settings->show_more_link ) ) : ?>
								<div class="wpfc-sermon-description-read-more">
									<a href="<?php echo get_permalink(); ?>"><?php echo $settings->more_link_text; ?></a>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<?php if ( ( '1' == $settings->show_audio ) && ( get_wpfc_sermon_meta( 'sermon_audio' ) || get_wpfc_sermon_meta( 'sermon_audio_id' ) ) ) : ?>
						<?php
						$sermon_audio_id     = get_wpfc_sermon_meta( 'sermon_audio_id' );
						$sermon_audio_url_wp = $sermon_audio_id ? wp_get_attachment_url( intval( $sermon_audio_id ) ) : false;
						$sermon_audio_url    = $sermon_audio_id && $sermon_audio_url_wp ? $sermon_audio_url_wp : get_wpfc_sermon_meta( 'sermon_audio' );
						?>
						<div class="wpfc-sermon-audio">
							<?php echo wpfc_render_audio( $sermon_audio_url ); ?>
						</div>
						<?php if ( '1' == $settings->show_download_audio ) : ?>
							<div class="smpro-meta-item smpro-meta-item_type_sermon-audio">
								<a class="smpro-meta-item__text smpro-meta-item__text_linked wpfc-sermon-att-audio dashicons dashicons-media-audio"
										href="#" download="<?php echo $sermon_audio_url; ?>" title="Audio"></a>
							</div>
						<?php endif; ?>
					<?php endif; ?>
					<?php if ( ( ( '1' == $settings->show_preacher ) && ( has_term( '', 'wpfc_preacher', get_the_ID() ) ) ) || ( ( '1' == $settings->show_passage ) && ( get_wpfc_sermon_meta( 'bible_passage' ) ) ) || ( ( '1' == $settings->show_service_type ) && ( has_term( '', 'wpfc_service_type', get_the_ID() ) ) ) ) : ?>
						<div class="wpfc-sermon-footer">
							<?php if ( ( '1' == $settings->show_preacher ) && ( has_term( '', 'wpfc_preacher', get_the_ID() ) ) ) : ?>
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
									<span class="wpfc-sermon-meta-prefix"><?php echo __( 'Preacher', 'church-sermon-manager' ); ?>
										:</span>
									<span class="wpfc-sermon-meta-text"><?php the_terms( get_the_ID(), 'wpfc_preacher' ); ?></span>
								</div>
							<?php endif; ?>
							<?php if ( ( '1' == $settings->show_passage ) && ( get_wpfc_sermon_meta( 'bible_passage' ) ) ) : ?>
								<div class="wpfc-sermon-meta-item wpfc-sermon-meta-passage">
											<span class="wpfc-sermon-meta-prefix"><?php echo __( 'Passage', 'church-sermon-manager' ); ?>
												:</span>
									<span class="wpfc-sermon-meta-text"><?php wpfc_sermon_meta( 'bible_passage' ); ?></span>
								</div>
							<?php endif; ?>
							<?php if ( ( '1' == $settings->show_service_type ) && ( has_term( '', 'wpfc_service_type', get_the_ID() ) ) ) : ?>
								<div class="wpfc-sermon-meta-item wpfc-sermon-meta-service">
											<span class="wpfc-sermon-meta-prefix"><?php echo __( 'Service Type', 'church-sermon-manager' ); ?>
												:</span>
									<span class="wpfc-sermon-meta-text"><?php the_terms( get_the_ID(), 'wpfc_service_type' ); ?></span>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</article>
	<?php endwhile; ?>

	</div>

	<?php
	$paginate_links = paginate_links( $pagination_args );
	if ( ( $paginate_links ) && ( '1' == $settings->show_pagination ) ) {
		?>
		<nav class='custom-pagination'>
			<p><?php echo $paginate_links; ?></p>
		</nav>
		<?php
	}
}

wp_reset_query();
