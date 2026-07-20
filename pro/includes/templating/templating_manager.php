<?php
/**
 * Templating feature.
 *
 * @since   2.0.4
 * @package SMP\Templating
 */

namespace SMP\Templating;

use SMP\Shortcodes\Template_Tags;
use Twig\Environment as Twig_Environment;
use Twig\Loader\FilesystemLoader as Twig_Loader_Filesystem;
use Twig\TwigFunction as Twig_SimpleFunction;

defined( 'ABSPATH' ) or die;

/**
 * Main class.
 *
 * @since 2.0.4
 */
final class Templating_Manager {
	/**
	 * Rescans the directory for templates, and updates templates in database based on results.
	 *
	 * @todo - record errors somehow. Currently blindly returns true.
	 *
	 * @return bool True on success, false otherwise.
	 */
	public static function rescan_templates() {
		/**
		 * Allows to change where the directory that contains the templates.
		 */
		$templates_dir = apply_filters( 'sm_pro_templating_templates_directory', WP_CONTENT_DIR . '/data/sermon-manager-for-wordpress/' );

		// Add a trailing slash to the path if it doesn't exist.
		$templates_dir = trailingslashit( $templates_dir );

		// Get all current Templates in WP.
		$wp_templates = self::get_templates();

		// Create a holder for Templates on filesystem.
		$filesystem_templates = array();

		// Create the directory if it doesn't exist.
		if ( ! is_dir( $templates_dir ) && is_writable( dirname( $templates_dir ) ) ) {
			wp_mkdir_p( $templates_dir );
		}

		// Do not use templating if we can't initialize it.
		if ( ! is_dir( $templates_dir ) ) {
			return false;
		}

		// Get the list of templates.
		foreach ( scandir( $templates_dir ) as $filename ) {
			if ( is_dir( $templates_dir . $filename ) && ! in_array( $filename, array( '.', '..' ) ) ) {
				// Check if metadata file exists.
				foreach ( scandir( $templates_dir . $filename ) as $subfile ) {
					if ( pathinfo( $templates_dir . $filename . '/' . $subfile, PATHINFO_EXTENSION ) === 'json' ) {
						$filesystem_templates[ $filename ] = json_decode( file_get_contents( $templates_dir . $filename . '/' . $subfile ), true );
						continue 2;
					}
				}
			}
		}

		// Loop through Templates on the filesystem.
		foreach ( $filesystem_templates as $id => $filesystem_template ) {
			// Loop through WP Templates, to see if we have any to update.
			foreach ( $wp_templates as $wp_template ) {
				// If we have found a match.
				if ( basename( $wp_template->path ) === $id ) {
					// Re-add the path to the metadata array.
					$filesystem_template['path'] = wp_slash( $templates_dir . $id );
					// Update metadata to latest.
					update_post_meta( $wp_template->id, 't_metadata', $filesystem_template );
					continue 2;
				}
			}

			if ( ! defined( 'SM_DOING_SAVE' ) ) {
				define( 'SM_DOING_SAVE', true ); // phpcs:ignore
			}

			// Insert a new template.
			$post_id = wp_insert_post(
				array(
					'post_title'  => $filesystem_template['name'],
					'post_type'   => 'wpfc_sm_template',
					'post_status' => 'publish',
				)
			);

			// Add the path to the metadata array.
			$filesystem_template['path'] = wp_slash( $templates_dir . $id );
			// Save metadata.
			update_post_meta( $post_id, 't_metadata', $filesystem_template );
			// Add default setting values.
			update_post_meta( $post_id, 't_settings', isset( $filesystem_template['default_settings'] ) ? $filesystem_template['default_settings'] : array() );

			// Set the default template as default, if none set previously. Just in case.
			if ( ! get_option( 'sm_template' ) ) {
				update_option( 'sm_template', self::get_default_template_id() );
			}
		}

		return true;
	}

	/**
	 * Gets all installed templates.
	 *
	 * @return array An array of SM_Pro_Template instances
	 */
	public static function get_templates() {
		$templates = get_posts( array( 'post_type' => 'wpfc_sm_template' ) );
		$instances = array();

		foreach ( $templates as $template ) {
			$instances[] = self::get_template( $template->ID );
		}

		return $instances;
	}

