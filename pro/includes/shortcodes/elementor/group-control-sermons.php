<?php
/**
 * Adds a group control for Sermons element for Elementor.
 *
 * @since   2.0.4
 * @package SMP\Shortcodes\Elementor
 */

namespace SMP\Shortcodes\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class Group_Control_Sermons
 *
 * @package SMP\Shortcodes\Elementor
 */
class Group_Control_Sermons extends Group_Control_Base {

	const INLINE_MAX_RESULTS = 9999;

	/**
	 * The control fields.
	 *
	 * @var array
	 */
	protected static $fields;

	/**
	 * Returns the control type.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'wpfc_sermons';
	}

	/**
	 * Init.
	 *
	 * @return array
	 */
	protected function init_fields() {
		$fields = array();

		$fields['post_type'] = array(
			'label' => __( 'Source', 'church-sermon-manager' ),
			'type'  => Controls_Manager::SELECT,
		);

		$fields['posts_ids'] = array(
			'label'       => __( 'Search & Select', 'church-sermon-manager' ),
			'type'        => 'query',
			'post_type'   => '',
			'options'     => array(),
			'label_block' => true,
			'multiple'    => true,
			'filter_type' => 'by_id',
			'condition'   => array(
				'post_type' => 'by_id',
			),
		);

		$fields['authors'] = array(
			'label'       => __( 'WordPress Author', 'church-sermon-manager' ),
			'label_block' => true,
			'type'        => 'query',
			'multiple'    => true,
			'default'     => array(),
			'options'     => array(),
			'filter_type' => 'author',
			'condition'   => array(
				'post_type!' => array(
					'by_id',
					'current_query',
				),
			),
		);

		return $fields;
	}

	/**
	 * Populate the data.
	 *
	 * @param array $fields The control fields.
	 *
	 * @return array
	 */
	protected function prepare_fields( $fields ) {
		$args = $this->get_args();

		$post_types = array(
			'wpfc_sermon' => 'All Sermons',
		);

		$post_types_options = $post_types;

		$post_types_options['by_id']         = __( 'Manual Selection', 'church-sermon-manager' );
		$post_types_options['current_query'] = __( 'Current Query', 'church-sermon-manager' );

		$fields['post_type']['options'] = $post_types_options;

		$fields['post_type']['default'] = key( $post_types );

		$fields['posts_ids']['object_type'] = array_keys( $post_types );

		$taxonomy_filter_args = array(
			'object_type' => array( 'wpfc_sermon' ),
		);

		$taxonomies = get_taxonomies( $taxonomy_filter_args, 'objects' );

		foreach ( $taxonomies as $taxonomy => $object ) {
			$taxonomy_args = array(
				'label'       => $object->label,
				'type'        => 'query',
				'label_block' => true,
				'multiple'    => true,
				'object_type' => $taxonomy,
				'options'     => array(),
				'condition'   => array(
					'post_type' => $object->object_type,
				),
			);

			$count = wp_count_terms( $taxonomy );

			$options = array();

			// For large websites, use Ajax to search.
			if ( $count > self::INLINE_MAX_RESULTS ) {
				$taxonomy_args['type'] = 'query';

				$taxonomy_args['filter_type'] = 'taxonomy';
			} else {
				$taxonomy_args['type'] = Controls_Manager::SELECT2;

				$terms = get_terms( $taxonomy );

				foreach ( $terms as $term ) {
					$options[ $term->term_id ] = $term->name;
				}

				$taxonomy_args['options'] = $options;
			}

			$fields[ $taxonomy . '_ids' ] = $taxonomy_args;
		}

		return parent::prepare_fields( $fields );
	}

	/**
	 * Get default group options.
	 *
	 * @return array
	 */
	protected function get_default_options() {
		return array(
			'popover' => false,
		);
	}
}
