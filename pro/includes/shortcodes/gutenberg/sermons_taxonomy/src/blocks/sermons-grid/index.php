<?php
function sermons_taxonomy_layout_render_sermons_grid( $attributes ){
		
	ob_start();

	$url          = $_SERVER['REQUEST_URI'];
	$segments     = explode( '/', $url );
	$page         = is_numeric( $segments[ count( $segments ) - 2 ] ) ? $segments[ count( $segments ) - 2 ] : 1;
	$next         = $page + 1;
	$prev         = $page - 1;
	$terms_number = min( $attributes['postscount'], wp_count_terms( $attributes['showTaxonomy'] ) );
	$lastpage     = min( ceil( wp_count_terms( $attributes['showTaxonomy'] ) / $terms_number ), $attributes['paginationTotalPages'] );

	$terms = get_terms( array(
		'taxonomy'   => $attributes['showTaxonomy'],
		'hide_empty' => false,
		'offset'     => ( $page - 1 ) * $terms_number,
		'number'     => $terms_number,
	) );

	$column_terms = round( intval( $terms_number ) / intval( $attributes['columns'] ) );

	$full_column = $terms_number % $attributes['columns'];

	if ( 0 != $full_column ) {
		$column_terms ++;
	}

	$first_letter = '';
	$col          = 1;
	$i            = 1;
	?>

	<div class="wpfc-term-content" <?php echo 'style="--item-padding-left-right : '.$attributes['columnGap'].'px; --item-margin-bottom : '.($attributes['columnGap']*2).'px; --item-height : '.(300-$attributes['columnGap']).'px"' ;  ?>>
		<?php echo $attributes['postLayout'] === 'grid' ? '<div class="wp-block-sermons-taxonomy-layout-sermons-grid sermons-grid-view gpl-d-flex gpl-flex-wrap sermon_skin wpfc-term-grid">' 
		: '<div class="wp-block-sermons-taxonomy-layout-sermons-grid sermons-grid-view gpl-d-flex gpl-flex-wrap sermon_skin wpfc-term-list">'; ?>
		<?php foreach ( $terms as $term ) : ?>
			<?php
			if ( $attributes['postLayout'] === 'list' && 1 == $i ) {
				echo '<div class="wpfc-term post-item wpfc-sermon gpl-mb-30 gpl-column-' . $attributes['columns'] . ' term-list sermon_skin">';
			}

			if ( $attributes['displayAlphabeticalList'] && $attributes['postLayout'] === 'list' ) {
				if ( $first_letter != $term->name[0] || 1 == $i ) {
					$first_letter = $term->name[0];
					echo '<div class="wpfc-term-first-letter ' . $attributes['align'] . '" style="padding-bottom:' . $attributes['letterBottomPadding'] . 'px;padding-top:' . $attributes['letterTopPadding'] . 'px;">' . $first_letter . '</div>';
				}
			}
			?>

			<?php echo $attributes['postLayout'] === 'grid' ? '<div class="wpfc-term post-item wpfc-sermon gpl-mb-30 gpl-column-' . $attributes['columns'] . ' sermon_skin">' : ''; ?>

			<?php
			if ( $attributes['postLayout'] === 'grid' && $attributes['displayTermImage'] ) {
				$associations = get_option( 'sermon_image_plugin' );

				if ( ! empty( $associations[ $term->term_id ] ) ) {
					$image_id = (int) $associations[ $term->term_id ];
				} else {
					$image_id = null;
				}

				$css_margin = $attributes['termImagePadding'] . 'px';

				if ( $image_id ) {
					/* @noinspection CssUnknownTarget */
					echo sprintf(
						'<a href="' . get_term_link( $term, $attributes['showTaxonomy'] ) . '" class="wpfc-term-grid-image" style="background-image:url(%s);margin-bottom:' . $css_margin . ';"></a>',
						wp_get_attachment_image_url( $image_id, array( 300, 300 ) )
					);
				} else {
					echo sprintf( '<a href="' . get_term_link( $term, $attributes['showTaxonomy'] ) . '" class="wpfc-term-grid-image" style="background-color:#cecece;margin-bottom:' . $css_margin . ';"></a>' );
				}
			}
			?>

			<div class="wpfc-term-inner <?php echo $attributes['align'];?>"
				<?php
				if ( $attributes['postLayout'] === 'grid' && $attributes['displayTermDescription'] ) {
					echo 'style="padding:' . $attributes['termDescriptionPadding'] . 'px;"';
				}
				?>
			>

				<?php
				if ( ( $attributes['postLayout'] === 'grid' && $attributes['displayTermTitle'] ) or ( ( $attributes['postLayout'] === 'list' ) ) ) {
					?>
					<a href="<?php echo get_term_link( $term, $attributes['showTaxonomy'] ); ?>"
							class="wpfc-term-title" <?php if ( $attributes['postLayout'] === 'grid' ) { echo 'style="padding-bottom:' . $attributes['termTitlePadding'] . 'px;"'; } ?>><?php echo $term->name; ?></a>
					<?php } ?>

				<?php
				if ( $attributes['postLayout'] === 'grid' && $attributes['displayTermDescription'] ) {
					?>
					<div class="wpfc-term-description"><?php echo wp_trim_words( $term->description, 25, '...' ); ?></div>
						
					<?php if ( ( str_word_count( $term->description ) > 0  ) && $attributes['displayTermReadMoreButton'] ) : ?>
					<div class="wpfc-term-description-read-more">
						<a href="<?php echo get_permalink(); ?>"><?php echo $attributes['termReadMoreButtonText']; ?></a>
					</div>
				<?php endif; ?>
						
				<?php } ?>

			</div>

			<?php echo $attributes['postLayout'] === 'grid' ? '</div>' : ''; ?>

			<?php
			if ( $attributes['postLayout'] === 'list' ) {
				if ( $i == $column_terms ) {
					echo '</div>';
					$i = 1;
					if ( $col == $full_column ) {
						$column_terms --;
					}
					$col ++;
				} else {
					$i ++;
				}
			}
			?>

		<?php endforeach; ?>
		<?php echo $attributes['postLayout'] === 'grid' ? '</div>' : '</div>'; ?>
		
		<?php
	if ( ( $attributes['displayPagination'] ) && ( 1 != $lastpage ) ) { ?>
		<div class="wpfc-term-pagination" style="text-align:<?php echo $attributes['paginationAlignment']; ?>;">
			<?php
				if ( ( $prev > 0 ) && ( $attributes['displayPrevNext'] ) ) {
					?>
					<a href="?page=<?php echo $prev; ?>"><?php echo $attributes['previousLabel']; ?></a>
					<?php
				}

				for ( $i = 1; $i <= $lastpage; $i ++ ) {

					if ( $page == $i ) {
						?>
						<span><?php echo $i; ?></span>
						<?php
					} else {
						?>
						<a href="?page=<?php echo $i; ?>" class="page-numbers"><?php echo $i; ?></a>
						<?php
					}
				}

				if ( ( $page < $lastpage ) && ( $attributes['displayPrevNext'] ) ) {
					?>
					<a href="?page=<?php echo $next; ?>"><?php echo $attributes['nextLabel']; ?></a>
					<?php
				} ?>
		</div>
		
	</div>

	<?php
	}

	$posts = ob_get_contents();

	ob_end_clean();

	$output = sprintf(
		'<div>
			%1$s
		</div> <!-- .et_pb_posts -->', $posts
	);

	return $output;
}

