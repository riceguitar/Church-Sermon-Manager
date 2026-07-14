<?php
/**
 * Templating settings.
 *
 * @since   2.0.4
 * @package SMP\Templating
 */

namespace SMP\Templating;

defined( 'ABSPATH' ) or exit;

/**
 * Manages sanitation and output of settings used in templating.
 *
 * @since 2.0.4
 */
class Settings {
	/**
	 * Gets the settings tabs.
	 *
	 * @return array The tabs.
	 */
	public static function get_tabs() {
		return array_keys( apply_filters( 'sm_pro_get_templating_settings', array() ) );
	}

	/**
	 * Get a setting from the template meta.
	 *
	 * @param string $name    The setting name.
	 * @param mixed  $default The default value to return.
	 *
	 * @return mixed Setting value or default value if not set/error.
	 */
	public static function get_setting( $name, $default = '' ) {
		$settings = self::get_settings();
		if ( ! $settings ) {
			return $default;
		}

		return isset( $settings[ $name ] ) ? $settings[ $name ] : $default;
	}

	/**
	 * Gets the settings.
	 *
	 * @return array The settings.
	 */
	public static function get_settings() {
		$template          = Templating_Manager::get_active_template();
		$settings_defaults = self::get_settings_defaults();
		$settings          = array();

		// Return default settings if template not set.
		if ( ! isset( $template->id ) || ! $template || ! Templating_Manager::is_active() ) {
			return $settings_defaults;
		}

		$saved_settings = (array) get_post_meta( $template->id, 'sm_template_settings', true );

		if ( ! ( isset( $saved_settings[0] ) && '' === $saved_settings[0] ) ) {
			foreach ( array_keys( $saved_settings ) as $key ) {
				$settings[ sanitize_title( $key ) ] = $saved_settings[ $key ];
			}
		}

		// Add template default settings if there is nothing saved.
		if ( is_array( $template->default_settings ) ) {
			$settings = $template->default_settings + $settings;
		}

		// Fill out missing settings with defaults.
		$settings += $settings_defaults;

		return $settings;
	}

	/**
	 * Gets the settings defaults.
	 *
	 * @param bool $tabs Should the settings be organized in tabs or not.
	 *
	 * @return array The defaults.
	 */
	public static function get_settings_defaults( $tabs = false ) {
		$settings = apply_filters( 'sm_pro_get_templating_settings', array() );

		$default_settings = array();
		foreach ( $settings as $tab_name => $settings_data ) {
			foreach ( $settings_data as $setting ) {
				if ( ! isset( $setting['id'] ) || ! isset( $setting['type'] ) ) {
					continue;
				}

				$option_name = $setting['id'];
				$raw_value   = isset( $setting['default'] ) ? $setting['default'] : null;

				if ( null === $raw_value ) {
					switch ( $setting['type'] ) {
						case 'checkbox':
							$raw_value = 'no';
							break;
						case 'color':
							$raw_value = '#000000';
							break;
						case 'select':
						case 'radio':
							if ( isset( $setting['options'] ) ) {
								$keys      = array_keys( $setting['options'] );
								$raw_value = $keys[0];
							} else {
								$raw_value = '';
							}

							break;
						default:
							$raw_value = '';
					}
				}

				$default_settings[ $option_name ] = $raw_value;
			}
		}

		return $default_settings;
	}

	/**
	 * Sanitizes and saves data to the template.
	 *
	 * @param int $post_id The ID of the template that is being saved.
	 */
	public static function save( $post_id ) {
		// Trigger actions.
		do_action( 'sm_pro_templating_before_settings_save' );

		$settings        = apply_filters( 'sm_pro_get_templating_settings', array() );
		$update_settings = array();
		foreach ( $settings as $tab_name => $settings_data ) {
			foreach ( $settings_data as $setting ) {
				if ( ! isset( $setting['id'] ) || ! isset( $setting['type'] ) ) {
					continue;
				}

				// Get posted value.
				$option_name = $setting['id'];
				$raw_value   = isset( $_POST[ $setting['id'] ] ) ? wp_unslash( $_POST[ $setting['id'] ] ) : null;

				// Format the value based on option type.
				switch ( $setting['type'] ) {
					case 'checkbox':
						if ( null === $raw_value ) {
							$value = 'no';
						} else {
							$value = '1' === $raw_value || 'yes' === $raw_value || 'on' === $raw_value ? 'yes' : 'no';
						}
						break;
					default:
						$value = sm_clean( $raw_value );
						break;
				}

				/**
				 * Sanitize the value of an option.
				 *
				 * @param mixed  $value     Setting value.
				 * @param string $setting   Setting name.
				 * @param mixed  $raw_value Non-sanitized value.
				 */
				$value = apply_filters( 'sm_pro_templating_sanitize_meta', $value, $setting, $raw_value );

				/**
				 * Sanitize the value of an option by option name.
				 *
				 * @param mixed  $value     Setting value.
				 * @param string $setting   Setting name.
				 * @param mixed  $raw_value Non-sanitized value.
				 */
				$value = apply_filters( "sm_pro_templating_sanitize_meta_$option_name", $value, $setting, $raw_value );

				if ( is_null( $value ) ) {
					continue;
				}

				$update_settings[ $option_name ] = $value;
			}
		}

		// Save all options in our array.
		update_post_meta( $post_id, 'sm_template_settings', $update_settings );

		// Trigger actions after settings saved.
		do_action( 'sm_pro_templating_after_settings_save' );
	}
}
