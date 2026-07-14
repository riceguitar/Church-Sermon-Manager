<?php
/**
 * Defines the functions for rendering parts of Sermon Manager's output.
 *
 * @since   2.0.4
 * @package SMP\Shortcodes
 */

namespace SMP\Shortcodes;

defined( 'ABSPATH' ) or exit;

/**
 * Class Template_Tags
 */
class Template_Tags {
	/**
	 * The current sermon.
	 *
	 * @var \WP_Post|null
	 */
	public $post = null;

	/**
	 * The current term.
	 *
	 * @var \WP_Term|null
	 */
	public $term = null;

	/**
	 * Template_Tags constructor.
	 *
	 * @param \WP_Post|\WP_Term|null $object The current sermon/term.
	 * @param bool                   $term   If it's a term.
	 */
	public function __construct( $object = null, $term = false ) {
		if ( null !== $object ) {
			if ( $term ) {
				$this->term = $object;
			} else {
				$this->post = $object;
			}
		} else {
			if ( ! $term ) {
				global $post;
				$this->post = $post;
			}
		}
	}

	/**
	 * Renders the title HTML.
	 *
	 * @param array $args Render arguments.
	 *
	 * @type string $args ['title_tag'] HTML title tag. Default: h3.
	 *
	 * @return mixed|void
	 */
	public function the_title( $args = array() ) {
		$post = $this->post;

		$default = array(
			'title_tag' => 'h3',
		);
		$args    = $args + $default;

		ob_start();
		?>
		<<?php echo esc_html( $args['title_tag'] ); ?> class="elementor-post__title sm-pro-sermon-title" style="margin-top: 0">
		<a href="<?php the_permalink( $this->post ); ?>" class="sm-pro-sermon-title-link">
			<?php echo $this->get_the_title(); ?>
		</a>
		<?php echo '</' . esc_html( $args['title_tag'] ); ?>>
		<?php
		$content = ob_get_clean();

		/**
		 * Allows to filter the rendered title HTML.
		 *
		 * @since 2.0.4
		 *
		 * @param string   $content The HTML.
		 * @param \WP_Post $post    The sermon.
		 * @param array    $args    The settings.
		 */
		echo apply_filters( 'smp/shortcodes/the_title', $content, $post, $args );
	}

	/**
	 * Gets the title.
	 *
	 * @return string The title.
	 */
	public function get_the_title() {
		$post  = $this->post;
		$title = get_the_title( $post );

		/**
		 * Allows to filter the returned title.
		 *
		 * @since 2.0.4
		 *
		 * @param string   $title The title.
		 * @param \WP_Post $post  The post.
		 */
		return apply_filters( 'smp/shortcodes/get_the_title', $title, $post );
	}

	/**
	 * Renders the excerpt HTML.
	 *
	 * @param array $args Render arguments.
	 *
	 * @type string $args ['excerpt_length'] The excerpt length in words. Default: WordPress filter default (25).
	 */
	public function the_excerpt( $args = array() ) {
		$post = $this->post;

		ob_start();
		?>
		<div class="elementor-post__excerpt sm-pro-sermon-excerpt">
			<?php echo esc_html( $this->get_the_excerpt( $args ) ); ?>
		</div>
		<?php
		$content = ob_get_clean();

		/**
		 * Allows to filter excerpt HTML.
		 *
		 * @since 2.0.4
		 *
		 * @param string   $content The HTML.
		 * @param \WP_Post $post    The sermon.
		 * @param array    $args    The settings.
		 */
		echo apply_filters( 'smp/shortcodes/the_excerpt', $content, $post, $args );
	}

