<?php // phpcs:ignore
/**
 * Template used for displaying single pages
 *
 * @package SM/Views
 */

defined( 'ABSPATH' ) or die;

get_header(); ?>

<?php echo wpfc_get_partial( 'content-sermon-wrapper-start' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme-overridable partial markup from wpfc_get_partial(). ?>

<?php

echo apply_filters( 'single-wpfc_sermon-before-sermons', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filterable markup slot (frozen public filter name).

while ( have_posts() ) :
	global $post;
	the_post();

	if ( ! post_password_required( $post ) ) {
		wpfc_sermon_single_v2(); // You can edit the content of this function in `partials/content-sermon-single.php`.
	} else {
		echo get_the_password_form( $post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core password form markup.
	}

	if ( comments_open() || get_comments_number() ) :
		if ( ! apply_filters( 'single-wpfc_sermon-disable-comments', false ) ) {
			comments_template();
		}
	endif;
endwhile;

echo apply_filters( 'single-wpfc_sermon-after-sermons', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filterable markup slot (frozen public filter name).

?>

<?php echo wpfc_get_partial( 'content-sermon-wrapper-end' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme-overridable partial markup from wpfc_get_partial(). ?>

<?php
get_footer();
