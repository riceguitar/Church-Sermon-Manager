<?php
/**
 * The main class for shortcodes.
 *
 * @since   2.0.4
 *
 * @package SMP\Shortcodes\WordPress
 */

namespace SMP\Shortcodes;

/**
 * Class WP_Shortcode.
 */
abstract class WP_Shortcode {
	/**
	 * Shortcode type.
	 *
	 * @var   string
	 */
	protected $type = '';

	/**
	 * Attributes.
	 *
	 * @var   array
	 */
	protected $attributes = array();

	/**
	 * Query args.
	 *
	 * @var   array
	 */
	protected $query_args = array();

	/**
	 * Initialize shortcode.
	 *
	 * @param array  $attributes Shortcode attributes.
	 * @param string $type       Shortcode type.
	 */
	public function __construct( $attributes = array(), $type = '' ) {
		if ( is_admin() ) {
			return; // We don't need to run it in admin area, don't we?
		}

		$this->type       = $type;
		$this->attributes = $this->parse_attributes( $attributes );
		$this->query_args = $this->parse_query_args();
	}

	/**
	 * Parse attributes.
	 *
	 * @param  array $attributes Shortcode attributes.
	 *
	 * @return array
	 */
	protected abstract function parse_attributes( $attributes );

	/**
	 * Parse query args.
	 *
	 * @return array
	 */
	protected abstract function parse_query_args();

	/**
	 * Get shortcode attributes.
	 *
	 * @return array
	 */
	public function get_attributes() {
		return $this->attributes;
	}

	/**
	 * Get query args.
	 *
	 * @return array
	 */
	public function get_query_args() {
		return $this->query_args;
	}

	/**
	 * Get shortcode type.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get shortcode content.
	 *
	 * @return string
	 */
	public abstract function get_content();

	/**
	 * Run the query and return an array of data, including queried ids and pagination information.
	 *
	 * @return \WP_Query The results.
	 */
	protected function get_query_results() {
		$query_args = apply_filters( 'smp/shortcodes/wordpress/query_args', $this->query_args );

		$query = new \WP_Query( $query_args );

		return $query;
	}
}