	/**
	 * Gets the sermon excerpt.
	 *
	 * @param array $args Output arguments.
	 *
	 * @type string $args ['excerpt_length'] The excerpt length in words. Default: WordPress filter default (25).
	 *
	 * @return string The excerpt.
	 */
	public function get_the_excerpt( $args = array() ) {
		$post = $this->post;

		$default = array(
			'excerpt_length' => apply_filters( 'excerpt_length', 25 ),
		);
		$args    = $args + $default;

		if ( post_password_required( $post ) ) {
			return __( 'There is no excerpt because this is a protected post.' );
		}

		$original_excerpt = get_post_meta( $post->ID, 'sermon_description', true );

		$excerpt = wp_trim_words( $original_excerpt, intval( $args['excerpt_length'] ) );

		/**
		 * Allows to filter returned excerpt.
		 *
		 * @since 2.0.4
		 *
		 * @param string   $excerpt          The trimmed excerpt.
		 * @param \WP_Post $post             The sermon.
		 * @param string   $original_excerpt Unmodified excerpt.
		 * @param array    $args             The settings.
		 */
		return apply_filters( 'smp/shortcodes/get_the_excerpt', $excerpt, $post, $original_excerpt, $args );
	}

	/**
	 * Renders the meta data content HTML.
	 *
	 * @param array $args Rendering options.
	 *
	 * @type array  $args ['meta_data'] Array of metadata keys.
	 * @type string $args ['before'] Optional content before the metadata.
	 * @type bool   $args ['inline'] Should it be inline. Default: false.
	 * @type bool   $args ['link'] Should it be linked. Default: true.
	 * @type string $args ['date_format'] Optional custom date format.
	 * @type bool   $args ['verse_init'] Should it initialize passage script or not.
	 */
	public function the_metadata( $args = array() ) {
		$default = array(
			'meta_data'   => array(),
			'before'      => '',
			'inline'      => false,
			'link'        => true,
			'date_format' => '',
			'verse_init'  => false,
		);
		$args    = $args + $default;

		$post              = $this->post;
		$args['meta_data'] = ! empty( $args['meta_data'] ) ? $args['meta_data'] : array();
		ob_start();
		?>

		<<?php echo $args['inline'] ? 'span' : 'div'; ?> class="sm-pro-sermon-meta-items">
		<?php foreach ( $args['meta_data'] as $item ) { ?>
			<span class="sm-pro-sermon-meta-item sm-pro-sermon-meta-item-<?php echo esc_attr( sanitize_title( $item ) ); ?> elementor-post-<?php echo esc_attr( sanitize_title( $item ) ); ?>">
					<?php if ( $args['before'] ) : ?>
						<?php echo $args['before']; ?>
					<?php endif; ?>
					<?php
					switch ( $item ) {
						case 'date':
							echo $this->get_the_published_date( array(
								'date_format' => $args['date_format'],
							) );
							break;
						case 'time':
							echo $this->get_the_published_time();
							break;
						case 'preached_date':
							echo 'Preached Date: ';
							echo $this->get_the_preached_date( array(
								'date_format' => $args['date_format'],
							) );
							break;
						case 'author':
							echo $this->get_the_author();
							break;
						case 'preachers':
							$this->the_terms(
								array(
									'taxonomy' => 'wpfc_preacher',
									'link'     => $args['link'],
								)
							);
							break;
						case 'books':
							$this->the_terms(
								array(
									'taxonomy' => 'wpfc_bible_book',
									'link'     => $args['link'],
								)
							);
							break;
						case 'series':
							$this->the_terms(
								array(
									'taxonomy' => 'wpfc_sermon_series',
									'link'     => $args['link'],
								)
							);
							break;
						case 'service_type':
							$this->the_terms(
								array(
									'taxonomy' => 'wpfc_service_type',
									'link'     => $args['link'],
								)
							);
							break;
						case 'comments':
							echo $this->get_the_comments();
							break;
						case 'passage':
							echo $this->get_the_passage( array(
								'verse_init' => $args['verse_init'],
							) );
							break;
						default:
							echo apply_filters( 'smp/shortcodes/the_metadata_' . $item, '' );
					}
					?>
				</span>
		<?php } ?>
		<<?php echo $args['inline'] ? '/span' : '/div'; ?>>

		<?php
		$content = ob_get_clean();

		/**
		 * Allows to filter the rendered metadata HTML.
		 *
		 * @since 2.0.4
		 *
		 * @param string   $content The HTML.
		 * @param \WP_Post $post    The sermon.
		 * @param array    $args    The settings.
		 */
		echo apply_filters( 'smp/shortcodes/the_metadata', $content, $post, $args );
	}

