<?php
/**
 * Church Sermon Manager — Pro feature modules bootstrap.
 *
 * @since   2.0.4
 * @package SMP\Core
 */

namespace SMP;

defined( 'ABSPATH' ) or die;

use SMP\Podcasting\Podcasting_Manager;
use SMP\Shortcodes\Shortcodes_Manager;
use SMP\Templating\Settings as Templating_Settings;
use SMP\Templating\Templating_Manager;

/**
 * Main Plugin Class
 *
 * @since 2.0.4
 */
class Plugin {

	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since  2.0.4
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;

	/**
	 * Templating Manager.
	 *
	 * Holds the Templating Manager.
	 *
	 * @var Templating_Manager
	 */
	public $templating_manager;

	/**
	 * Shortcodes.
	 *
	 * Holds the shortcodes.
	 *
	 * @var Shortcodes_Manager
	 */
	public $shortcodes_manager;

	/**
	 * Notice Manager.
	 *
	 * Handles notices.
	 *
	 * @var Notice_Manager
	 */
	public $notice_manager;

	/**
	 * Install Manager.
	 *
	 * Handles first time install and update functions.
	 *
	 * @var Install_Manager
	 */
	public $install_manager;

	/**
	 * Podcasting Manager.
	 *
	 * Handles podcasting stuff.
	 *
	 * @var Podcasting_Manager
	 */
	public $podcasting_manager;

	/**
	 * Plugin constructor.
	 *
	 * @access private
	 */
	private function __construct() {
		// Register autoloader.
		if ( ! $this->_register_autoloader() ) {
			add_action(
				'admin_notices',
				function () {
					echo '<div class="notice notice-error"><p>Church Sermon Manager could not load its bundled libraries. Please reinstall the plugin, or <a href="https://github.com/riceguitar/Church-Sermon-Manager/issues" target="_blank">report the issue</a>.</p></div>';
				}
			);

			return;
		}

		// Divi page meta override.
		include_once SMP_PATH . 'includes/smp-divi-override.php';

		add_action( 'init', array( $this, 'init' ), 0 );

		// Register categories for Elementor
		include_once SMP_PATH . 'includes/shortcodes/elementor/register_categories.php';
	}

	/**
	 * Register autoloader.
	 *
	 * @access private
	 */
	private function _register_autoloader() {
		if ( ! file_exists( SMP_PATH . 'vendor/autoload.php' ) ) {
			return false;
		}

		require SMP_PATH . 'vendor/autoload.php';
		require SMP_PATH . 'includes/autoloader.php';

		Autoloader::run();

		return true;
	}

	/**
	 * Clone.
	 *
	 * Disable class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @access public
	 * @since  2.0.4
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'church-sermon-manager' ), '2.0.4' );
	}

	/**
	 * Wakeup.
	 *
	 * Disable unserializing of the class.
	 *
	 * @access public
	 * @since  1.0.0.beta.1
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'church-sermon-manager' ), '2.0.4' );
	}

	/**
	 * Init.
	 *
	 * Initialize the Pro feature modules.
	 */
	public function init() {
		$this->_include_files();
		$this->_init_components();
		$this->_add_actions();
		$this->_add_filters();

		if ( ! defined( 'POWERPRESS_POST_TYPES' ) ) {
			define( 'POWERPRESS_POST_TYPES', apply_filters( 'smp_powerpress_support', 'post,page,wpfc_sermon' ) ); // phpcs:ignore
		}

		/**
		 * Pro modules init.
		 *
		 * @since 2.0.4
		 */
		do_action( 'smp/init' );
	}

	/**
	 * Includes required files.
	 *
	 * @access private
	 */
	private function _include_files() {
		// Helper functions.
		require_once SMP_PATH . 'includes/smp-core-functions.php';
		require_once SMP_PATH . 'includes/smp-formatting-functions.php';
		require_once SMP_PATH . 'includes/smp-admin-functions.php';

		// Templating helpers.
		require_once SMP_PATH . 'includes/templating/functions.php';
		require_once SMP_PATH . 'includes/templating/wp.php';

		// Podcasting helpers.
		require_once SMP_PATH . 'includes/podcasting/functions.php';
		require_once SMP_PATH . 'includes/podcasting/wp.php';

		// Shortcode helpers.
		require_once SMP_PATH . 'includes/shortcodes/functions.php';
	}

