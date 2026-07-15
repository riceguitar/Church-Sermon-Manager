<?php
/**
 * Overrides Divi function for displaying post meta, so we can disable it for page override.
 *
 * @package SMP
 */

if ( ! function_exists( 'et_divi_post_meta' ) ) {
	/**
	 * Disables the post meta output for page override pages.
	 *
	 * @since 1.0.0-beta.0
	 */
	function et_divi_post_meta() {
		$pages = array(
			intval( \SermonManager::getOption( 'smp_archive_page', 0 ) ),
			intval( \SermonManager::getOption( 'smp_tax_page', 0 ) ),
		);

		if ( in_array( get_the_ID(), $pages ) ) {
			return;
		}

		$postinfo = is_single() ? et_get_option( 'divi_postinfo2' ) : et_get_option( 'divi_postinfo1' );

		if ( $postinfo ) :
			echo '<p class="post-meta">';
			echo et_pb_postinfo_meta( $postinfo, et_get_option( 'divi_date_format', 'M j, Y' ), esc_html__( '0 comments', 'Divi' ), esc_html__( '1 comment', 'Divi' ), '% ' . esc_html__( 'comments', 'Divi' ) );
			echo '</p>';
		endif;
	}
}