	/**
	 * Gets the published date.
	 *
	 * @param array $args Function arguments.
	 *
	 * @type string $args ['date_format'] Custom date format.
	 *
	 * @return string The formatted published date.
	 */
	public function get_the_published_date( $args = array() ) {
		$post = $this->post;

		$default = array(
			'date_format' => '',
		);
		$args    = $args + $default;

		$date = mysql2date( $args['date_format'] ?: get_option( 'date_format' ), $post->post_date );

		/**
		 * Allows to modify the returned published time.
		 *
		 * @param string   $time The time.
		 * @param \WP_Post $post The sermon.
		 */
		return apply_filters( 'smp/shortcodes/get_the_published_date', $date, $post );
	}

	/**
	 * Gets the published time.
	 *
	 * @return string The formatted published time.
	 */
	public function get_the_published_time() {
		$post = $this->post;
		$time = get_post_time( get_option( 'time_format' ), false, $post, true );

		/**
		 * Allows to modify the returned published time.
		 *
		 * @param string   $time The time.
		 * @param \WP_Post $post The sermon.
		 */
		return apply_filters( 'smp/shortcodes/get_the_published_time', $time, $post );
	}

	/**
	 * Gets the preached date of the sermon.
	 *
	 * @param array $args Function arguments.
	 *
	 * @type string $args ['date_format'] Custom date format.
	 *
	 * @return string The formatted preached date.
	 */
	public function get_the_preached_date( $args = array() ) {
		$post = $this->post;

		$default = array(
			'date_format' => '',
		);
		$args    = $args + $default;

		$date = \SM_Dates::get( $args['date_format'] ?: get_option( 'date_format' ) );

		/**
		 * Allows to modify the returned preached date.
		 *
		 * @param string   $date The formatted date.
		 * @param \WP_Post $post The sermon.
		 */
		return apply_filters( 'smp/shortcodes/get_the_preached_date', $date, $post );
	}

	/**
	 * Gets the WordPress author of the Sermon.
	 *
	 * @return string The author.
	 */
	public function get_the_author() {
		$post   = $this->post;
		$author = get_the_author();

		/**
		 * Allows to filter the returned author name.
		 *
		 * @since 2.0.4
		 *
		 * @param string   $author The author name.
		 * @param \WP_Post $post   The sermon.
		 */
		return apply_filters( 'smp/shortcodes/get_the_author', $author, $post );
	}

	/**
	 * Renders the terms list from the specified sermon taxonomy.
	 *
	 * @param array $args Render arguments.
	 *
	 * @type string $args ['taxonomy'] The taxonomy.
	 * @type bool   $args ['link'] Should it output as links or plain-text. Default: links (true).
	 */
	public function the_terms( $args ) {
		$default = array(
			'taxonomy' => '',
			'link'     => true,
		);
		$args    = $args + $default;

		$post       = $this->post;
		$terms      = $this->get_the_terms( array( 'taxonomy' => $args['taxonomy'] ) );
		$tax_object = get_taxonomy_labels( get_taxonomy( $args['taxonomy'] ) );
		if ( $terms ) {
			ob_start();
			?>
			<?php echo 'Preacher' === $tax_object->singular_name ? 'Preacher Name' : esc_html( $tax_object->singular_name ); ?>
			<span
					class="sm-pro-sermon-taxonomy-label-separator">:</span>
			<?php foreach ( $terms as $term ) : ?>
				<?php echo ( $args['link'] ? '<a href="' . get_term_link( $term ) . '">' : '<span>' ) . esc_html( $term->name ) . ( $args['link'] ? '</a>' : '</span>' ) . ( end( $terms ) !== $term ? apply_filters( 'smp/shortcodes/the_terms/separator', ',' ) : '' ); ?>
			<?php endforeach; ?>
			<?php
			$content = ob_get_clean();
		} else {
			$content = '';
		}

		/**
		 * Allows to filter the outputted content.
		 *
		 * @param string   $content The HTML.
		 * @param \WP_Post $post    The sermon.
		 * @param array    $args    The render arguments.
		 */
		echo apply_filters( 'smp/shortcodes/the_terms', $content, $post, $args );
	}

