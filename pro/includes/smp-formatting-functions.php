<?php
/**
 * Functions used for formatting.
 *
 * @package SMP
 */

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @param string $string  String to convert.
 * @param bool   $strict  If set to false, it will return original variable instead of false if no match found.
 * @param bool   $numeric If it's set to true, it will also check for numeric 1 and 0.
 *
 * @return bool|mixed True if it's truthy, false if it's falsy. Original variable if not strict.
 */
function smp_string_to_bool( $string, $strict = true, $numeric = false ) {
	if ( is_bool( $string ) ) {
		return $string;
	}

	if (
		in_array( $string, array( 'yes', 'y', 'positive', 'true', 'on' ) ) ||
		( $numeric && in_array( $string, array( '1', 1 ) ) )
	) {
		return true;
	}

	if (
		in_array( $string, array( 'no', 'n', 'negative', 'false', 'off' ) ) ||
		( $numeric && in_array( $string, array( '0', 0 ) ) )
	) {
		return false;
	}

	return $strict ? false : $string;
}

/**
 * Converts a bool to a 'yes' or 'no'.
 *
 * @param bool $bool String to convert.
 *
 * @return string|mixed "yes" if it's true, "no" if it's false. Original variable otherwise.
 */
function smp_bool_to_string( $bool ) {
	if ( ! is_bool( $bool ) ) {
		$bool = smp_string_to_bool( $bool, true, true );
	}

	return true === $bool ? 'yes' : 'no';
}
