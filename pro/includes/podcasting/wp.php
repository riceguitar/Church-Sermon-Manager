<?php
/**
 * Used for overriding WP with our data for Podcasting.
 *
 * @since   1.0.0-beta.5
 * @package SMP\Podcasting
 */

namespace SMP\Podcasting;

use SMP\Plugin;

defined( 'ABSPATH' ) or die;

/**
 * Class WP
 *
 * @since 1.0.0-beta.5
 */
class WP {

	/**
	 * Podcasting post type.
	 *
	 * @var    string
	 */
	private $post_type = 'wpfc_sm_podcast';

	/**
	 * WP constructor.
	 */
	public function __construct() {
		$this->_filters();
		$this->_actions();
	}

	/**
	 * Adds the required filters.
	 */
	protected function _filters() {
		// Add podcasting metaboxes.
		add_action( 'add_meta_boxes', array( $this, 'add_tutorial_metabox' ), 1, 2 );
		add_action( 'add_meta_boxes', array( $this, 'add_main_metabox' ), 2, 2 );

		// Add taxonomy to wpfc_sm_podcast post type.
		add_filter( 'sm_taxonomy_objects_wpfc_sermon_series', array( $this, 'add_taxonomy_podcast' ), 10 );
		add_filter( 'sm_taxonomy_objects_wpfc_preacher', array( $this, 'add_taxonomy_podcast' ), 10 );
		add_filter( 'sm_taxonomy_objects_wpfc_sermon_topics', array( $this, 'add_taxonomy_podcast' ), 10 );
		add_filter( 'sm_taxonomy_objects_wpfc_bible_book', array( $this, 'add_taxonomy_podcast' ), 10 );
		add_filter( 'sm_taxonomy_objects_wpfc_service_type', array( $this, 'add_taxonomy_podcast' ), 10 );

		// Replace table columns in "Podcasting" view.
		add_filter( 'manage_' . $this->post_type . '_posts_columns', array( $this, 'podcast_columns' ) );
		// Show all podcasts on the view.
		add_filter( 'edit_posts_per_page', array( $this, 'adjust_sermons_per_page' ), 10, 2 );
		// Change the post permalink to the feed URL.
		add_filter( 'post_type_link', array( $this, 'change_actual_permalink' ), 10, 4 );
		// Add additional body class on editor load.
		add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
	}

	/**
	 * Add additional body class on editor load.
	 *
	 * @param string $classes Existing classes.
	 *
	 * @return string Modified classes.
	 */
	public function add_body_class( $classes ) {
		global $pagenow, $post;

		if ( 'post.php' === $pagenow && $post->post_type === $this->post_type ) {
			$classes .= ' post-' . $post->ID;
		}

		return $classes;
	}

	/**
	 * Show all sermons.
	 *
	 * @param int    $default   Existing default amount.
	 * @param string $post_type The post type.
	 *
	 * @return int Max sermon count.
	 */
	public function adjust_sermons_per_page( $default, $post_type ) {
		if ( $this->post_type === $post_type ) {
			return 9999;
		}

		return $default;
	}