	/**
	 * Gets the requested template.
	 *
	 * @param int $id The template ID.
	 *
	 * @return Template
	 */
	public static function get_template( $id = 0 ) {
		if ( 0 === $id ) {
			$id = (int) self::get_active_template_id();
		}

		return Template::get_instance( $id );
	}

	/**
	 * Gets the current active template's ID.
	 *
	 * @return int The ID. Default template's ID otherwise.
	 */
	public static function get_active_template_id() {
		$template = self::get_active_template();

		if ( $template ) {
			return $template->id;
		}

		return self::get_default_template_id();
	}

	/**
	 * Gets the current active template.
	 *
	 * @return Template|false
	 */
	public static function get_active_template() {
		return Template::get_instance( get_option( 'sm_template', self::get_default_template_id() ) );
	}

	/**
	 * Gets the "Default" template ID.
	 *
	 * @return int The template ID.
	 */
	public static function get_default_template_id() {
		global $wpdb;

		/* @noinspection SqlNoDataSourceInspection */

		/* @noinspection SqlResolve */
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = %s",
				array(
					'Sermon Manager',
					'wpfc_sm_template',
				)
			)
		);
	}

	/**
	 * Deletes the template from filesystem.
	 *
	 * @param int $id Template ID to delete.
	 *
	 * @return bool True on success, false otherwise.
	 */
	public static function delete_template( $id ) {
		$template = self::get_template( $id );
		if ( ! $template ) {
			return false;
		}

		if ( file_exists( $template->path ) ) {
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
			}

			/* @noinspection PhpUndefinedMethodInspection */
			return $wp_filesystem->rmdir( $template->path, true );
		} else {
			return true;
		}
	}

	/**
	 * Changes current active template.
	 *
	 * @param int $id Template ID to change to.
	 *
	 * @return bool True on success, false otherwise.
	 */
	public static function switch_to_template( $id ) {
		// Check if the ID is a valid post.
		$template = get_post( $id );
		if ( ! $template ) {
			return false;
		}

		// Check if the post is a template.
		if ( 'wpfc_sm_template' !== $template->post_type ) {
			return false;
		}

		return update_option( 'sm_template', $id );
	}

	/**
	 * Duplicates the selected template.
	 *
	 * @param int $id Template ID to duplicate.
	 *
	 * @return Template|false Newly created template.
	 *
	 * @throws \Exception On failure.
	 * @throws \InvalidArgumentException On invalid source template ID.
	 */
	public static function duplicate_template( $id ) {
		$post = get_post( $id );

		if ( ! defined( 'SM_DOING_SAVE' ) ) {
			define( 'SM_DOING_SAVE', true ); // phpcs:ignore
		}

		if ( isset( $post ) && null != $post ) {
			if ( 'wpfc_sm_template' !== $post->post_type ) {
				throw new \Exception( 'Invalid post type.' );
			}

			$template = self::get_template( $id );
			if ( $template->is_invalid ) {
				throw new \Exception( 'Invalid source template.' );
			}

			$args = array(
				'post_content' => '',
				'post_status'  => 'publish',
				'post_title'   => $post->post_title . ' (copy)',
				'post_type'    => $post->post_type,
			);

			// Create the new template post instance.
			$new_post_id = wp_insert_post( $args, true );

			if ( $new_post_id instanceof \WP_Error ) {
				throw new \Exception( 'Failed to insert the template into database.' );
			}

			if ( sanitize_title( get_the_title( $new_post_id ) ) === '' ) {
				wp_delete_post( $new_post_id, true );

				throw new \Exception( 'Empty copied template title.' );
			}

			// Duplicate all terms.
			$taxonomies = get_object_taxonomies( $post->post_type ); // Returns array of taxonomy names for post type, ex array("category", "post_tag");.
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms( $id, $taxonomy, array( 'fields' => 'slugs' ) );
				wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
			}

			// Duplicate settings metadata.
			update_post_meta( $new_post_id, 'sm_template_settings', get_post_meta( $id, 'sm_template_settings', true ) );
			// Duplicate metadata. And update path.
			$original_metadata         = get_post_meta( $id, 't_metadata', true );
			$original_metadata['path'] = wp_slash( dirname( $template->path ) . '/' . sanitize_title( get_the_title( $new_post_id ) ) );
			update_post_meta( $new_post_id, 't_metadata', $original_metadata );

			// Duplicate files.
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				if ( ! WP_Filesystem() ) {
					throw new \Exception( 'Failed setting up the filesystem.' );
				}
			}

			$source_dir = $template->path;
			$target_dir = dirname( $template->path ) . '/' . sanitize_title( get_the_title( $new_post_id ) );

			/* @noinspection PhpUndefinedMethodInspection */
			if ( ! $wp_filesystem->mkdir( $target_dir ) ) {
				throw new \Exception( 'Unable to create the new template directory.' );
			}

			$copy = copy_dir(
				$source_dir,
				$target_dir
			);

			if ( $copy instanceof \WP_Error ) {
				wp_delete_post( $new_post_id, true );

				throw new \Exception( 'Failed copying the template files. ' . $copy->get_error_message() );
			}

			// Return the Template instance of new template.
			return self::get_template( $new_post_id );
		}

		throw new \InvalidArgumentException( 'Invalid template ID.' );
	}

	/**
	 * Renders the view. Should be used inside a loop, to render a single item (either on archive or single pages).
	 *
	 * @static
	 *
	 * @param string   $context The context in which to render: archive, single, taxonomy, ect.
	 * @param \WP_Post $object  The sermon instance.
	 * @param array    $args    Additional arguments.
	 *
	 * @return string The rendered HTML.
	 *
	 * @throws \RuntimeException If we can't load a view for any reason.
	 */
	static public function render( $context = '', $object = null, $args = array() ) {
		// Let's assume that it's going to be null only when a sermon and not term is used.
		if ( null === $object ) {
			global $post;
			$object = $post;
		}

		// Add new suffixes here.
		$reserved = array(
			'-elementor',
			'-divi',
		);

		// Check if it's an allowed file.
		if ( ! in_array(
			$context,
			array(
				'single',
				'archive',
				'archive-elementor',
				'archive-divi',
				'taxonomy',
				'taxonomy-elementor',
				'taxonomy-divi',
			)
		) ) {
			throw new \RuntimeException( 'The context is invalid. Requested context "' . esc_html( $context ) . '."' );
		}

		// Check if we should use Sermon Manager views.
		if ( ! Templating_Manager::is_active() ) {
			$found = false;
			foreach ( $reserved as $item ) {
				if ( strpos( $context, $item ) ) {
					$found = true;
					break;
				}
			}
			if ( ! $found ) {
				return false;
			}
		}

		// Get the template data.
		$template = self::get_template();

		// Check if template file exist.
		$templates_root = $template->path;
		$file_path      = $template->path . '/' . $context . '.twig';
		if ( ! file_exists( $file_path ) || $template->is_invalid ) {
			if ( strpos( $context, '-elementor' ) !== false ) {
				$context        = str_replace( '-elementor', '', $context );
				$templates_root = SMP_PATH . 'views/elementor';
			}

			if ( strpos( $context, '-divi' ) !== false ) {
				$context        = str_replace( '-divi', '', $context );
				$templates_root = SMP_PATH . 'views/divi';
			}
		}

		if ( empty( $templates_root ) || ! $templates_root ) {
			throw new \RuntimeException( 'Could not find the root directory for template files.' );
		}

		// Define Twig arguments. Recreate the cache dir if something removed it,
		// otherwise Twig silently recompiles templates on every render.
		wp_mkdir_p( SMP_PATH . 'cache/twig' );
		$twig_args = array();
		if ( wp_is_writable( SMP_PATH . 'cache/twig' ) ) {
			$twig_args = array(
				'cache'       => SMP_PATH . 'cache/twig',
				'auto_reload' => true,
			);
		}

		// Load the twig file.
		$loader = new Twig_Loader_Filesystem( $templates_root );
		$twig   = new Twig_Environment( $loader, $twig_args );

		$twig->addFunction(
			new Twig_SimpleFunction(
				'fn',
				function ( $function_name ) {
					$args = func_get_args();
					array_shift( $args );
					if ( is_string( $function_name ) ) {
						$function_name = trim( $function_name );
					}

					return call_user_func_array( $function_name, ( $args ) );
				}
			)
		);

		$GLOBALS['tt_object'] = $object; // phpcs:ignore

		$twig->addFunction(
			new Twig_SimpleFunction(
				'TemplateTags',
				function ( $method_name ) {
					global $tt_object;

					$args = func_get_args();
					array_shift( $args );
					if ( is_string( $method_name ) ) {
						$method_name = trim( $method_name );
					}

					$template_tags = new Template_Tags( $tt_object, false );

					return call_user_func_array( array( $template_tags, $method_name ), ( $args ) );
				}
			)
		);

		try {
			$template = $twig->load( $context . '.twig' );
		} catch ( \Exception $e ) {
			throw new \RuntimeException( 'Error in loading template file: ' . $e->getMessage() );
		}

		$settings = Settings::get_settings();

		$settings += array(
			'use_published_date' => \SermonManager::getOption( 'use_published_date' ),
		);

		if ( isset( $args['settings'] ) ) {
			$settings = $args['settings'] + $settings;
		}

		ob_start();
		// Precedence note: the original expression concatenated before the ternary,
		// so 'smpro-article' was never actually emitted. Kept behavior-identical
		// (minus the PHP warning) to avoid changing front-end markup.
		post_class( isset( $args['additional_class'] ) ? $args['additional_class'] : '', $object );
		$post_class = ob_get_clean();

		$settings['post_class'] = $post_class;

		// Add preacher label.
		$args['preacher_label'] = \SermonManager::getOption( 'preacher_label' ) ?: __( 'Preacher', 'church-sermon-manager' );
		$args['date_format']    = get_option( 'date_format', 'Y-m-d' );

		// Comments.
		if ( 'Divi' === get_option( 'template' ) && function_exists( 'et_get_option' ) ) {
			if ( ( comments_open() || get_comments_number() ) && 'on' == et_get_option( 'divi_show_postcomments', 'on' ) ) {
				ob_start();
				comments_template( '', true );
				$comments = ob_get_clean();
			}
		}
		$args['comments'] = ! empty( $comments ) ? $comments : '';

		// Get some nice sermon data for easy access.
		$sermon = array(
			'series'        => wp_get_post_terms( $object->ID, 'wpfc_sermon_series' ),
			'preachers'     => wp_get_post_terms( $object->ID, 'wpfc_preacher' ),
			'bible_book'    => wp_get_post_terms( $object->ID, 'wpfc_bible_book' ),
			'topics'        => wp_get_post_terms( $object->ID, 'wpfc_sermon_topics' ),
			'service_type'  => wp_get_post_terms( $object->ID, 'wpfc_service_type' ),
			'description'   => wpfc_sermon_description( '', '', true ),
			'date_preached' => get_post_meta( $object->ID, 'sermon_date', true ),
		);

		$render_args = array(
			'settings' => $settings,
			'post'     => $object,
			'args'     => $args,
			'sermon'   => $sermon,
		);

		$GLOBALS['smpro_template'] = true;

		try {
			return $template->render( $render_args );
		} catch ( \Throwable $e ) {
			// Twig errors extend \Exception, not \RuntimeException — rethrow so the
			// callers' catch ( \RuntimeException ) fallbacks actually engage instead
			// of the error escaping as a fatal.
			throw new \RuntimeException( 'Error rendering template: ' . $e->getMessage(), 0, $e );
		}
	}

	/**
	 * Checks if templating is active.
	 *
	 * @return bool True if it is, false otherwise.
	 */
	public static function is_active() {
		$template = self::get_active_template_id();

		return $template && self::get_default_template_id() !== $template;
	}
}