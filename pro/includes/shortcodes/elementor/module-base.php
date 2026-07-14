<?php
/**
 * Module base implementation from Elementor Pro.
 *
 * @since   2.0.4
 *
 * @package SMP\Shortcodes\Elementor
 */

namespace SMP\Shortcodes\Elementor;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Module_Base
 *
 * @package SMP\Shortcodes\Elementor
 */
abstract class Module_Base {

	/**
	 * The instances.
	 *
	 * @var Module_Base
	 */
	protected static $_instances = array();

	/**
	 * Reflection.
	 *
	 * @var \ReflectionClass
	 */
	private $reflection;

	/**
	 * Components.
	 *
	 * @var array
	 */
	private $components = array();

	/**
	 * Module_Base constructor.
	 *
	 * @throws \ReflectionException Ehh.
	 */
	public function __construct() {
		$this->reflection = new \ReflectionClass( $this );

		add_action( 'elementor/widgets/register', array( $this, 'init_widgets' ) );
	}

	/**
	 * If is active.
	 *
	 * @return bool
	 */
	public static function is_active() {
		return true;
	}

	/**
	 * The instance of this class.
	 *
	 * @throws \ReflectionException Who knows.
	 *
	 * @return Module_Base The instance.
	 */
	public static function instance() {
		if ( empty( static::$_instances[ static::class_name() ] ) ) {
			static::$_instances[ static::class_name() ] = new static();
		}

		return static::$_instances[ static::class_name() ];
	}

	/**
	 * Returns the name of this class.
	 *
	 * @return string The class name.
	 */
	public static function class_name() {
		return get_called_class();
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 2.0.4
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Something went wrong.', 'sermon-manager' ), '2.0.4' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 2.0.4
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Something went wrong.', 'sermon-manager' ), '2.0.4' );
	}

	/**
	 * Gets assets URL.
	 *
	 * @return string
	 */
	public function get_assets_url() {
		return ELEMENTOR_PRO_MODULES_URL . $this->get_name() . '/assets/';
	}

	/**
	 * Returns the module name.
	 *
	 * @return mixed
	 */
	abstract public function get_name();

	/**
	 * Initializes widgets.
	 */
	public function init_widgets() {
		$widget_manager = Plugin::instance()->widgets_manager;

		foreach ( $this->get_widgets() as $widget ) {
			$class_name = $this->reflection->getNamespaceName() . '\Widgets\\' . $widget;
			$widget_manager->register( new $class_name() );
		}
	}

	/**
	 * Get the widgets.
	 *
	 * @return array
	 */
	public function get_widgets() {
		return array();
	}

	/**
	 * Add a component.
	 *
	 * @param string $id       The ID.
	 * @param mixed  $instance The component instance.
	 */
	public function add_component( $id, $instance ) {
		$this->components[ $id ] = $instance;
	}

	/**
	 * Get a component.
	 *
	 * @param string $id The id.
	 *
	 * @return bool|mixed
	 */
	public function get_component( $id ) {
		if ( isset( $this->components[ $id ] ) ) {
			return $this->components[ $id ];
		}

		return false;
	}
}