	/**
	 * Adds the required actions.
	 */
	protected function _actions() {
		// Register post type.
		add_action( 'init', array( $this, 'register_post_type' ) );

		// Render table columns in "Podcasting" view.
		add_action( 'manage_' . $this->post_type . '_posts_custom_column', array(
			$this,
			'render_podcast_columns',
		), 2 );
		// Add custom row actions to podcasts in "Podcasting" view.
		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 100, 2 );
		// Save podcast data.
		add_action( 'save_post', array( $this, 'save' ) );
		// Create a default podcast if it doesn't exist.
		add_action( 'init', array( $this, 'maybe_create_default_podcast' ), 20 );
		// Remove podcast when podcasts is deleted.
		add_action( 'post_action_p_delete', array( $this, 'delete_podcast' ) );
		// Renders the permalink in the Add New/Edit screen.
		add_action( 'edit_form_before_permalink', array( $this, 'render_permalink' ) );
	}

	/**
	 * Changes the actual post permalink, not just the display.
	 *
	 * @param string   $post_link The post's permalink.
	 * @param \WP_Post $post      The post in question.
	 * @param bool     $leavename Whether to keep the post slug.
	 * @param bool     $sample    Is it a sample permalink.
	 *
	 * @return string The modified permalink.
	 */
	public function change_actual_permalink( $post_link, $post, $leavename, $sample ) {
		if ( $post->post_type !== $this->post_type ) {
			return $post_link;
		}

		$link               = site_url( '/?feed=rss2&post_type=wpfc_sermon' );
		$link_with_id       = $link . '&id=' . $post->ID;
		$is_default_podcast = Plugin::instance()->podcasting_manager->get_default_podcast_id() === $post->ID;

		// To make the link editable, use "%post_type%" where dynamic part should be, if $sample is true.
		return $is_default_podcast ? $link : $link_with_id;
	}

	/**
	 * Renders the permalink in the Add New/Edit screen.
	 *
	 * @param \WP_Post $post The post in question.
	 *
	 * @since 1.0.0-beta.8
	 */
	public function render_permalink( $post ) {
		if ( $post->post_type !== $this->post_type ) {
			return;
		}

		echo get_sample_permalink_html( $post->ID );
	}

	/**
	 * Callback for deleting a Podcast.
	 *
	 * @param int $id Podcast to delete.
	 */
	public function delete_podcast( $id ) {
		$post = get_post( $id );
		if ( ! $post ) {
			return;
		}

		if ( $this->post_type !== $post->post_type ) {
			return;
		}

		wp_delete_post( $id, true );

		// Add a delete notice.
		Plugin::instance()->notice_manager->add_success( 'podcast_delete', 'Podcast successfully deleted.', 10, '', false, true );

		wp_redirect( admin_url( 'edit.php?post_type=wpfc_sm_podcast' ) );
		exit;
	}

	/**
	 * Add tutorial metabox.
	 *
	 * @since 1.0.0-beta.8
	 */
	public function add_tutorial_metabox() {
		add_meta_box( 'podcast_tutorial', __( 'Podcasting Tutorial', 'sermon-manager' ), array(
			$this,
			'render_tutorial_metabox',
		), $this->post_type, 'advanced', 'high' );
	}

	/**
	 * Render the tutorial metabox.
	 *
	 * @since 1.0.0-beta.8
	 */
	public function render_tutorial_metabox() {
		?>
		<p>Church Sermon Manager lets you create multiple podcasts for different categories of sermons. For example, you
			can have a default podcast where all sermons are contained, but then you can also create a separate podcast
			where only Youth Group sermons are located.</p>
		<p>Here are some tips:</p>
		<ul style="list-style: initial;padding-left: 20px;">
			<li>You will find the main podcast settings below this tutorial. By default each podcast you create will
				inherit the default podcast settings (the default data is shown with a gray color) however you can vary
				any information and filtering on the side to make a podcast different or unique.
			</li>
			<li>The title at the top of the screen represent the internal podcast title - some name that will be easy
				for you to remember what the podcast is used for. That title won't be shown publicly.
			</li>
			<li>The title that is in the main settings, named "Podcast Title" is the one that will be seen on iTunes and
				other RSS readers.
			</li>
			<li>On the right side of the screen, you can select which series/preachers/topics/etc this podcast should
				show. If you don't select any, it will act the same as the default podcast.
			</li>
			<li>To submit this podcast to iTunes, just grab the link that is under the title. In future, you will be
				able to create a custom slug for the podcast.
			</li>
		</ul>
		<p style="padding-bottom: 10px;">
			To hide this tutorial, click on "Screen Options" at the top right of the screen, and uncheck the checkbox
			named "Podcasting Tutorial" (or <a href="#" onclick="jQuery('#podcast_tutorial-hide').click()">click
				here</a>). To show it again, just check the checkbox.</p>
		<?php
	}

	/**
	 * Add main metabox with settings.
	 *
	 * @since 1.0.0-beta.8
	 */
	public function add_main_metabox() {
		add_meta_box( 'podcast_settings', __( 'Podcast settings', 'sermon-manager' ), array(
			$this,
			'render_main_metabox',
		), $this->post_type, 'advanced', 'high' );
	}

	/**
	 * Render the main metabox.
	 *
	 * @param \WP_Post $post Post object.
	 *
	 * @since 1.0.0-beta.8
	 */
	public function render_main_metabox( $post ) {
		$all_settings = apply_filters( 'sm_pro_get_podcasting_settings', array() );

		$values = array();

		foreach ( $all_settings as $setting_data => $setting ) {
			if ( ! isset( $setting['id'] ) ) {
				continue;
			}

			$setting += array(
				'default' => '',
			);

			$values[ $setting['id'] ] = Settings::get_setting( $post->ID, $setting['id'], $setting['default'] );
		}

		wp_enqueue_media();

		// Prefix the settings IDs, so we don't get caught with some other input.
		foreach ( $all_settings as &$setting ) {
			if ( in_array( $setting['type'], array( 'title', 'sectionend' ) ) ) {
				continue;
			}

			$setting['id'] = 'podcast_' . $setting['id'];
		}

		// Prefix the values.
		foreach ( $values as $id => $value ) {
			if ( in_array( $id, array( 'podcast_settings' ) ) ) {
				continue;
			}

			$values[ 'podcast_' . $id ] = $value;
			unset( $values[ $id ] );
		}

		// Output fields.
		\SM_Admin_Settings::output_fields( $all_settings, $values );

		?>
		<style>
			body.post-<?php echo Plugin::instance()->podcasting_manager->get_default_podcast_id(); ?> #delete-action {
				display: none;
			}
		</style>
		<script>
            let frame;

            function uploadImage(event) {
                if (frame) {
                    frame.open();
                    return;
                }

                frame = wp.media({
                    title: 'Select or Upload Cover Image',
                    button: {
                        text: 'Use this image',
                    },
                    library: {
                        type: ['image'],
                    },
                    multiple: false,
                });

                frame.on('select', function () {
                    let attachment = frame.state().get('selection').first().toJSON();

                    jQuery(event.target).prev().val(attachment.url);
                });

                frame.open();
            }

            jQuery('#upload_podcast_itunes_cover_image').on('click', uploadImage);
		</script>
		<?php
	}

	/**
	 * Registers Podcast post type.
	 */
	public function register_post_type() {
		register_post_type( $this->post_type, apply_filters( 'sm_pro_register_post_type_' . $this->post_type, array(
			'labels'            => array(
				'name'                  => __( 'Podcasts', 'sermon-manager' ),
				'singular_name'         => __( 'Podcast', 'sermon-manager' ),
				'all_items'             => __( 'Podcasts', 'sermon-manager' ),
				'menu_name'             => _x( 'Podcasting', 'menu', 'sermon-manager' ),
				'add_new'               => __( 'Add New Podcast', 'sermon-manager' ),
				'add_new_item'          => __( 'Add New Podcast', 'sermon-manager' ),
				'edit'                  => __( 'Edit', 'sermon-manager' ),
				'edit_item'             => __( 'Edit Podcast', 'sermon-manager' ),
				'new_item'              => __( 'New Podcast', 'sermon-manager' ),
				'view'                  => __( 'View Podcast', 'sermon-manager' ),
				'view_item'             => __( 'View Podcast', 'sermon-manager' ),
				'search_items'          => __( 'Search Podcast', 'sermon-manager' ),
				'not_found'             => __( 'No Podcasts found', 'sermon-manager' ),
				'not_found_in_trash'    => __( 'No Podcasts found in trash', 'sermon-manager' ),
				'featured_image'        => '', // not used.
				'set_featured_image'    => '', // not used.
				'remove_featured_image' => '', // not used.
				'use_featured_image'    => '', // not used.
				'insert_into_item'      => '', // not used.
				'uploaded_to_this_item' => '', // not used.
				'filter_items_list'     => __( 'Filter Podcasts', 'sermon-manager' ),
				'items_list_navigation' => __( 'Podcasts Navigation', 'sermon-manager' ),
				'items_list'            => __( 'Podcasts List', 'sermon-manager' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_in_menu'      => 'edit.php?post_type=wpfc_sermon',
			'query_var'         => false,
			'show_in_nav_menus' => false, // check.
			'supports'          => array(
				'title',
			),
		) ) );
	}

	/**
	 * Creates a default podcast item if none exist.
	 *
	 * @return bool True on success, false otherwise.
	 */
	public function maybe_create_default_podcast() {
		if ( ! $this->if_default_podcast_exists() ) {
			return $this->create_default_podcast();
		} else {
			return true;
		}
	}

	/**
	 * Checks if default podcast exists.
	 *
	 * @return bool True if exists, false otherwise.
	 */
	public function if_default_podcast_exists() {
		return (bool) Plugin::instance()->podcasting_manager->get_default_podcast_id();
	}

	/**
	 * Creates a default podcast.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function create_default_podcast() {
		if ( ! defined( 'SM_DOING_SAVE' ) ) {
			define( 'SM_DOING_SAVE', true ); // Override our function from trying to save a podcast from editor.
		}

		$id = wp_insert_post( array(
			'post_title'  => 'Default',
			'post_type'   => $this->post_type,
			'post_status' => 'publish',
		) );

		update_option( 'wpfc_sm_default_podcast', $id );

		return (bool) $id;
	}

	/**
	 * Replaces default WP columns with our custom ones in "Podcasts" view.
	 *
	 * @param array $columns Default columns.
	 *
	 * @return array Modified columns.
	 */
	public function podcast_columns( $columns ) {
		unset( $columns['cb'] );

		return $columns;
	}

	/**
	 * Renders custom columns in "Podcasts" view.
	 *
	 * @param string $column Column ID to render.
	 */
	public function render_podcast_columns( $column ) {
		global $post;

		if ( empty( $post->ID ) ) {
			return;
		}

		switch ( $column ) {
			default:
				$data = '';
				break;
		}

		if ( $data instanceof \WP_Error ) {
			$data = __( 'Error' );
		}

		echo $data;
	}

	/**
	 * Adds custom row actions to podcasting in "Podcasts" view.
	 *
	 * @param  array    $actions The existing actions.
	 * @param  \WP_Post $post    The current podcast.
	 *
	 * @return array The modified actions.
	 */
	public function row_actions( $actions, $post ) {
		if ( $this->post_type !== $post->post_type ) {
			return $actions;
		}

		// Get the post type.
		$post_type_object = get_post_type_object( $this->post_type );

		// If we are rendering default podcast.
		$default_podcast = Plugin::instance()->podcasting_manager->get_default_podcast_id() === $post->ID;

		// Remove quick edit.
		unset( $actions['inline hide-if-no-js'] );

		// Remove trash (so we can replace it with delete).
		unset( $actions['trash'] );

		if ( $default_podcast ) {
			$feed_url = site_url( '/?feed=rss2&post_type=wpfc_sermon' );
		} else {
			$feed_url = site_url( '/?feed=rss2&post_type=wpfc_sermon&id=' . $post->ID );
		}

		/* @noinspection HtmlUnknownTarget */
		$actions['view'] = sprintf(
			'<a href="%s" rel="bookmark" aria-label="%s">%s</a>',
			$feed_url,
			/* translators: %s: post title */
			esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $post->post_title ) ),
			__( 'View' )
		);

		if ( ! $default_podcast ) {
			// Add permanent delete action.
			/* @noinspection HtmlUnknownTarget */ // translators: %s Sermon title.
			$actions['delete'] = sprintf( '<a href="%s" class="submitdelete" aria-label="%s">%s</a>', admin_url( sprintf( $post_type_object->_edit_link . '&action=p_delete', $post->ID ) ), esc_attr( sprintf( __( 'Delete &#8220;%s&#8221; permanently' ), $post->post_title ) ), __( 'Delete Permanently' ) );
		}

		return $actions;
	}

	/**
	 * Save podcast data.
	 *
	 * @param int $post_id The post ID.
	 *
	 * @since 1.0.0-beta.8
	 */
	public function save( $post_id ) {
		global $wpdb;

		if ( defined( 'SM_DOING_SAVE' ) ) {
			return;
		}
		define( 'SM_DOING_SAVE', true );

		$post = get_post( $post_id );

		if ( $post->post_type !== $this->post_type ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) || 'auto-draft' === $post->post_status ) {
			return;
		}

		// Update basic post data.
		wp_update_post( array(
			'ID'          => $post_id,
			'post_status' => 'publish',
		) );

		// Update podcast settings.
		Settings::save( $post_id );

		// Clear caches.
		if ( apply_filters( 'sm_clear_feed_transients', true ) ) {
			/* @noinspection SqlNoDataSourceInspection */

			/* @noinspection SqlResolve */
			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_feed_%') OR `option_name` LIKE ('_transient_timeout_feed_%')" );
		}
	}


	/**
	 * Adds taxonomies for the Podcast
	 *
	 * @param  array $custom_types Custom post types.
	 *
	 * @return array
	 */
	public function add_taxonomy_podcast( $custom_types ) {

		// Add new custom post type.
		$custom_types[] = $this->post_type;

		return $custom_types;
	}
}

return new WP();