	/**
	 * Gets the array of specified sermon taxonomy objects for the current sermon.
	 *
	 * @param array $args     Function arguments.
	 *
	 * @type string $taxonomy From what taxonomy to get the terms.
	 *
	 * @return array The terms array.
	 */
	public function get_the_terms( $args = array() ) {
		$default = array(
			'taxonomy' => '',
		);
		$args    = $args + $default;

		$post  = $this->post;
		$terms = wp_get_object_terms( $post->ID, $args['taxonomy'] );

		/**
		 * Allows to filter the terms array.
		 *
		 * @since 2.0.4
		 *
		 * @param array    $term The term array.
		 * @param \WP_Post $post The sermon.
		 * @param array    $args The function arguments.
		 */
		return apply_filters( 'smp/shortcodes/get_the_terms', $terms, $post, $args );
	}

	/**
	 * Gets the number of comments.
	 *
	 * @return string The number of comments. Not an int.
	 */
	public function get_the_comments() {
		$post     = $this->post;
		$comments = comments_number();

		/**
		 * Allows to filter the comments meta.
		 *
		 * @param string   $comments The number of comments.
		 * @param \WP_Post $post     The sermon.
		 */
		return apply_filters( 'smp/shortcodes/get_the_comments', $comments, $post );
	}

	/**
	 * Gets the sermon passage.
	 *
	 * @param array $args Function arguments.
	 *
	 * @type bool   $args ['verse_init'] Should it init the script or not.
	 *
	 * @return string The passage.
	 */
	public function get_the_passage( $args = array() ) {
		$post = $this->post;

		$default = array(
			'verse_init' => false,
		);
		$args    = $args + $default;

		if ( $args['verse_init'] ) {
			wp_enqueue_script( 'wpfc-sm-verse-script' );
		}

		$passage = get_post_meta( $post->ID, 'bible_passage', true );

		/**
		 * Allows to filter the passage.
		 *
		 * @param string   $passage The passage.
		 * @param \WP_Post $post    The sermon.
		 */
		return apply_filters( 'smp/shortcodes/get_the_passage', $passage, $post );
	}

	/**
	 * Renders the sermon audio player.
	 *
	 * @param array $args Render settings.
	 *
	 * @type string $args ['url'] The audio source.
	 * @type string $args ['seek'] Should it seek to specific point in time.
	 * @type string $args ['player'] The player to use.
	 */
	public function the_audio_player( $args = array() ) {
		$default = array(
			'url'      => '',
			'seek'     => '',
			'player'   => 'plyr',
			'download' => 'false',
		);
		$args    = $args + $default;
		$post    = $this->post;

		if ( '' === $args['url'] ) {
			$args['url'] = get_post_meta( $post->ID, 'sermon_audio', true );
		}

		if ( '' !== $args['url'] ) {
			$output = '<div class="sm-pro-sermon-audio-player sm-pro-sermon-audio-player-' . esc_attr( $args['player'] ) . '">';
			if ( strtolower( 'WordPress' ) === $args['player'] ) {
				$attr = array(
					'src'     => $args['url'],
					'preload' => 'none',
				);

				$output .= wp_audio_shortcode( $attr );
			} else {
				$extra_settings = '';

				if ( is_numeric( $args['seek'] ) ) {
					// Sanitation just in case.
					$extra_settings = 'data-plyr_seek=\'' . intval( $args['seek'] ) . '\'';
				}

				if ( ! empty( $args['download'] ) ) {

					$config_download = array(
						'urls'     => array(
							'download' => $args['url'],
						),
						'controls' => array(
							'download',
							'play-large',
							'play',
							'progress',
							'current-time',
							'mute',
							'volume',
							'captions',
							'settings',
							'pip',
							'airplay',
							'fullscreen',
						),
					);

					$extra_settings .= ' data-plyr-config=\'' . json_encode( $config_download ) . '\'';
				}


				$output .= '<audio controls preload="metadata" class="wpfc-sermon-player ' . ( 'mediaelement' === $args['player'] ? 'mejs__player' : '' ) . '" ' . $extra_settings . '>';
				$output .= '<source src="' . $args['url'] . '">';
				$output .= '</audio>';
			}

			$output .= '</div>';
		} else {
			$output = '';
		}

		/**
		 * Allows to filter the player HTML.
		 *
		 * @since 2.0.4
		 *
		 * @param string   $output The HTML.
		 * @param \WP_Post $post   The sermon.
		 * @param array    $args   The settings.
		 */
		echo apply_filters( 'smp/shortcodes/the_audio_player', $output, $post, $args );
	}