function sermons_taxonomy_layout_register_sermons_grid(){
	
	if( !function_exists('register_block_type') ){
		return;
	}

	register_block_type( 'sermons-taxonomy-layout/sermons-grid', array(
		'attributes' => array(
			'align' => array(
				'type' => 'string',
				'default' => 'left',
			),
			'showTaxonomy' => array(
				'type' => 'string',
				'default' => 'wpfc_sermon_series'
			),
			'displayTermImage' => array(
				'type' => 'boolean',
				'default' => true
			),
			'displayAlphabeticalList' => array(
				'type' => 'boolean',
				'default' => true
			),
			'letterTopPadding' => array(
				'type' => 'number',
				'default' => 10,
			),
			'letterBottomPadding' => array(
				'type' => 'number',
				'default' => 5,
			),
			'termImagePadding' => array(
				'type' => 'number',
				'default' => 20,
			),
			'displayTermTitle' => array(
				'type' => 'boolean',
				'default' => true
			),
			'termTitlePadding' => array(
				'type' => 'number',
				'default' => 10,
			),
			'displayTermDescription' => array(
				'type' => 'boolean',
				'default' => true
			),
			'termDescriptionPadding' => array(
				'type' => 'number',
				'default' => 0,
			),
			'post_type' => array(
				'type' => 'string',
				'default' => 'wpfc_sermon'
			),
			'postscount' => array(
				'type' => 'number',
				'default' => 9,
			),
			'columns' => array(
				'type' => 'number',
				'default' => 3
			),
			'columnGap' => array(
				'type' => 'number',
				'default' => 15
			),
			'postLayout' => array(
				'type' => 'string',
				'default' => 'grid',
			),
			'gridLayoutStyle' => array(
				'type' => 'string',
				'default' => 'sermon_skin',
			),
			'displayTermReadMoreButton' => array(
				'type' => 'boolean',
				'default' => true,
			),
			'termReadMoreButtonText' => array(
				'type' => 'string',
				'default' => 'Read More',
			),
			'displayPagination' => array(
				'type' => 'boolean',
				'default' => true,
			),
			'paginationTotalPages' => array(
				'type' => 'number',
				'default' => 5,
			),
			'displayPrevNext' => array(
				'type' => 'boolean',
				'default' => true,
			),
			'previousLabel' => array(
				'type' => 'string',
				'default' => '« Previous',
			),
			'nextLabel' => array(
				'type' => 'string',
				'default' => 'Next »',
			),
			'paginationAlignment' => array(
				'type' => 'string',
				'default' => 'left',
			),
		),
		'render_callback' => 'sermons_taxonomy_layout_render_sermons_grid',
	));
	
	
}
add_action( 'init', 'sermons_taxonomy_layout_register_sermons_grid' );