	/**
	 * Init components.
	 *
	 * Initialize the Pro feature module components.
	 *
	 * @access private
	 */
	private function _init_components() {
		$this->notice_manager     = new Notice_Manager();
		$this->templating_manager = new Templating_Manager();
		$this->shortcodes_manager = new Shortcodes_Manager();
		$this->install_manager    = new Install_Manager();
		$this->podcasting_manager = new Podcasting_Manager();
	}

	/**
	 * Hooks into the required actions.
	 *
	 * @access private
	 */
	private function _add_actions() {
		// Enqueue assets that are supposed to show up on all pages in admin area.
		add_action(
			'admin_head',
			function () {
				wp_enqueue_script( 'sm_pro_notices', SMP_URL . 'assets/js/dismiss-notice.js', array(), SMP_VERSION, true );
			}
		);

		// Enqueue assets that are supposed to show up only on Sermon Manager pages.
		add_action(
			'sm_enqueue_admin_css',
			function () {
				wp_enqueue_style( 'sm_pro_admin_style', SMP_URL . 'assets/css/admin/admin.css', array(), SMP_VERSION );
				wp_enqueue_style( 'sm_pro_templating', SMP_URL . 'assets/css/admin/templating.min.css', array(), SMP_VERSION );
				wp_enqueue_style( 'sm_pro_podcasting', SMP_URL . 'assets/css/admin/podcasting.min.css', array(), SMP_VERSION );
				wp_enqueue_style( 'sm_pro_templating_color_picker', SMP_URL . 'assets/vendor/colorpicker/css/colorpicker.css', array(), SMP_VERSION );

				wp_enqueue_script(
					'sm_pro_templating',
					SMP_URL . 'assets/js/templating.js',
					array(
						'jquery',
						'jquery-ui-tabs',
						'sm_pro_templating_color_picker',
					),
					SMP_VERSION,
					false
				);

				wp_enqueue_script(
					'sm_pro_templating_color_picker',
					SMP_URL . 'assets/vendor/colorpicker/js/colorpicker.js',
					array(
						'jquery',
					),
					SMP_VERSION,
					false
				);

				wp_localize_script( 'sm_pro_templating', 'sm_pro_templating_params', array() );

				wp_enqueue_style( 'sm_pro_settings_style', SMP_URL . 'assets/css/admin/settings.min.css', array(), SMP_VERSION );
				wp_enqueue_script( 'sm_pro_settings_js', SMP_URL . 'assets/js/settings.js', array(), SMP_VERSION, false );
			}
		);

		// Enqueue assets that are supposed to show up only on frontend.
		add_action(
			'sm_enqueue_css',
			function () {
				// Get SM PRO settings.
				$smpro_settings       = Templating_Settings::get_settings();
				$smpro_layout_columns = intval( $smpro_settings['layout_columns'] );

				wp_enqueue_style( 'sm_pro_frontend_style', SMP_URL . 'assets/css/frontend.min.css', array(), SMP_VERSION );
				wp_add_inline_style( 'sm_pro_frontend_style', wp_sprintf( '.smpro-items-container, .smpro-items {--smpro-layout-columns: %s}', $smpro_layout_columns ) );

				wp_enqueue_script( 'sm_pro_masonry_js', SMP_URL . 'assets/js/masonry.js', array(), SMP_VERSION, false );
			}
		);

		// Replace RefTagger with advanced version.
		add_action(
			'sm_enqueue_js',
			function () {
				if ( ! Templating_Manager::is_active() ) {
					return;
				}

				wp_deregister_script( 'wpfc-sm-verse-script' );

				wp_register_script( 'wpfc-sm-verse-script', SMP_URL . 'assets/js/verse.js', array(), SMP_VERSION, false );
			}
		);

		// Filters the verse JS variables.
		add_filter(
			'sm_verse_popup_data',
			function ( $verse ) {
				if ( ! Templating_Manager::is_active() ) {
					return $verse;
				}

				$sm_bible_version = $verse['bible_version'];
				unset( $verse['bible_version'] );

				$settings = Templating_Settings::get_settings();

				$socials = array(
					'twitter',
					'facebook',
					'googleplus',
					'faithlife',
				);

				foreach ( $socials as $key => $social ) {
					if ( empty( $settings[ 'bible_' . $social ] ) || 'no' === $settings[ 'bible_' . $social ] ) {
						unset( $socials[ $key ] );
					}
				}

				$verse['settings'] = array(
					'bibleVersion'       => $sm_bible_version,
					'dropShadow'         => $settings['bible_drop_shadow'],
					'roundCorners'       => $settings['bible_rounded_corners'],
					'socialSharing'      => $socials,
					'bibleReader'        => $settings['bible_reader'],
					'noSearchTagNames'   => ( $settings['bible_exclude_heading'] ? array_map( 'trim', explode( ',', $settings['bible_exclude_heading'] ) ) : false ) ?: false,
					'noSearchClassNames' => ( $settings['bible_exclude_classes'] ? array_map( 'trim', explode( ',', $settings['bible_exclude_classes'] ) ) : false ) ?: false,
					'logosLinkIcon'      => $settings['bible_logos_color'],
					'addLogosLink'       => $settings['bible_logos'],
					'useTooltip'         => $settings['bible_tooltip'],
					'linksOpenNewWindow' => $settings['bible_new_window'],
					'caseInsensitive'    => $settings['bible_case_sensitivity'],
					'convertHyperlinks'  => $settings['bible_existing_biblia'],
					'tagChapters'        => $settings['bible_chapter_level_tagging'],
					'tooltipStyle'       => $settings['bible_body_background_color'],
					'customStyle'        => array(
						'heading' => array(
							'fontFamily'      => $settings['bible_heading_font_family'],
							'fontSize'        => $settings['bible_heading_font_size'],
							'color'           => $settings['bible_heading_font_color'],
							'backgroundColor' => $settings['bible_heading_background_color'],
						),
						'body'    => array(
							'fontFamily' => $settings['bible_body_font_family'],
							'fontSize'   => $settings['bible_body_font_size'],
							'color'      => $settings['bible_body_font_color'],
						),
					),
				);

				// Convert value from yes|on|no to bool.
				foreach ( $verse['settings'] as $key => &$setting ) {
					$setting = smp_string_to_bool( $setting, false );
				}

				$verse['settings'] = array_filter( $verse['settings'] );

				return $verse;
			}
		);

		// Maybe disable verses.
		add_filter(
			'verse_popup_disable',
			function ( $default ) {
				if ( Templating_Manager::is_active() && ! smp_string_to_bool( Templating_Settings::get_setting( 'bible_enable' ) ) ) {
					return true;
				}

				return $default;
			}
		);

		// Check if there are any notices to show.
		add_action(
			'admin_notices',
			function () {
				$notices = Plugin::instance()->notice_manager->get_notices();

				foreach ( $notices as $priority => $notices_group ) {
					foreach ( $notices_group as $id => $notice ) {
						if ( $notice['seen'] ) {
							continue;
						}

						?>
						<div class="notice smp-notice notice-<?php echo esc_attr( $notice['type'] ); ?> <?php echo true === $notice['preserve'] ? 'is-dismissible' : ''; ?>"
								id="smp-notice-<?php echo esc_attr( $notice['id'] ); ?>">
							<p>
								<?php if ( ! $notice['hide_plugin_name'] ) : ?>
									<strong><?php echo esc_html__( 'Church Sermon Manager', 'church-sermon-manager' ); ?></strong>&nbsp;
								<?php endif; ?>
								<?php echo $notice['message']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- notice HTML composed by the plugin's notice manager. ?>
							</p>
							<?php
							// Set as seen if it's single show only.
							if ( false === $notice['preserve'] ) {
								Plugin::instance()->notice_manager->set_seen( $notice['id'] );
							}
							?>
						</div>
						<?php
					}
				}
			}
		);

		// Check if there are any template updates.
		add_action(
			'admin_notices',
			function () {
				$smp_new_templates = get_option( 'smp_new_templates', array(), true );

				if ( ! empty( $smp_new_templates ) ) {
					?>
					<div class="notice notice-info is-dismissible">
						<h3 style="margin:0">Church Sermon Manager</h3>
						<p><?php echo esc_html__( 'Hi there! There are new versions available for some of installed templates:', 'church-sermon-manager' ); ?></p>
						<table class="template-versions">
							<tr>
								<th style="text-align: left;">Name</th>
								<th>Installed version</th>
								<th>New version</th>
							</tr>
							<?php foreach ( $smp_new_templates as $name => $versions ) : ?>
								<tr>
									<td style="text-align: left;"><?php echo esc_html( $name ); ?></td>
									<td><?php echo esc_html( $versions['old_version'] ); ?></td>
									<td><?php echo esc_html( $versions['new_version'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						</table>
						<p>
							<a class="button button-primary"
									href="<?php echo esc_url( admin_url( 'edit.php?post_type=wpfc_sm_template&doaction=updateall' ) ); ?>">
								Update all</a>
							<span style="color: #bbb; font-style: italic;">Only the listed templates will be affected. Copies will not be modified or removed. The settings of all templates will be preserved.</span>
						</p>
					</div>
					<style>
						table.template-versions th, table.template-versions td {
							text-align: center;
							min-width: 80px;
							padding: 0;
							margin: 0;
						}
					</style>
					<?php
				}
			}
		);

		// Handler for dismissing error notice.
		add_action(
			'wp_ajax_smp_notice_handler',
			function () {
				if ( isset( $_POST['id'] ) ) {
					echo Plugin::instance()->notice_manager->set_seen( sanitize_title( str_replace( 'smp-notice', '', $_POST['id'] ) ) ) ? 1 : 0;
				} else {
					echo 0;
				}

				exit;
			}
		);

		// Replace our player with Blubrry's one.
		add_action(
			'sm_audio_player',
			function ( $content ) {
				if ( defined( 'POWERPRESS_VERSION' ) && \SermonManager::getOption( 'blubrry_powerpress_player' ) ) {
					return do_shortcode( '[powerpress]' );
				}

				return $content;
			}
		);

		// Add download button for Plyr player.
		add_filter(
			'sm_audio_player',
			function ( $output, $source, $source_ori ) {

				$settings = Templating_Settings::get_settings();

				if ( ! empty( $settings['show_download_button'] ) && 'yes' === $settings['show_download_button'] ) {

					$player = strtolower( \SermonManager::getOption( 'player' ) ?: 'plyr' );

					if ( strtolower( $player ) === 'plyr' ) {

						$extra_settings = '';

						$seek = wpfc_get_media_url_seconds( $source );

						if ( is_numeric( $seek ) ) {
							// Sanitation just in case.
							$extra_settings = 'data-plyr_seek=\'' . intval( $seek ) . '\'';
						}

						$config_download = array(
							'urls'     => array(
								'download' => $source,
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


						$output = '';

						$output .= '<audio controls preload="metadata" class="wpfc-sermon-player ' . ( 'mediaelement' === $player ? 'mejs__player' : '' ) . '" ' . $extra_settings . '>';
						$output .= '<source src="' . $source . '" type="audio/mp3">';
						$output .= '</audio>';
					}
				}



				return $output;
			},
			10,
			4
		);

		// Remove description editor in SM.
		add_action(
			'sm_cmb2_meta_fields',
			function ( $sermon_details_meta ) {
				/**
				 * Sermon Details meta box.
				 *
				 * @var $sermon_details_meta \CMB2
				 */

				$sermon_details_meta->remove_field( 'sermon_description' );
			}
		);

		// Hijack default editor to use our meta description.
		add_action(
			'edit_form_after_title',
			function () {
				global $post;

				if ( get_post_type() !== 'wpfc_sermon' ) {
					return;
				}

				/**
				 * The sermon.
				 *
				 * @var $post \WP_Post
				 */

				$GLOBALS['sm_post_content'] = $post->post_content; // phpcs:ignore

				// Show the description meta in the editor, falling back to post_content
				// for sermons saved while this hijack was inactive (e.g. Pro deactivated).
				// In-memory swap only — never write to the database on an edit-screen load.
				$description = get_post_meta( $post->ID, 'sermon_description', true );
				if ( '' !== trim( (string) $description ) ) {
					$post->post_content = $description;
				}
			}
		);

		// Revert the post content.
		// add_action(
		// 	'edit_form_after_editor',
		// 	function () {
		// 		global $post;

		// 		if ( get_post_type() !== 'wpfc_sermon' ) {
		// 			return;
		// 		}

		// 		if ( isset( $GLOBALS['sm_post_content'] ) ) {
		// 			$post->post_content = $GLOBALS['sm_post_content'];
		// 			error_log('4444444');
		// 			error_log(print_r($post->post_content,true));
		// 		}
		// 	}
		// );

		// Update sermon description and content.
		add_action(
			'wp_insert_post',
			function ( $post_ID ) {
				if ( ! isset( $_POST['content'] ) || 'wpfc_sermon' !== get_post_type( $post_ID ) ) {
					return;
				}			
				update_post_meta( $post_ID, 'sermon_description', $_POST['content'] );
				
				// Update date mapping.
				smp_update_sermon_date_mapping( $post_ID );
			}
		);
	}

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since  2.0.4
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();

			/**
			 * Pro modules loaded.
			 *
			 * Fires when plugin was fully loaded and instantiated.
			 *
			 * @since 2.0.4
			 */
			do_action( 'smp/loaded' );
		}

		return self::$instance;
	}

	/**
	 * Hooks into the required filters.
	 *
	 * @access private
	 */
	private function _add_filters() {
		// Enable the block/classic editor for sermons.
		add_filter(
			'sm_register_post_type_wpfc_sermon',
			function ( $args ) {
				$args['supports'][] = 'editor';

				return $args;
			}
		);

		// Add SM Pro screen ids.
		add_filter(
			'sm_screen_ids',
			function ( $screen_ids ) {
				$screen_ids_pro = array(
					'edit-wpfc_sm_template',
					'wpfc_sm_template',
					'edit-wpfc_sm_podcast',
					'wpfc_sm_podcast',
				);

				return array_merge( $screen_ids, $screen_ids_pro );
			}
		);

		// Add SM Pro classes.
		add_filter(
			'sm_templates_additional_classes',
			function ( $classes ) {
				if ( is_archive() ) {
					$classes[] = 'smpro-items';
				}

				return $classes;
			}
		);

		// Add Divi Builder compatibility.
		add_filter(
			'et_builder_third_party_post_types',
			function ( $post_types ) {
				if ( ! isset( $post_types['wpfc_sermon'] ) ) {
					$post_types[] = 'wpfc_sermon';
				}

				return $post_types;
			}
		);

		// Note: a wp_insert_post_data filter used to freeze post_content to its
		// previous DB value on every sermon save. Removed 2026-07-14 so the editor
		// content is written to post_content normally; the wp_insert_post hook above
		// mirrors it into the sermon_description meta, keeping both in sync.

		// Note: Gutenberg enable/disable for sermons is governed by the core
		// "sermonmanager_disable_gutenberg_block_editor" option (see the main
		// plugin file); the hard-disable filters that lived here were removed.

		// Add custom enclosure tag to the feed item.
		add_filter(
			'wpfc-podcast-feed-custom-enclosure',
			function ( $output, $post_id, $settings ) {
				unset( $output );

				$video_url = get_post_meta( $post_id, 'sermon_video_link', true );

				if ( empty( $video_url ) ) {
					return false;
				}

				$mime_type = array(
					'mov' => 'video/quicktime',
					'wmv' => 'video/wmv',
					'mp4' => 'video/mp4',
				);

				$head   = array_change_key_case( get_headers( $video_url, 1 ) );
				$length = ! empty( $head['content-length'] ) ? intval( $head['content-length'] ) : 0;

				$ext = pathinfo( $video_url, PATHINFO_EXTENSION );

				// Audio sermons only.
				if ( empty( $settings['sermons_to_show'] ) ) {
					return false;
				}

				// Audio sermons priority.
				if ( 'audio_priority' === $settings['sermons_to_show'] && ! empty( $ext ) ) {
					return false;
				}

				ob_start();
				?>
				<!--suppress CheckEmptyScriptTag -->
				<enclosure url="<?php echo esc_url( $video_url ); ?>"
						length="<?php echo esc_attr( $length ); ?>"
						type="<?php echo esc_attr( $mime_type[ $ext ] ); ?>"/>
				<?php
				return ob_get_clean();

			},
			10,
			3
		);
	}
}

return Plugin::instance();