	/**
	 * Renders the sermon video player.
	 *
	 * @param array $args Render settings.
	 *
	 * @type string $args ['url'] The video source.
	 * @type string $args ['seek'] Should it seek to specific point in time.
	 * @type string $args ['player'] The player to use.
	 */
	public function the_video_player( $args = array() ) {
		$default = array(
			'url'    => '',
			'seek'   => '',
			'player' => 'plyr',
		);
		$args    = $args + $default;
		$post    = $this->post;

		if ( ! is_string( $args['url'] ) || trim( $args['url'] ) === '' ) {
			$output = '';
		} else {

			if ( strpos( $args['url'], 'facebook.' ) !== false ) {
				wp_enqueue_script( 'wpfc-sm-fb-player' );

				parse_str( parse_url( $args['url'], PHP_URL_QUERY ), $query );

				$output = '<div class="fb-video" data-href="' . $args['url'] . '" data-width="' . ( isset( $query['width'] ) ? ( is_numeric( $query['width'] ) ? $query['width'] : '600' ) : '600' ) . '" data-allowfullscreen="' . ( isset( $query['fullscreen'] ) ? ( 'yes' === $query['width'] ? 'true' : 'false' ) : 'true' ) . '"></div>';
			} else {
				$player = strtolower( \SermonManager::getOption( 'player' ) ?: 'plyr' );

				if ( strtolower( 'WordPress' ) === $player ) {
					$attr = array(
						'src'     => $args['url'],
						'preload' => 'none',
					);

					$output = wp_video_shortcode( $attr );
				} else {
					$is_youtube_long  = strpos( strtolower( $args['url'] ), 'youtube.com' );
					$is_youtube_short = strpos( strtolower( $args['url'] ), 'youtu.be' );
					$is_youtube       = $is_youtube_long || $is_youtube_short;
					$is_vimeo         = strpos( strtolower( $args['url'] ), 'vimeo.com' );
					$extra_settings   = '';
					$output           = '';

					if ( is_numeric( $args['seek'] ) ) {
						// Sanitation just in case.
						$extra_settings = 'data-plyr_seek=\'' . intval( $args['seek'] ) . '\'';
					}

					if ( 'plyr' === $player && ( $is_youtube || $is_vimeo ) ) {
						$output .= '<div data-type="' . ( $is_youtube ? 'youtube' : 'vimeo' ) . '" data-video-id="' . $args['url'] . '" class="wpfc-sermon-video-player video-' . ( $is_youtube ? 'youtube' : 'vimeo' ) . ( 'mediaelement' === $player ? 'mejs__player' : '' ) . '" ' . $extra_settings . '></div>';
					} else {
						$output .= '<video controls preload="metadata" class="wpfc-sermon-video-player ' . ( 'mediaelement' === $player ? 'mejs__player' : '' ) . '" ' . $extra_settings . '>';
						$output .= '<source src="' . $args['url'] . '">';
						$output .= '</video>';
					}
				}
			}
		}

		/**
		 * Allows to filter the player HTML.
		 *
		 * @since 2.0.4
		 *
		 * @param string   $output The HTML.
		 * @param \WP_Post $post   The sermon.
		 * @param array    $args   The settings.
		 */
		echo apply_filters( 'smp/shortcodes/the_video_player', $output, $post, $args );
	}

