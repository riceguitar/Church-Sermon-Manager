<?php
/**
 * Podcasting feature.
 *
 * @since   1.0.0-beta.5
 * @package SMP\Podcasting
 */

namespace SMP\Podcasting;

defined( 'ABSPATH' ) or die;

/**
 * Main class.
 *
 * @since 1.0.0-beta.5
 */
class Podcasting_Manager {
	/**
	 * Podcasting_Manager constructor.
	 */
	public function __construct() {
		// Render the podcast.
		add_action( 'rss_tag_pre', array( $this, 'maybe_render' ), 5 );
		// Filter the podcast query if needed.
		add_filter( 'sermon_feed_query_args', array( $this, 'filter_the_query' ) );
	}

	/**
	 * Check if we should render the podcast.
	 */
	public function maybe_render() {
		global $post_type;

		if ( ! is_feed() ) {
			return;
		}

		if ( ! 'wpfc_sermon' === $post_type ) {
			return;
		}

		if ( ! isset( $_GET['id'] ) ) {
			$this->_render( $this->get_default_podcast_id() );
		} else {
			$this->_render( $_GET['id'] );
		}
	}

	/**
	 * Renders the podcast XML.
	 *
	 * @param string $id The podcast ID.
	 */
	protected function _render( $id ) {
		$podcast = $this->get_the_podcast( $id );

		if ( ! $podcast instanceof \WP_Post ) {
			return;
		}

		$podcast_data = get_post_meta( $podcast->ID, 'sm_podcast_settings', true );

		if ( is_array( $podcast_data ) ) {
			foreach ( $podcast_data as $key => &$podcast_option ) {
				$podcast_option = smp_string_to_bool( $podcast_option, false );
			}
		}

		$GLOBALS['sm_podcast_data'] = $podcast_data;
	}

	/**
	 * Get the podcast object by slug or ID.
	 *
	 * @param int|string $id The podcast ID/slug.
	 *
	 * @return \WP_Post|null
	 */
	protected function get_the_podcast( $id ) {
		if ( is_numeric( $id ) ) {
			$podcast = get_post( $id );
		} elseif ( is_string( $id ) ) {
			$podcast = get_page_by_path( $id, OBJECT, 'wpfc_sm_podcast' );
		} else {
			$podcast = null;
		}

		return $podcast;
	}

	/**
	 * Returns default podcast ID.
	 *
	 * @return int The default podcast ID or 0 (zero) if it does not exist.
	 */
	public function get_default_podcast_id() {
		return (int) get_option( 'wpfc_sm_default_podcast', 0 );
	}

	/**
	 * Filters the query.
	 *
	 * @param array $args Existing args.
	 *
	 * @return array Modified args.
	 */
	public function filter_the_query( $args ) {
		if ( ! isset( $_GET['id'] ) ) {
			return $args;
		}

		$podcast = $this->get_the_podcast( $_GET['id'] );

		if ( ! $podcast instanceof \WP_Post ) {
			return $args;
		}

		foreach ( sm_get_taxonomies() as $taxonomy ) {
			$terms = wp_get_object_terms( $podcast->ID, $taxonomy, array( 'fields' => 'ids' ) );

			if ( empty( $terms ) ) {
				continue;
			}

			if ( empty( $args['tax_query'] ) ) {
				$args['tax_query'] = array();
			}

			$args['tax_query'][] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => $terms,
			);
		}

		if ( isset( $args['tax_query'] ) && count( $args['tax_query'] ) > 1 ) {
			$args['tax_query']['relation'] = 'AND';
		}

		// Add video instead of audio file.
		$podcast_settings = get_post_meta( $podcast->ID, 'sm_podcast_settings', true );
		if ( ! empty( $podcast_settings['sermons_to_show'] ) ) {

			if ( $podcast_settings['sermons_to_show'] === 'video' ) {
				// Remove audio from meta_query.
				if ( ! empty( $args['meta_query'] ) && is_array( $args['meta_query'] ) ) {

					foreach ( $args['meta_query'] as $index => $meta ) {
						if ( isset( $meta['key'] ) && 'sermon_audio' === $meta['key'] ) {
							unset( $args['meta_query'][ $index ] );
						}
					}
				}
			}

			if ( empty( $args['meta_query'] ) ) {
				$args['meta_query'] = array();
			}

			$args['meta_query'][] = array(
				'key'     => 'sermon_video_link',
				'compare' => 'EXISTS',
			);

			$args['meta_query'][] = array(
				'key'     => 'sermon_video_link',
				'value'   => '',
				'compare' => '!=',
			);
		}

		return $args;
	}
}

