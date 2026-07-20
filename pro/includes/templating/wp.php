<?php
/**
 * Used for overriding WP with our data for Templating.
 *
 * @since   2.0.4
 * @package SMP\Templating
 */

namespace SMP\Templating;

use SMP\Plugin;

defined( 'ABSPATH' ) or die;

/**
 * Class SM_Pro_Templating_WP
 *
 * @since 2.0.4
 */
class WP {
	/**
	 * Podcasting post type.
	 *
	 * @var    string
	 */
	private $post_type = 'wpfc_sm_template';

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

		// Replace default WP editor with our custom one.
		add_filter( 'replace_editor', array( $this, 'replace_editor' ), 10, 2 );
		// Replace table columns in "Templates" view.
		add_filter( 'manage_' . $this->post_type . '_posts_columns', array( $this, 'template_columns' ) );
		// Show all templates on the view.
		add_filter( 'edit_posts_per_page', array( $this, 'show_all_templates' ), 10, 2 );
		// Replace SM content with the Template.
		add_filter( 'wpfc_sermon_single_v2', function ( $output, $post ) {
			try {
				return Templating_Manager::render( 'single', $post ) ?: $output;
			} catch ( \RuntimeException $e ) {
				define( 'SMPRO_RENDER_ERROR', true );

				return '<div class="notice notice-error"><p><strong>Church Sermon Manager</strong>: Error in rendering the view, error message: "' . $e->getMessage() . '"</p></div>';
			}
		}, 10, 2 );

		add_filter( 'wpfc_sermon_excerpt_v2', function ( $output, $post, $args ) {
			
			try {
				if ( ! Templating_Manager::is_active() ) {
					return $output;
				} 

				// Additional class.
				$args['additional_class'] = isset( $GLOBALS['smp_in_sm_shortcode'] ) && true === $GLOBALS['smp_in_sm_shortcode'] ? 'wpfc-sermon wpfc-sermon-shortcode' : '';

				// Get shortcode overrides.
				if ( isset( $args['attributes'] ) ) {
					if ( ! isset( $args['settings'] ) ) {
						$args['settings'] = array();
					}

					if ( isset( $args['attributes']['columns'] ) ) {
						$args['settings']['layout_columns'] = $args['attributes']['columns'];
					}
				}

				$return = '';

				if ( is_tax() ) { 
					$return .= Templating_Manager::render( 'taxonomy', $post, $args ) ?: $output;
				} else {
					$return .= Templating_Manager::render( 'archive', $post, $args ) ?: $output;
				}

				$GLOBALS['smp_in_sm_shortcode'] = false;

				return $return;
			} catch ( \RuntimeException $e ) {
				define( 'SMPRO_RENDER_ERROR', true );

				return '<div class="notice notice-error"><p><strong>Church Sermon Manager</strong>: Error in rendering the view, error message: "' . $e->getMessage() . '"</p></div>';
			}
		}, 10, 3 );
		// Merge divs if templating is used and we are in SM shortcode.
		add_filter( 'sm_shortcode_sermons_single_output', function ( $output, $post, $args ) {
			if ( ! Templating_Manager::is_active() ) {
				return $output;
			}

			$GLOBALS['smp_in_sm_shortcode'] = true;

			return wpfc_sermon_excerpt_v2( true, $args );
		}, 10, 3 );
		// Prevents double calling of render function.
		add_filter( 'sm_shortcode_output_override', function ( $default ) {
			return Templating_Manager::is_active();
		} );
		// Add template setting to filtering.
		add_filter( 'render_wpfc_sorting_output', function ( $content ) {
			if ( $content ) {
				ob_start();
				?>
				<div class="sm-filtering">
					<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- editor form markup composed by the templating admin views. ?>
				</div>
				<?php
				return ob_get_clean();
			}

			return $content;
		} );

