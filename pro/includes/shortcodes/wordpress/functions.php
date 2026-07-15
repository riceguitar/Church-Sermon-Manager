<?php
/**
 * Helper functions for WordPress rendering stuff.
 *
 * @package SMP\Shortcodes\WordPress
 */

defined( 'ABSPATH' ) or die;

/**
 * This function overrides the current view, if it's required.
 *
 * @since 1.0.0-beta.0
 *
 * @param WP_Query $query The existing query.
 */
function smp_maybe_override_view( WP_Query $query ) {
	// Check if we've already done it.
	if ( defined( 'SMP_VIEW_OVERRIDDEN' ) ) {
		return;
	}

	// Check that we are not in admin area.
	if ( is_admin() ) {
		return;
	}

	// Check that this is not single sermon.
	if ( is_single() ) {
		return;
	}

	// Check that this is not RSS page.
	if ( is_feed() ) {
		return;
	}

	// Check that the default template is not selected.
	if ( ! \SMP\Templating\Templating_Manager::is_active() && ! SermonManager::getOption( 'force_page_overrides' ) ) {
		return;
	}

	// Get the queried object.
	$queried_object = $query->get_queried_object();

	// Check that we are on sermons page.
	if ( ! ( 'wpfc_sermon' === $query->get( 'post_type' ) || ( isset( $queried_object->taxonomy ) && in_array( $queried_object->taxonomy, sm_get_taxonomies() ) ) || ( isset( $query->query ) && array_intersect( sm_get_taxonomies(), array_keys( $query->query ) ) ) ) ) {
		return;
	}

	// Check that theme doesn't have an override.
	if ( is_tax() ) {
		$default_file = 'taxonomy-' . $queried_object->taxonomy . '.php';

		if ( ! file_exists( get_stylesheet_directory() . '/' . $default_file ) ) {
			$default_file = 'archive-wpfc_sermon.php';
		}
	} elseif ( is_archive() ) {
		$default_file = 'archive-wpfc_sermon.php';
	} else {
		return; // Nothing to do here.
	}

	if ( file_exists( get_stylesheet_directory() . '/' . $default_file ) ) {
		return;
	}

	// Init variable.
	$page_redirect = null;

	// Redirect the view to the page.
	if ( is_tax() ) {
		$page_redirect = \SermonManager::getOption( 'smp_tax_page' );
	} elseif ( is_archive() ) {
		$page_redirect = \SermonManager::getOption( 'smp_archive_page' );
	}

	if ( $page_redirect && 'page' === get_post( intval( $page_redirect ) )->post_type ) {
		$query->parse_query( array(
			'p'         => intval( $page_redirect ),
			'post_type' => 'page',
			'paged'     => get_query_var( 'paged' ),
		) );

		// @todo - do not remove this action in future, but check how to not remove "page(d)" parameter.
		remove_action( 'template_redirect', 'redirect_canonical' );

		define( 'SMP_VIEW_OVERRIDDEN', true );
	}
}

add_action( 'pre_get_posts', 'smp_maybe_override_view' );