/**
 * Create API fields for additional info
 */

function sermons_taxonomy_layout_register_rest_fields(  ) {
	$post_types = get_taxonomies();
	
		
	register_rest_field(
		$post_types,
		'sermons_taxonomy_image',
		array(
			'get_callback' => 'get_sermons_taxonomy_image',
			'update_callback' => null,
			'schema' => array(
				'description' => __( 'Sermon Tanonomy Image', 'sermons-taxonomy-layout'),
				'type' => 'array'
			)
		)
	);
	
	register_rest_field(
		$post_types,
		'sermons_taxonomy_description',
		array(
			'get_callback' => 'get_sermons_taxonomy_description',
			'update_callback' => null,
			'schema' => array(
				'description' => __( 'Sermon Taxonomy Description', 'sermons-taxonomy-layout'),
				'type' => 'array'
			)
		)
	);
		
}

add_action('rest_api_init', 'sermons_taxonomy_layout_register_rest_fields');


function get_sermons_taxonomy_image($object) {
	$associations = get_option( 'sermon_image_plugin' );

	if ( ! empty( $associations[ $object[ 'id' ] ] ) ) {
		$image_id = (int) $associations[ $object[ 'id' ] ];
	} else {
		$image_id = null;
	}

	if ( $image_id ) {
		/* @noinspection CssUnknownTarget */
		return wp_get_attachment_image_url( $image_id, array( 300, 300 ) );
	} else {
		return null;
	}
	
}

function get_sermons_taxonomy_description($object) {

	return wp_trim_words( $object[ 'description' ], 25, '...' );

}