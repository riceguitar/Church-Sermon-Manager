<?php // phpcs:ignore
/**
 * Plugin Name: Church Sermon Manager
 * Plugin URI: https://github.com/riceguitar/Church-Sermon-Manager
 * Description: Add audio and video sermons, manage speakers, series, templates, podcasting, and page-builder widgets to your church website. Community-maintained successor to Sermon Manager and Sermon Manager Pro.
 * Version: 3.4.1
 * Update URI: https://github.com/riceguitar/Church-Sermon-Manager
 * Author: Sierra Marketing (David Sudarma)
 * Author URI: https://sierra.host/church-sermon-manager/
 * Original Author: Based on the original Sermon Manager by WP for Church.
 * Requires at least: 6.4
 * Tested up to wordpress: 6.8
 * Tested up to PHP : 8.2
 * Requires PHP: 8.1
 *
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Text Domain: church-sermon-manager
 * Domain Path: /languages/
 *
 * @package SM\Core
 */

// All files must be PHP 8.1 compatible!
defined( 'ABSPATH' ) or die;

// Refuse to load alongside the legacy standalone plugins this one absorbed —
// their classes and functions would collide fatally. Data carries over as-is.
if ( in_array( 'sermon-manager-for-wordpress/sermons.php', (array) get_option( 'active_plugins', array() ), true )
	|| in_array( 'sermon-manager-pro/sermons.php', (array) get_option( 'active_plugins', array() ), true )
	|| class_exists( 'SermonManager', false ) ) {
	add_action( 'admin_notices', function () {
		echo '<div class="notice notice-error"><p><strong>Church Sermon Manager</strong> is idle: please deactivate the legacy "Sermon Manager for WordPress" and "Sermon Manager Pro" plugins first. All sermons, settings, and templates carry over automatically.</p></div>';
	} );
	return;
}

// Everything below lives in a separate file so that, when the guard above
// trips, none of this plugin's classes or functions are even compiled.
define( 'CSM_PLUGIN_FILE', __FILE__ );
require_once __DIR__ . '/includes/main.php';