	/**
	 * Gets the preacher avatar.
	 *
	 * @param array $args Function arguments.
	 *
	 * @type int    $args ['id']      Preacher ID.
	 * @type int    $args ['size']    Optional image size in pixels.
	 * @type string $args ['default'] Optional default image URL.
	 * @type string $args ['alt']     Optional alt text for the image.
	 *
	 * @return string The avatar HTML.
	 */
	public function get_the_avatar( $args = array() ) {
		$default = array(
			'id'      => 0,
			'size'    => 96,
			'default' => '',
			'alt'     => '',
		);
		$args    = $args + $default;

		$image_id  = $this->get_the_term_image_id( array( 'id' => $args['id'] ) );
		$image_url = wp_get_attachment_image_url( $image_id, array( $args['size'], $args['size'] ) );

		/**
		 * Allows to add arbitrary HTML before the actual avatar HTML.
		 *
		 * @since 2.0.4
		 *
		 * @param array $args Function arguments.
		 */
		$avatar = apply_filters( 'smp/shortcodes/pre_get_avatar', null, $args );

		if ( ! $image_url ) {
			if ( $args['default'] ) {
				$image_url = $args['default'];
			} else {
				/**
				 * Allows to filter the output when no image exists.
				 *
				 * @since 2.0.4
				 *
				 * @param array $args Function arguments.
				 */
				return apply_filters( 'smp/shortcodes/get_the_avatar', $avatar, $args );
			}
		}

		ob_start();
		?>
		<div class="sm-pro-sermon-avatar-container"
				style="height: 60px;
                        width: 60px;
				        overflow: hidden;
						transform: translateY(-50%);
						border-radius: 50%;
						background-image: url(<?php echo $image_url; ?>);
                        background-position: center;
                        background-size: cover;">
		</div>
		<?php
		$avatar .= ob_get_clean();

		/**
		 * Allows to filter the output.
		 *
		 * @since 2.0.4
		 *
		 * @param array $args Function arguments.
		 */
		return apply_filters( 'smp/shortcodes/get_the_avatar', $avatar, $args );
	}

	/**
	 * Gets the WordPress attachment ID of the specified term.
	 *
	 * @param array $args The function arguments.
	 *
	 * @type int    $args ['id']   The term ID. Optional if used in taxonomy context.
	 *
	 * @return int|null The image ID or null if not found.
	 */
	public function get_the_term_image_id( $args = array() ) {
		$default = array(
			'id' => 0,
		);
		$args    = $args + $default;

		if ( $this->term ) {
			$args['id'] = $this->term->term_id;
		}

		$associations = get_option( 'sermon_image_plugin' );

		if ( ! empty( $associations[ $args['id'] ] ) ) {
			$image_id = (int) $associations[ $args['id'] ];
		} else {
			$image_id = null;
		}

		/**
		 * Allows to filter the image ID.
		 *
		 * @since 2.0.4
		 *
		 * @param int|null $image_id The image ID.
		 * @param array    $args     Function arguments.
		 */
		return apply_filters( 'smp/shortcodes/get_the_term_image_id', $image_id, $args['id'] );
	}

	/**
	 * Renders the taxonomy term HTML.
	 *
	 * @param array $args Render arguments.
	 *
	 * @type string $args ['desc_length'] The description length in words. Default: WordPress excerpt filter default
	 *       (25).
	 */
	public function the_description( $args = array() ) {
		$term = $this->term;

		ob_start();
		?>
		<div class="sm-pro-sermon-description">
			<?php echo esc_html( $this->get_the_description( $args ) ); ?>
		</div>
		<?php
		$content = ob_get_clean();

		/**
		 * Allows to filter description HTML.
		 *
		 * @since 2.0.4
		 *
		 * @param string   $content The HTML.
		 * @param \WP_Post $post    The sermon.
		 * @param array    $args    The settings.
		 */
		echo apply_filters( 'smp/shortcodes/the_description', $content, $term, $args );
	}

	/**
	 * Gets the term description.
	 *
	 * @param array $args Output arguments.
	 *
	 * @type string $args ['desc_length'] The description length in words. Default: WordPress excerpt filter default
	 *       (25).
	 *
	 * @return string The description.
	 */
	public function get_the_description( $args = array() ) {
		$term = $this->term;

		$default = array(
			'desc_length' => apply_filters( 'excerpt_length', 25 ),
		);
		$args    = $args + $default;

		$original_description = $term->description;

		$description = wp_trim_words( $original_description, intval( $args['desc_length'] ) );

		/**
		 * Allows to filter returned description.
		 *
		 * @since 2.0.4
		 *
		 * @param string   $excerpt          The trimmed description.
		 * @param \WP_Post $post             The term.
		 * @param string   $original_excerpt Unmodified description.
		 * @param array    $args             The settings.
		 */
		return apply_filters( 'smp/shortcodes/get_the_description', $description, $term, $original_description, $args );
	}
}
