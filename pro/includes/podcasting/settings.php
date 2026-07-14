<?php
/**
 * Podcasting settings.
 *
 * @since   1.0.0-beta.5
 * @package SMP\Podcasting
 */

namespace SMP\Podcasting;

defined( 'ABSPATH' ) or exit;

/**
 * Manages sanitation and output of settings used in podcasting.
 *
 * @since 1.0.0-beta.5
 */
class Settings {
	/**
	 * Gets the settings tabs.
	 *
	 * @return array The tabs.
	 */
	public static function get_tabs() {
		return array_keys( apply_filters( 'sm_pro_get_podcasting_settings', array() ) );
	}

	/**
	 * Get a setting from the podcast meta.
	 *
	 * @param int    $id      Podcast ID.
	 * @param string $name    The setting name.
	 * @param mixed  $default The default value to return.
	 *
	 * @return mixed Setting value or default value if not set/error.
	 */
	public static function get_setting( $id, $name, $default = '' ) {
		$podcast = get_post_meta( $id, 'sm_podcast_settings', true );

		return isset( $podcast[ $name ] ) ? $podcast[ $name ] : $default;
	}

	/**
	 * Sanitizes and saves data to the podcast.
	 *
	 * @param int $post_id The ID of the podcast that is being saved.
	 */
	public static function save( $post_id ) {
		// Trigger actions.
		do_action( 'sm_pro_podcasting_before_settings_save' );

		$settings        = apply_filters( 'sm_pro_get_podcasting_settings', array() );
		$update_settings = array();
		foreach ( $settings as $settings_data => $setting ) {
			if ( ! isset( $setting['id'] ) || ! isset( $setting['type'] ) ) {
				continue;
			}

			// Get posted value.
			$option_name = $setting['id'];
			$raw_value   = isset( $_POST[ 'podcast_' . $setting['id'] ] ) ? wp_unslash( $_POST[ 'podcast_' . $setting['id'] ] ) : null;

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
			$value = apply_filters( 'sm_pro_podcasting_sanitize_meta', $value, $setting, $raw_value );

			/**
			 * Sanitize the value of an option by option name.
			 *
			 * @param mixed  $value     Setting value.
			 * @param string $setting   Setting name.
			 * @param mixed  $raw_value Non-sanitized value.
			 */
			$value = apply_filters( "sm_pro_podcasting_sanitize_meta_$option_name", $value, $setting, $raw_value );

			if ( is_null( $value ) ) {
				continue;
			}

			$update_settings[ $option_name ] = $value;
		}

		// Save all options in our array.
		update_post_meta( $post_id, 'sm_podcast_settings', $update_settings );

		// Trigger actions after settings saved.
		do_action( 'sm_pro_podcasting_after_settings_save' );

		update_option( 'sm_podcasting_saved_notice', 1 );
	}
}
