<?php

ob_start();

if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
	define( 'SM_ENQUEUE_SCRIPTS_STYLES', true );
}

$url        = $_SERVER['REQUEST_URI'];
$segments   = explode( '/', $url );
$page       = is_numeric( $segments[ count( $segments ) - 2 ] ) ? $segments[ count( $segments ) - 2 ] : 1;
$next       = $page + 1;
$prev       = $page - 1;
$term_count = wp_count_terms( $settings->show_taxonomy );

$terms_number = min( $settings->taxonomy_number, $term_count ) ?: 1;

$lastpage = ceil( $term_count / $terms_number );

$terms = get_terms( array(
	'taxonomy'   => $settings->show_taxonomy,
	'hide_empty' => false,
	'offset'     => ( $page - 1 ) * $terms_number,
	'number'     => $terms_number,
) );

$column_terms = round( intval( $terms_number ) / intval( $settings->term_columns ) );

$full_column = $terms_number % $settings->term_columns;

if ( 0 != $full_column ) {
	$column_terms ++;
}

$first_letter = '';
$col          = 1;
$i            = 1;

if ( 'grid' == $settings->taxonomy_layout ) {
	$article_class   = 'fl-term-grid fl-term-column-' . $settings->term_columns;
	$container_class = 'fl-term-container fl-term-container-grid';
} else {
	$article_class   = 'fl-term-list fl-term-column-' . $settings->term_columns;
	$container_class = 'fl-term-container fl-term-container-list';
}
?>

<div class="wpfc-term-container <?php echo $container_class; ?>">

	<?php foreach ( $terms as $term ) :

		if ( 'list' == $settings->taxonomy_layout && 1 == $i ) {
			echo '<div class="' . $article_class . '">';
		}

		if ( '1' == $settings->show_alphabetical_list && 'list' == $settings->taxonomy_layout ) {
			if ( $first_letter != $term->name[0] || 1 == $i ) {
				$first_letter = $term->name[0];
				echo '<div class="wpfc-term-first-letter">' . $first_letter . '</div>';
			}
		} ?>

		<div class="wpfc-term-inner <?php if ( 'grid' == $settings->taxonomy_layout ) {
			echo $article_class;
		} ?>">

			<?php

			if ( ( 'grid' == $settings->taxonomy_layout ) && ( '1' == $settings->show_term_image ) ) {
				$associations = get_option( 'sermon_image_plugin' );

				if ( ! empty( $associations[ $term->term_id ] ) ) {
					$image_id = (int) $associations[ $term->term_id ];
				} else {
					$image_id = null;
				}

				if ( $image_id ) {
					echo sprintf(
						'<a href="' . get_term_link( $term, $settings->show_taxonomy ) . '" class="wpfc-term-grid-image" style="background-image:url(%s);"></a>',
						wp_get_attachment_image_url( $image_id, array( 300, 300 ) )
					);
				} else {
					echo sprintf( '<a href="' . get_term_link( $term, $settings->show_taxonomy ) . '" class="wpfc-term-grid-image" style="background-color:#cecece;"></a>' );
				}
			} ?>

			<div class="wpfc-term-content">

				<?php
				if ( ( ( 'grid' == $settings->taxonomy_layout ) && ( '1' == $settings->show_term_title ) ) or ( ( 'list' == $settings->taxonomy_layout ) ) ) {
					?>
					<a href="<?php echo get_term_link( $term, $settings->show_taxonomy ); ?>"
							class="wpfc-term-title"><?php echo $term->name; ?></a>
				<?php } ?>

				<?php
				if ( ( 'grid' == $settings->taxonomy_layout ) && ( '1' == $settings->show_term_description ) ) {
					?>
					<div class="wpfc-term-description"><?php echo wp_trim_words( $term->description, $settings->term_description_length, '...' ); ?></div>

					<?php if ( ( ( str_word_count( $term->description ) > $settings->term_description_length ) ) && ( $settings->show_term_more_link == '1' ) ) : ?>
						<div class="wpfc-term-description-read-more">
							<a href="<?php echo get_permalink(); ?>"><?php echo $settings->term_more_link_text; ?></a>
						</div>
					<?php endif; ?>

				<?php } ?>

			</div>

		</div>

		<?php
		if ( 'list' == $settings->taxonomy_layout ) {
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

</div>

<?php
if ( ( '1' == $settings->show_term_pagination ) && ( $lastpage > 1 ) ) {
	?>
	<div class="wpfc-term-pagination">
		<?php
		if ( ( $prev > 0 ) && ( '1' == $settings->term_show_prev_next ) ) {
			?>
			<a href="?page=<?php echo $prev; ?>"><?php echo $settings->term_previous_label; ?></a>
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

		if ( ( $page < $lastpage ) && ( '1' == $settings->term_show_prev_next ) ) {
			?>
			<a href="?page=<?php echo $next; ?>"><?php echo $settings->term_next_label; ?></a>
			<?php
		} ?>
	</div>
	<?php
}

$posts = ob_get_contents();

ob_end_clean();

echo $posts;

?>
