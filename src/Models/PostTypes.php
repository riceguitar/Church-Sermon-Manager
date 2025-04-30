<?php

declare(strict_types=1);

namespace SM\Models;

use SM\Core\Plugin;

/**
 * Handles the registration of custom post types and taxonomies.
 *
 * @package SM\Models
 */
class PostTypes {
    /**
     * The single instance of the class.
     *
     * @var PostTypes
     */
    protected static $instance = null;

    /**
     * Main PostTypes Instance.
     *
     * Ensures only one instance of PostTypes is loaded or can be loaded.
     *
     * @return PostTypes - Main instance.
     */
    public static function get_instance(): PostTypes {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * PostTypes constructor.
     */
    protected function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks.
     */
    protected function init_hooks(): void {
        add_action('init', [$this, 'register_post_types'], 6);
        add_action('init', [$this, 'register_taxonomies'], 5);
        add_action('init', [$this, 'support_jetpack_omnisearch']);
        add_filter('rest_api_allowed_post_types', [$this, 'rest_api_allowed_post_types']);
        add_action('sm_flush_rewrite_rules', [$this, 'flush_rewrite_rules']);
    }

    /**
     * Register sermon post type.
     */
    public function register_post_types(): void {
        if (!is_blog_installed()) {
            return;
        }

        if (post_type_exists('wpfc_sermon')) {
            return;
        }

        do_action('sm_register_post_type');

        $permalinks = sm_get_permalink_structure();
        $supports = ['title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'revisions'];

        register_post_type(
            'wpfc_sermon',
            apply_filters(
                'sm_register_post_type_wpfc_sermon',
                [
                    'labels'              => [
                        'name'               => __('Sermons', 'sermon-manager-for-wordpress'),
                        'singular_name'      => __('Sermon', 'sermon-manager-for-wordpress'),
                        'menu_name'          => _x('Sermons', 'menu', 'sermon-manager-for-wordpress'),
                        'add_new'            => __('Add Sermon', 'sermon-manager-for-wordpress'),
                        'add_new_item'       => __('Add New Sermon', 'sermon-manager-for-wordpress'),
                        'edit'               => __('Edit', 'sermon-manager-for-wordpress'),
                        'edit_item'          => __('Edit Sermon', 'sermon-manager-for-wordpress'),
                        'new_item'           => __('New Sermon', 'sermon-manager-for-wordpress'),
                        'view'               => __('View Sermon', 'sermon-manager-for-wordpress'),
                        'view_item'          => __('View Sermon', 'sermon-manager-for-wordpress'),
                        'search_items'       => __('Search Sermons', 'sermon-manager-for-wordpress'),
                        'not_found'          => __('No Sermons found', 'sermon-manager-for-wordpress'),
                        'not_found_in_trash' => __('No Sermons found in trash', 'sermon-manager-for-wordpress'),
                    ],
                    'public'              => true,
                    'show_ui'             => true,
                    'show_in_menu'        => true,
                    'show_in_nav_menus'   => true,
                    'show_in_admin_bar'   => true,
                    'menu_position'       => 5,
                    'menu_icon'           => 'dashicons-microphone',
                    'can_export'          => true,
                    'delete_with_user'    => false,
                    'hierarchical'        => false,
                    'has_archive'         => true,
                    'query_var'           => true,
                    'supports'            => $supports,
                    'show_in_rest'        => true,
                    'rest_base'           => 'sermons',
                    'rewrite'             => [
                        'slug'       => $permalinks['wpfc_sermon'],
                        'with_front' => false,
                    ],
                ]
            )
        );

        do_action('sm_after_register_post_type');
    }

    /**
     * Register sermon taxonomies.
     */
    public function register_taxonomies(): void {
        if (!is_blog_installed()) {
            return;
        }

        if (taxonomy_exists('wpfc_preacher')) {
            return;
        }

        do_action('sm_register_taxonomy');

        $permalinks = sm_get_permalink_structure();

        $capabilities = [
            'manage_terms' => 'manage_wpfc_categories',
            'edit_terms'   => 'manage_wpfc_categories',
            'delete_terms' => 'manage_wpfc_categories',
            'assign_terms' => 'manage_wpfc_categories',
        ];

        // The labels with their defaults in the singular lowercase form.
        $labels = [
            'wpfc_preacher'     => Plugin::getOption('preacher_label') ? strtolower(Plugin::getOption('preacher_label')) : __('Preacher', 'sermon-manager-for-wordpress'),
            'wpfc_service_type' => Plugin::getOption('service_type_label') ? strtolower(Plugin::getOption('service_type_label')) : __('Service Type', 'sermon-manager-for-wordpress'),
        ];

        register_taxonomy(
            'wpfc_preacher',
            apply_filters('sm_taxonomy_objects_wpfc_preacher', ['wpfc_sermon']),
            apply_filters(
                'sm_taxonomy_args_wpfc_preacher',
                [
                    'hierarchical' => false,
                    'label'        => ucwords($labels['wpfc_preacher']),
                    'labels'       => [
                        'name'              => ucwords($labels['wpfc_preacher'] . 's'),
                        'singular_name'     => ucwords($labels['wpfc_preacher']),
                        'menu_name'         => ucwords($labels['wpfc_preacher'] . 's'),
                        'search_items'      => wp_sprintf(__('Search %s', 'sermon-manager-for-wordpress'), $labels['wpfc_preacher']),
                        'all_items'         => wp_sprintf(__('All %s', 'sermon-manager-for-wordpress'), $labels['wpfc_preacher']),
                        'parent_item'       => null,
                        'parent_item_colon' => null,
                        'edit_item'         => wp_sprintf(__('Edit %s', 'sermon-manager-for-wordpress'), $labels['wpfc_preacher']),
                        'update_item'       => wp_sprintf(__('Update %s', 'sermon-manager-for-wordpress'), $labels['wpfc_preacher']),
                        'add_new_item'      => wp_sprintf(__('Add new %s', 'sermon-manager-for-wordpress'), $labels['wpfc_preacher']),
                        'new_item_name'     => wp_sprintf(__('New %s name', 'sermon-manager-for-wordpress'), $labels['wpfc_preacher']),
                        'not_found'         => wp_sprintf(__('No %s found', 'sermon-manager-for-wordpress'), $labels['wpfc_preacher']),
                    ],
                    'show_ui'      => true,
                    'query_var'    => true,
                    'show_in_rest' => true,
                    'rewrite'      => [
                        'slug'       => $permalinks['wpfc_preacher'],
                        'with_front' => false,
                    ],
                    'capabilities' => $capabilities,
                ],
                $permalinks,
                $capabilities
            )
        );

        // Register other taxonomies...
        // [Previous taxonomy registrations would go here]

        do_action('sm_after_register_taxonomy');
    }

    /**
     * Flush rewrite rules.
     */
    public function flush_rewrite_rules(): void {
        flush_rewrite_rules();
    }

    /**
     * Support Jetpack Omnisearch.
     */
    public function support_jetpack_omnisearch(): void {
        if (class_exists('Jetpack_Omnisearch_Posts')) {
            new \Jetpack_Omnisearch_Posts('wpfc_sermon');
        }
    }

    /**
     * Add sermon post type to REST API allowed post types.
     *
     * @param array $post_types Allowed post types.
     * @return array Modified post types.
     */
    public function rest_api_allowed_post_types(array $post_types): array {
        $post_types[] = 'wpfc_sermon';
        return $post_types;
    }
} 