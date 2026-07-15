<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * `wp-blocks`: includes block type registration and related functions.
 *
 * @since 1.0.0
 */
function sermons_blog_frontend_assets()
{
    wp_enqueue_style(
        'sermons_blog-style-css',
        plugins_url('dist/blocks.style.build.css', dirname(__FILE__)),
        array(),
        filemtime(plugin_dir_path(__DIR__).'dist/blocks.style.build.css')
    );
    
}

// Hook: Frontend assets.
add_action('enqueue_block_assets', 'sermons_blog_frontend_assets');

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * `wp-blocks`: includes block type registration and related functions.
 * `wp-element`: includes the WordPress Element abstraction for describing the structure of your blocks.
 * `wp-i18n`: To internationalize the block's text.
 *
 * @since 1.0.0
 */
function sermons_blog_editor_assets()
{
    wp_enqueue_script(
        'sermons_blog-js',
        plugins_url('dist/blocks.build.js', dirname(__FILE__)),
        array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-components' , 'wp-editor' ),
        filemtime(plugin_dir_path(__DIR__).'dist/blocks.build.js'),
        true
    );

    wp_enqueue_style(
        'sermons_blog-editor-css',
        plugins_url('dist/blocks.editor.build.css', dirname(__FILE__)),
        array('wp-edit-blocks'),
        filemtime(plugin_dir_path(__DIR__).'dist/blocks.editor.build.css')
    );
}

// Hook: Editor assets.
add_action('enqueue_block_editor_assets', 'sermons_blog_editor_assets');
