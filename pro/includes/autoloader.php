<?php
/**
 * Autoloader.
 *
 * @package SMP\Core
 */

namespace SMP;

defined( 'ABSPATH' ) or die;

/**
 * Sermon Manager Pro Autoloader
 *
 * @since 2.0.4
 */
class Autoloader {
	/**
	 * Path to the includes directory.
	 *
	 * @var string
	 * @access private
	 */
	private static $include_path = SMP_PATH_INCLUDES;

	/**
	 * The Constructor.
	 */
	public static function run() {
		if ( function_exists( '__autoload' ) ) {
			spl_autoload_register( '__autoload' );
		}

		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Auto-load SM classes on demand to reduce memory consumption.
	 *
	 * @param string $class The class name.
	 */
	public static function autoload( $class ) {
		$class = strtolower( $class );

		// Check if it's SM Pro.
		if ( strpos( $class, '\\' ) !== false && strpos( $class, 'smp' ) !== false ) {
			$class_path = explode( '\\', $class );
			$path       = '';

			foreach ( $class_path as $item ) {
				switch ( $item ) {
					case 'shortcodes':
						$path = 'shortcodes/';
						break;
					case 'templating':
						$path = 'templating/';
						break;
					case 'podcasting':
						$path = 'podcasting/';
						break;
					case 'sermonmanagerpro':
					default:
						break;
				}
			}

			$class = end( $class_path );
			$file  = self::get_file_name_from_class( $class );
			$path  = self::$include_path . $path;
		} else {
			return;
		}

		if ( empty( $path ) || ! self::load_file( $path . $file ) ) {
			self::load_file( self::$include_path . $file );
		}
	}

	/**
	 * Take a class name and turn it into a file name.
	 *
	 * @param  string $class The class name.
	 *
	 * @return string File name
	 * @access private
	 */
	private static function get_file_name_from_class( $class ) {
		return "${class}.php";
	}

	/**
	 * Include a class file.
	 *
	 * @param string $path The path to include.
	 *
	 * @return bool Successful or not
	 * @access private
	 */
	private static function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			/* @noinspection PhpIncludeInspection */
			include_once( $path );

			return true;
		}

		return false;
	}
}
