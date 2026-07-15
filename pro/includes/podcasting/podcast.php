<?php
/**
 * The Podcast instance.
 *
 * @since   2.0.4
 * @package SMP\Podcasting
 */

namespace SMP\Podcasting;

defined( 'ABSPATH' ) or die;

/**
 * The podcast.
 *
 * @since 1.0.0-beta.5
 */
final class Podcast {
	/**
	 * Podcast ID.
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * Podcast name.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Podcast slug.
	 *
	 * @var string
	 */
	public $slug = '';

	/**
	 * Podcast constructor.
	 *
	 * @param \WP_Post|object $podcast Podcast object.
	 */
	public function __construct( $podcast ) {
	}

	/**
	 * Retrieve Podcast instance.
	 *
	 * @static
	 *
	 * @param int $id Podcast ID.
	 *
	 * @return Podcast|false Podcast object, false otherwise.
	 */
	public static function get_instance( $id ) {
		$id = (int) $id;
		if ( ! $id ) {
			return false;
		}

		$post = get_post( $id );
		if ( $post && 'wpfc_sm_podcast' !== $post->post_type ) {
			return false;
		}

		if ( ! $post ) {
			return false;
		}

		return new self( $post );
	}
}