		// Add wrappers.
		add_filter( 'archive-wpfc_sermon-before-sermons', array( $this, 'add_wrapper_start' ) );
		add_filter( 'taxonomy-wpfc_bible_book-before-sermons', array( $this, 'add_wrapper_start' ) );
		add_filter( 'taxonomy-wpfc_preacher-before-sermons', array( $this, 'add_wrapper_start' ) );
		add_filter( 'taxonomy-wpfc_sermon_series-before-sermons', array( $this, 'add_wrapper_start' ) );
		add_filter( 'taxonomy-wpfc_sermon_topics-before-sermons', array( $this, 'add_wrapper_start' ) );
		add_filter( 'taxonomy-wpfc_service_type-before-sermons', array( $this, 'add_wrapper_start' ) );
		add_filter( 'smp/shortcodes/wordpress/archive/before_loop', array( $this, 'add_wrapper_start' ) );
		add_filter( 'archive-wpfc_sermon-after-sermons', array( $this, 'add_wrapper_end' ) );
		add_filter( 'taxonomy-wpfc_bible_book-after-sermons', array( $this, 'add_wrapper_end' ) );
		add_filter( 'taxonomy-wpfc_preacher-after-sermons', array( $this, 'add_wrapper_end' ) );
		add_filter( 'taxonomy-wpfc_sermon_series-after-sermons', array( $this, 'add_wrapper_end' ) );
		add_filter( 'taxonomy-wpfc_sermon_topics-after-sermons', array( $this, 'add_wrapper_end' ) );
		add_filter( 'taxonomy-wpfc_service_type-after-sermons', array( $this, 'add_wrapper_end' ) );
		add_filter( 'smp/shortcodes/wordpress/archive/after_loop', array( $this, 'add_wrapper_end' ) );
	}

	/**
	 * Shows all templates in templating.
	 *
	 * @param int    $default   The default post count.
	 * @param string $post_type The post type.
	 *
	 * @return int Modified post count.
	 */
	public function show_all_templates( $default, $post_type ) {
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
		// Render table columns in "Templates" view.
		add_action( 'manage_' . $this->post_type . '_posts_custom_column', array(
			$this,
			'render_template_columns',
		), 2 );
		// Add custom row actions to templates in "Templates" view.
		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 100, 2 );
		// Make the template default when the action is clicked.
		add_action( 'post_action_t_default', array( $this, 'switch_to_template' ) );
		// Duplicate the template when the action is clicked.
		add_action( 'post_action_t_duplicate', array( $this, 'duplicate_the_template' ) );
		// Remove Template files when Template is deleted.
		add_action( 'post_action_t_delete', array( $this, 'delete_template' ) );
		// Re-scan for templates.
		add_action( 'load-post-new.php', array( $this, 'rescan_templates' ) );
		// Save template data on post save action.
		add_action( 'save_post_' . $this->post_type, array( $this, 'save' ) );
		// Enqueue scripts and styles always.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		// Create a default template if it doesn't exist.
		add_action( 'init', array( $this, 'maybe_create_default_template' ), 20 );
		// Enqueue a notice if saving was successful.
		add_action( 'sm_pro_templating_after_settings_save', array( $this, 'enqueue_success_notice' ) );
	}

	/**
	 * Adds start wrapper to Pro sermons.
	 *
	 * @return string
	 *
	 * @since 1.0.0-beta.6
	 */
	public function add_wrapper_start() {
		$settings = Settings::get_settings();
		$settings += array(
			'masonry_layout' => '',
		);

		return '<div class="smpro-items-container ' . ( 'yes' === $settings['masonry_layout'] ? 'smpro-masonry-layout grid js-masonry"' . ' data-masonry=\'{ "gutter": 24 }\' ' : '"' ) . '>';
	}

	/**
	 * Adds end wrapper to Pro sermons.
	 *
	 * @return string
	 *
	 * @since 1.0.0-beta.6
	 */
	public function add_wrapper_end() {
		return '</div>';
	}

	/**
	 * Enqueues a success notice to show up if settings saving was successful.
	 */
	public function enqueue_success_notice() {
		update_option( 'sm_templating_saved_notice', 1 );
	}

	/**
	 * Enqueues scripts and styles, in all frontend views.
	 */
	public function enqueue() {
		if ( ! Templating_Manager::is_active() ) {
			return;
		}

		$template = Templating_Manager::get_active_template();
		
		/*echo $template->path; echo 'mohit gautam'; die();*/ 
		foreach ( scandir( $template->path ) as $file ) {
			switch ( pathinfo( $template->path . '/' . $file, PATHINFO_EXTENSION ) ) {
				case 'css':
					wp_enqueue_style( 'sm_pro_templating_' . basename( $template->path ), $template->url . '/' . $file, array(), $template->version );
					break;
				case 'js':
					wp_enqueue_script( 'sm_pro_templating' . basename( $template->path ), $template->url . '/' . $file, array(), $template->version );
					break;
			}
		}
	}

	/**
	 * Replaces default editor with our custom one.
	 *
	 * @param boolean  $false The default value.
	 * @param \WP_Post $post  The post.
	 *
	 * @return boolean Returns the value that is set for $default (false)
	 */
	public function replace_editor( $false, $post ) {
		if ( $post instanceof \WP_Post && $this->post_type === $post->post_type ) {
			require_once SMP_PATH . 'includes/templating/views/html-editor.php';

			return true;
		}

		return $false;
	}

	/**
	 * Registers Template post type.
	 */
	public function register_post_type() {
		register_post_type( $this->post_type, apply_filters( 'sm_pro_register_post_type_' . $this->post_type, array(
			'labels'            => array(
				'name'                  => __( 'Templates', 'church-sermon-manager' ),
				'singular_name'         => __( 'Template', 'church-sermon-manager' ),
				'all_items'             => __( 'Templates', 'church-sermon-manager' ),
				'menu_name'             => _x( 'Templates', 'menu', 'church-sermon-manager' ),
				'add_new'               => __( 'Re-scan templates', 'church-sermon-manager' ),
				'add_new_item'          => __( 'Add New Template', 'church-sermon-manager' ),
				'edit'                  => __( 'Edit', 'church-sermon-manager' ),
				'edit_item'             => __( 'Edit Template', 'church-sermon-manager' ),
				'new_item'              => __( 'New Template', 'church-sermon-manager' ),
				'view'                  => __( 'View Template', 'church-sermon-manager' ),
				'view_item'             => __( 'View Template', 'church-sermon-manager' ),
				'search_items'          => __( 'Search Template', 'church-sermon-manager' ),
				'not_found'             => __( 'No Templates found', 'church-sermon-manager' ),
				'not_found_in_trash'    => __( 'No Templates found in trash', 'church-sermon-manager' ),
				'featured_image'        => '', // not used.
				'set_featured_image'    => '', // not used.
				'remove_featured_image' => '', // not used.
				'use_featured_image'    => '', // not used.
				'insert_into_item'      => '', // not used.
				'uploaded_to_this_item' => '', // not used.
				'filter_items_list'     => __( 'Filter Templates', 'church-sermon-manager' ),
				'items_list_navigation' => __( 'Templates Navigation', 'church-sermon-manager' ),
				'items_list'            => __( 'Templates List', 'church-sermon-manager' ),
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
	 * Creates a default template if it doesn't exist.
	 *
	 * @return bool True on success, false otherwise.
	 */
	public function maybe_create_default_template() {
		if ( ! $this->if_default_template_exists() ) {
			return $this->create_default_template();
		} else {
			return true;
		}
	}

	/**
	 * Checks if default template exists.
	 *
	 * @return bool True if exists, false otherwise.
	 */
	public function if_default_template_exists() {
		global $wpdb;

		/* @noinspection SqlNoDataSourceInspection */

		/* @noinspection SqlResolve */
		return (bool) $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = %s", array(
			'Sermon Manager',
			$this->post_type,
		) ) );
	}

	/**
	 * Creates a default template, i.e. the way to use SM views.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function create_default_template() {
		if ( ! defined( 'SM_DOING_SAVE' ) ) {
			define( 'SM_DOING_SAVE', true ); // Override our function from trying to save a template from editor.
		}

		return (bool) wp_insert_post( array(
			'post_title' => 'Sermon Manager',
			'post_type'  => $this->post_type,
		) );
	}

	/**
	 * Replaces default WP columns with our custom ones in "Templates" view.
	 */
	public function template_columns() {
		$columns               = array();
		$columns['t_title']    = 'Template Title';
		$columns['t_default']  = 'Default';
		$columns['t_template'] = 'Template';
		$columns['t_version']  = 'Version';
		$columns['t_date']     = 'Creation Date';
		$columns['t_author']   = 'Author';

		return $columns;
	}

	/**
	 * Renders custom columns in "Templates" view.
	 *
	 * @param string $column Column ID to render.
	 */
	public function render_template_columns( $column ) {
		global $post;

		if ( empty( $post->ID ) ) {
			return;
		}

		$template = Templating_Manager::get_template( $post->ID );

		switch ( $column ) {
			case 't_title':
				if ( Templating_Manager::get_default_template_id() === $post->ID ) {
					$data = sprintf(
						'<a class="row-title">%s%s%s</a> %s',
						( $template->is_invalid ? '<s>' : ' ' ),
						$post->post_title,
						( $template->is_invalid ? '</s>' : ' ' ),
						( $template->is_invalid ? '(Invalid)' : ' ' )
					);
				} else {
					/* @noinspection HtmlUnknownTarget */
					$data = sprintf(
						'<a class="row-title" href="%s">%s%s%s</a> %s',
						get_edit_post_link( $post->ID ),
						( $template->is_invalid ? '<s>' : ' ' ),
						$post->post_title,
						( $template->is_invalid ? '</s>' : ' ' ),
						( $template->is_invalid ? '(Invalid)' : ' ' )
					);
				}

				break;
			case 't_default':
				$post_type_object = get_post_type_object( $this->post_type );
				$default_link     = add_query_arg( 'action', 't_default', admin_url( sprintf( $post_type_object->_edit_link, $post->ID ) ) );

				$data = $template->is_default ? '<span class="dashicons dashicons-star-filled"></span>' :
					'<a href="' . $default_link . '" aria-label="Make &#8220;' . $post->post_title . '&#8221; as default template"><span class="dashicons dashicons-star-empty"></span></a>';
				break;
			case 't_template':
				$data = basename( $template->path );
				break;
			case 't_version':
				$data = $template->version;
				break;
			case 't_date':
				$data = $template->date_created;
				break;
			case 't_author':
				$data = $template->author;
				break;
			default:
				$data = '';
				break;
		}

		if ( $data instanceof \WP_Error ) {
			$data = __( 'Error', 'church-sermon-manager' );
		}

		echo esc_html( $data );
	}

	/**
	 * Adds custom row actions to templates in "Templates" view.
	 *
	 * @param array    $actions The existing actions.
	 * @param \WP_Post $post    The current template.
	 *
	 * @return array The modified actions.
	 */
	public function row_actions( $actions, $post ) {
		if ( $this->post_type === $post->post_type ) {
			$post_type_object = get_post_type_object( $this->post_type );

			if ( Templating_Manager::get_default_template_id() !== $post->ID ) {
				/* @noinspection HtmlUnknownTarget */
				$actions = array(
					'id'        => 'ID: ' . $post->ID,
					'duplicate' => sprintf(
						'<a href="%s" aria-label="%s">%s</a>',
						admin_url( sprintf( $post_type_object->_edit_link . '&action=t_duplicate', $post->ID ) ),
						// translators: %s Sermon title.
						esc_attr( sprintf( __( 'Duplicate &#8220;%s&#8221;', 'church-sermon-manager' ), $post->post_title ) ),
						__( 'Duplicate', 'church-sermon-manager' )
					),
					'edit'      => sprintf(
						'<a href="%s" aria-label="%s">%s</a>',
						get_edit_post_link( $post->ID ),
						// translators: %s Sermon title.
						esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;', 'church-sermon-manager' ), $post->post_title ) ),
						__( 'Edit', 'church-sermon-manager' )
					),
					'delete'    => sprintf(
						'<a href="%s" class="submitdelete" aria-label="%s">%s</a>',
						admin_url( sprintf( $post_type_object->_edit_link . '&action=t_delete', $post->ID ) ),
						// translators: %s Sermon title.
						esc_attr( sprintf( __( 'Delete &#8220;%s&#8221; permanently', 'church-sermon-manager' ), $post->post_title ) ),
						__( 'Delete Permanently', 'church-sermon-manager' )
					),
				);
			} else {
				$actions = array(
					'id' => 'ID: ' . $post->ID,
				);
			}
		}

		return $actions;
	}

	/**
	 * Callback for Template duplication.
	 *
	 * @param int $id Template to duplicate.
	 */
	public function duplicate_the_template( $id ) {
		try {
			Templating_Manager::duplicate_template( $id );

			Plugin::instance()->notice_manager->add_success( 'templating_duplicate_success', 'Template successfully duplicated.', 10, 'templating' );
		} catch ( \Exception $e ) {
			Plugin::instance()->notice_manager->add_error( 'template_duplicating_fail', 'Failed duplicating the template. ' . $e->getMessage(), 10, 'templating' );
		}

		wp_safe_redirect( admin_url( 'edit.php?post_type=' . $this->post_type ) );
		exit;
	}

	/**
	 * Callback for setting Template as default.
	 *
	 * @param int $id Template to set as default.
	 */
	public function switch_to_template( $id ) {
		$success = Templating_Manager::switch_to_template( $id );
		if ( $success ) {
			Plugin::instance()->notice_manager->add_success( 'templating_switch_success', 'Successfully switched the template.', 10, 'templating' );
		} else {
			Plugin::instance()->notice_manager->add_error( 'templating_switch_fail', 'Failed to switch to the template.', 10, 'templating' );
		}

		wp_safe_redirect( admin_url( 'edit.php?post_type=' . $this->post_type ) );
		exit;
	}

	/**
	 * Callback for rescanning for Templates.
	 */
	public function rescan_templates() {
		if ( ! isset( $_GET['post_type'] ) || $this->post_type !== $_GET['post_type'] ) {
			return;
		}

		$success = Templating_Manager::rescan_templates();
		if ( ! $success ) {
			Plugin::instance()->notice_manager->add_error( 'templating_rescan_fail', 'Failed rescanning the templates.', 10, 'templating' );
		}

		wp_safe_redirect( admin_url( 'edit.php?post_type=' . $this->post_type ) );
		exit;
	}

	/**
	 * Callback for deleting a Template.
	 *
	 * @param int $id Template to delete.
	 */
	public function delete_template( $id ) {
		$post = get_post( $id );
		if ( ! $post ) {
			return;
		}

		if ( $this->post_type !== $post->post_type ) {
			$error = 'Not right post type. Expected "' . $this->post_type . '", got "' . $post->post_type . '"';
			goto redirect; // phpcs:ignore
		}

		if ( ! Templating_Manager::delete_template( $id ) ) {
			$error = 'Failed to remove the directory.';
			goto redirect; // phpcs:ignore
		}

		if ( ! wp_delete_post( $id, true ) ) {
			$error = 'Failed to remove the post from database.';
			goto redirect; // phpcs:ignore
		}

		redirect: // phpcs:ignore
		if ( ! empty( $error ) ) {
			Plugin::instance()->notice_manager->add_error( 'templating_delete_fail', 'Failed to delete the template. ' . $error, 10, 'templating' );
			goto redirect; // phpcs:ignore
		} else {
			Plugin::instance()->notice_manager->add_success( 'templating_delete_success', 'Template removed.', 10, 'templating' );
		}

		wp_safe_redirect( admin_url( 'edit.php?post_type=' . $this->post_type ) );
		exit;
	}

	/**
	 * Saves Template data on save action.
	 *
	 * @param int $post_id The template post ID to save.
	 */
	public function save( $post_id ) {
		if ( defined( 'SM_DOING_SAVE' ) || empty( $_POST['title'] ) ) {
			return;
		}
		define( 'SM_DOING_SAVE', true );

		Settings::save( $post_id );

		wp_update_post( array(
			'ID'         => $post_id,
			'post_title' => $_POST['title'],
		) );
	}
}

return new WP();
