<?php // phpcs:ignore
/**
 * Template used for displaying archive pages
 *
 * @package SM/Views
 */

defined( 'ABSPATH' ) or die;

get_header(); ?>

<?php echo wpfc_get_partial( 'content-sermon-wrapper-start' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme-overridable partial markup from wpfc_get_partial(). ?>

<?php
echo render_wpfc_sorting(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtering UI markup composed by the plugin.

if ( have_posts() ) :

	echo apply_filters( 'archive-wpfc_sermon-before-sermons', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filterable markup slot (frozen public filter name).

	while ( have_posts() ) :
		the_post();
		wpfc_sermon_excerpt_v2(); // You can edit the content of this function in `partials/content-sermon-archive.php`.
	endwhile;

	echo apply_filters( 'archive-wpfc_sermon-after-sermons', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filterable markup slot (frozen public filter name).

	echo '<div class="sm-pagination ast-pagination">';
	sm_pagination();
	echo '</div>';
else :
	echo esc_html__( 'Sorry, but there aren\'t any posts matching your query.', 'church-sermon-manager' );
endif;
?>

<?php echo wpfc_get_partial( 'content-sermon-wrapper-end' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme-overridable partial markup from wpfc_get_partial(). ?>

<?php
get_footer();
