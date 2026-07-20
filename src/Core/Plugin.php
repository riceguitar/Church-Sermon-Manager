<?php

declare(strict_types=1);

namespace SM\Core;

/**
 * Main plugin class.
 *
 * @package SM\Core
 */
class Plugin {
    /**
     * The single instance of the class.
     *
     * @var Plugin
     */
    protected static $instance = null;

    /**
     * Main Plugin Instance.
     *
     * Ensures only one instance of Plugin is loaded or can be loaded.
     *
     * @return Plugin - Main instance.
     */
    public static function get_instance(): Plugin {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Plugin constructor.
     */
    protected function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks.
     */
    protected function init_hooks(): void {
        add_action('init', [$this, 'load_translations']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts_styles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts_styles']);
        add_filter('post_class', [$this, 'add_additional_sermon_classes'], 10, 3);
        add_action('after_setup_theme', [$this, 'add_image_sizes']);
        add_action('wp_footer', [$this, 'maybe_print_cloudflare_plyr']);
        add_action('init', [$this, 'register_scripts_styles']);
    }

    /**
     * Load plugin translations.
     */
    public function load_translations(): void {
        load_plugin_textdomain(
            'church-sermon-manager',
            false,
            dirname(plugin_basename(__FILE__)) . '/../languages/'
        );
    }

    /**
     * Enqueue scripts and styles.
     */
    public function enqueue_scripts_styles(): void {
        // Implementation will be moved from the old class
    }

    /**
     * Add additional classes to sermon posts.
     *
     * @param array $classes Array of post classes.
     * @param array $class Array of additional classes.
     * @param int $post_id Post ID.
     * @return array Modified array of post classes.
     */
    public function add_additional_sermon_classes(array $classes, array $class, int $post_id): array {
        // Implementation will be moved from the old class
        return $classes;
    }

    /**
     * Add image sizes.
     */
    public function add_image_sizes(): void {
        // Implementation will be moved from the old class
    }

    /**
     * Maybe print Cloudflare Plyr.
     */
    public function maybe_print_cloudflare_plyr(): void {
        // Implementation will be moved from the old class
    }

    /**
     * Register scripts and styles.
     */
    public function register_scripts_styles(): void {
        // Implementation will be moved from the old class
    }
} 