<?php
/**
 * Helper functions for Divi page builder.
 *
 * @since   1.0.0-beta.0
 *
 * @package SMP\Shortcodes\Divi
 */

defined( 'ABSPATH' ) or die;

/**
 * Renders the taxonomy selector for Divi builder.
 *
 * @since 1.0.0-beta.0
 *
 * @return string The selector.
 */
function smp_divi_include_taxonomies() {
	$output = "\t" . "<% var et_pb_include_taxonomies_temp = typeof data !== 'undefined' && typeof data.et_pb_include_taxonomies !== 'undefined' ? data.et_pb_include_taxonomies.split( ',' ) : []; et_pb_include_taxonomies_temp = typeof data === 'undefined' && typeof et_pb_include_taxonomies !== 'undefined' ? et_pb_include_taxonomies.split( ',' ) : et_pb_include_taxonomies_temp; console.log(et_pb_include_taxonomies_temp); %>" . "\n";

	foreach ( sm_get_taxonomies() as $taxonomy ) {
		$labels = get_taxonomy_labels( get_taxonomy( $taxonomy ) );
		$terms  = get_terms( $taxonomy, array( 'hide_empty' => false ) );

		$output .= '<label>' . esc_html( $labels->name ) . ':<select multiple="multiple" class="et_pb_include_taxonomies-select">';

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				// @todo - update the data decoder. (decode outside the loop, then access it here)
				/*$contains = sprintf(
					'<%%= _.contains( et_pb_include_taxonomies_temp, "%1$s" ) ? selected="selected" : "" %%>',
					$term->term_id
				);*/
				$contains = false;

				/* @noinspection HtmlUnknownAttribute */
				$output .= sprintf(
					'%4$s<option value="%1$s" %3$s>%2$s</option>',
					esc_attr( $term->term_id ),
					esc_html( $term->name ),
					$contains,
					"\n\t\t\t\t\t"
				);
			}
		} else {
			/* @noinspection HtmlUnknownAttribute */
			$output .= sprintf(
				'%3$s<option value="%1$s">%2$s</option>',
				'',
				__( '-- No terms available --', 'sermon-manager' ),
				"\n\t\t\t\t\t"
			);
		}

		$output .= '</select></label><br>';
	}

	// @todo - for some reason, this field is not being saved. Which is blocking everything. High priority.
	// @todo - add the value in here on load. See how to echo "et_pb_include_taxonomies_temp" JS variable here.
	$output = '<div id="et_pb_include_taxonomies">' . $output . '<input name="et_pb_include_taxonomies" value=""></div>';

	// @todo - find a way to integrate with Divi and access the settings modal, instead of this workaround. This is maybe breaking the modal rendering as well.
	$output .= '<script src="' . SMP_URL . 'assets/js/divi/choices.js' . '"></script>';
	$output .= '<style>.et-pb-option-container--smp_divi_include_taxonomies {width: 30%;min-width: 250px;}</style>';

	return $output;
}
