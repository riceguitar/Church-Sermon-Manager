<?php
/**
 * Adds a custom control - Choices, to replace Select2.
 *
 * @package SMP\Shortcodes\Elementor
 */

namespace SMP\Shortcodes\Elementor;

use Elementor\Base_Data_Control;

defined( 'ABSPATH' ) or die;

/**
 * Init class.
 *
 * @package SMP\Shortcodes\Elementor
 */
class Control_Choices extends Base_Data_Control {
	/**
	 * Get control name.
	 *
	 * @return string
	 */
	public function get_type() {
		return self::get_the_type();
	}

	/**
	 * Get the control name.
	 *
	 * @return string
	 * @static
	 */
	public static function get_the_type() {
		return 'choices';
	}

	/**
	 * Renders the actual control.
	 *
	 * @return void
	 */
	public function content_template() {
		$uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field">
			<label for="<?php echo esc_attr( $uid ); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
				<select id="<?php echo esc_attr( $uid ); ?>" class="elementor-choices" type="choices" {{ multiple }}
						data-setting="{{ data.name }}">
					<# _.each( data.options, function( option_title, option_value ) {
					var value = data.controlValue;
					if ( typeof value == 'string' ) {
					var selected = ( option_value === value ) ? 'selected' : '';
					} else if ( null !== value ) {
					var value = _.values( value );
					var selected = ( -1 !== value.indexOf( option_value ) ) ? 'selected' : '';
					}
					#>
					<option {{ selected }} value="{{ option_value }}">{{{ option_title }}}</option>
					<# } ); #>
				</select>
			</div>
			<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
			<# } #>
		</div>
		<?php

	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue() {
		wp_enqueue_script( 'sm_pro_choices', SMP_URL . 'assets/vendor/choices/js/choices.min.js', array(), '3.0.4' );
		wp_enqueue_script( 'sm_pro_choices_elementor', SMP_URL . 'assets/js/choices.js', array( 'sm_pro_choices' ), SMP_VERSION );
		wp_enqueue_style( 'sm_pro_choices', SMP_URL . 'assets/vendor/choices/css/choices.min.css', array(), '3.0.4' );
	}

	/**
	 * Gets the default options.
	 *
	 * @return array The options.
	 */
	protected function get_default_settings() {
		return array(
			'label_block' => true,
			'default'     => array(),
			'multiple'    => false,
			'options'     => array(),
		);
	}
}

