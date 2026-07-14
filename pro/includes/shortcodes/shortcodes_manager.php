<?php
/**
 * This file contains main initialization code for shortcodes.
 *
 * @since   2.0.4
 * @package SMP\Shortcodes
 */

namespace SMP\Shortcodes;

use \Elementor\Controls_Manager;
use \Elementor\Elements_Manager;
use SMP\Shortcodes\Elementor\Control_Choices;
use SMP\Shortcodes\Elementor\Group_Control_Sermons;
use SMP\Shortcodes\Elementor\Query;

defined( 'ABSPATH' ) or exit;

/**
 * Define the class.
 */
class Shortcodes_Manager {

	/**
	 * Constructor.
	 *
	 * We might remove this in future and leave only the individual controls, so this class can act as an actual
	 * manager.
	 */
	public function __construct() {
		$this->init_elementor();
		$this->init_wordpress();
		$this->init_beaver();
		$this->init_vc();
		$this->init_gutenberg();

		add_action( 'et_builder_framework_loaded', array( $this, 'init_divi' ) );
	}

	/**
	 * Loads Elementor related functionality.
	 */
	public function init_elementor() {
		// Create controls.
		add_action( 'elementor/controls/register', function ( Controls_Manager $controls_manager ) {
			require_once __DIR__ . '/elementor/group-control-sermons.php';
			require_once __DIR__ . '/elementor/control-choices.php';

			require_once __DIR__ . '/elementor/control-query.php';
			require_once __DIR__ . '/elementor/module-base.php';
			require_once __DIR__ . '/elementor/module.php';

			$controls_manager->register( new Query() );
			$controls_manager->register( new Control_Choices() );
			/* @noinspection PhpParamsInspection */
			$controls_manager->add_group_control( Group_Control_Sermons::get_type(), new Group_Control_Sermons() );
		} );

		// Do widget includes.
		add_action( 'elementor/widgets/register', function () {
			require_once __DIR__ . '/elementor/skin-base.php';
			require_once __DIR__ . '/elementor/skin-cards.php';

			// Define skins.
			require_once __DIR__ . '/elementor/skin-classic.php';
			require_once __DIR__ . '/elementor/skin-list-taxonomy.php';
			require_once __DIR__ . '/elementor/skin-cards-taxonomy.php';

			// Define widgets.
			require_once __DIR__ . '/elementor/sermon-archive.php';
			require_once __DIR__ . '/elementor/sermon-filtering.php';
			require_once __DIR__ . '/elementor/sermon-taxonomy.php';

			// Define theme widgets.
			require_once __DIR__ . '/elementor/sermon-audio-player.php';
			require_once __DIR__ . '/elementor/sermon-video-player.php';
			require_once __DIR__ . '/elementor/sermon-info.php';
		} );

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		if ( \is_plugin_active( 'elementor/elementor.php' ) ) {
			if ( ! class_exists( '\ElementorPro\Plugin' ) ) {
				wp_enqueue_style( 'smp_elementor_pro', SMP_URL . 'assets/css/shortcodes/elementor-pro-styling.css', array(), SMP_VERSION );
			}

			wp_enqueue_style( 'smp_elementor_all', SMP_URL . 'assets/css/shortcodes/elementor-all-styling.css', array(), SMP_VERSION );
			wp_enqueue_script( 'sm_pro_masonry_js', SMP_URL . 'assets/js/masonry.js', array(), SMP_VERSION, false );
		}
	}

	/**
	 * Adds WordPress shortcodes.
	 *
	 * @since 2.0.4
	 */
	public function init_wordpress() {
		include_once __DIR__ . '/wordpress/wp_shortcode.php';
		include_once __DIR__ . '/wordpress/wp_archive.php';
		include_once __DIR__ . '/wordpress/wp_taxonomy.php';
		include_once __DIR__ . '/wordpress/functions.php';

		add_shortcode(
			'smpro_archive',
			function ( $atts ) {
				if ( is_admin() ) {
					return '';
				}

				$shortcode = new WP_Archive( (array) $atts );

				if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
					define( 'SM_ENQUEUE_SCRIPTS_STYLES', true ); // phpcs:ignore
				}

				return $shortcode->get_content();
			}
		);

		add_shortcode(
			'smpro_tax',
			function ( $atts ) {
				if ( is_admin() ) {
					return '';
				}

				$shortcode = new WP_Taxonomy( (array) $atts );

				if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
					define( 'SM_ENQUEUE_SCRIPTS_STYLES', true ); // phpcs:ignore
				}

				return $shortcode->get_content();
			}
		);
	}

	/**
	 * Loads code for Gutenberg page builder.
	 */
	public function init_gutenberg() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		include_once SMP_PATH_SHORTCODES . 'gutenberg-v2/gutenberg.php';
		include_once SMP_PATH_SHORTCODES . 'gutenberg/sermons_blog/sermons_blog.php'; // Deprecated.
		include_once SMP_PATH_SHORTCODES . 'gutenberg/sermons_taxonomy/sermons_taxonomy.php'; // Deprecated.
	}

	/**
	 * Loads code for Beaver Builder page builder.
	 */
	public function init_beaver() {
		if ( ! class_exists( '\FLBuilder' ) ) {
			return;
		}

		include_once __DIR__ . '/beaver/functions.php';

	}

	/**
	 * Loads code for Visual Composer page builder.
	 */
	public function init_vc() {
		if ( ! function_exists( 'vc_lean_map' ) ) {
			return;
		}

		include_once __DIR__ . '/visual-composer/sermon-blog.php';
		include_once __DIR__ . '/visual-composer/sermon-taxonomy.php';

		wp_enqueue_style( 'smp_vc', SMP_URL . 'assets/css/shortcodes/visual-composer.css', array(), SMP_VERSION );
	}

	/**
	 * Loads code for Divi page builder.
	 */
	public function init_divi() {
		if ( ! class_exists( '\ET_Builder_Module' ) ) {
			return;
		}

		include_once __DIR__ . '/divi/functions.php';
		include_once __DIR__ . '/divi/sermon-blog.php';
		include_once __DIR__ . '/divi/sermon-taxonomy.php';

		wp_enqueue_style( 'sm_pro_divi', SMP_URL . 'assets/css/shortcodes/divi.css', array(), SMP_VERSION );

		if ( true ) { // Temporary, until `smp_divi_include_taxonomies()` is fixed.
			return;
		}

		wp_enqueue_script( 'sm_pro_choices', SMP_URL . 'assets/vendor/choices/js/choices.min.js', array( 'et_pb_admin_js' ), SMP_VERSION );
		wp_enqueue_style( 'sm_pro_choices', SMP_URL . 'assets/vendor/choices/css/choices.min.css', array(), SMP_VERSION );
	}

}
