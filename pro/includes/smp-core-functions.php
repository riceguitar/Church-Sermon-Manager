<?php
/**
 * Functions that are used both on front and backend, general purpose.
 *
 * @package SMP\Core
 */

defined( 'ABSPATH' ) or die;

/**
 * Adds taxonomy parameter to the query.
 *
 * @param array $args Existing query args.
 *
 * @return array $args Modified query args.
 *
 * @since 1.0.0-beta.8
 */
function smp_add_taxonomy_to_query( $args ) {
	global $taxonomy, $term;

	// Add global taxonomies.
	if ( ! empty( $taxonomy ) && ! empty( $term ) ) {
		// Override the default tax_query for that taxonomy.
		if ( ! empty( $args['tax_query'] ) ) {
			foreach ( $args['tax_query'] as $id => $arg ) {
				if ( ! is_array( $arg ) ) {
					continue;
				}

				if ( $arg['taxonomy'] === $taxonomy ) {
					unset( $args['tax_query'][ $id ] );
				}
			}
		}

		$args['tax_query'][] = array(
			'taxonomy' => $taxonomy,
			'field'    => is_numeric( $term ) ? 'term_id' : 'slug',
			'terms'    => is_numeric( $term ) ? intval( $term ) : sanitize_title( $term ),
		);

		if ( count( $args['tax_query'] ) > 1 ) {
			$args['tax_query']['relation'] = 'AND';
		}
	}

	// Add GET taxonomies.
	foreach (
		array(
			'wpfc_preacher',
			'wpfc_sermon_series',
			'wpfc_sermon_topics',
			'wpfc_bible_book',
			'wpfc_service_type',
			'wpfc_date'
		) as $taxonomy
	) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only request state, no data mutation.
		if ( isset( $_GET[ $taxonomy ] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only request state, no data mutation.
			$terms = sanitize_text_field( wp_unslash( $_GET[ $taxonomy ] ) );

			// Override the default tax_query for that taxonomy.
			if ( ! empty( $args['tax_query'] ) ) {
				foreach ( $args['tax_query'] as $id => $arg ) {
					if ( ! is_array( $arg ) ) {
						continue;
					}

					if ( $arg['taxonomy'] === $taxonomy ) {
						unset( $args['tax_query'][ $id ] );
					}
				}
			}
			
			$termData = (false !== strpos( $terms, ',' )) ? array_map( 'sanitize_title', explode( ',', $terms ) ) : sanitize_title( $terms );

			// PHP 8 issue Unparenthesized a ? b : c ? d : e is deprecated. Use either (a ? b : c) ? d : e or a ? b : (c ? d : e)

			if (is_numeric($terms)) {
			    $field = 'term_id';
			    $terms = intval($terms);
			} else {
			    $field = 'slug';
			    $terms = $termData;
			}

			// $args['tax_query'][] = array(
			// 	'taxonomy' => $taxonomy,
			// 	'field'    => is_numeric( $terms ) ? 'term_id' : 'slug',
			// 	'terms'    => is_numeric( $terms ) ? intval( $terms ) : $termData ,
			// );

			$args['tax_query'][] = array(
			    'taxonomy' => $taxonomy,
			    'field'    => $field,
			    'terms'    => $terms,
			);

			if ( count( $args['tax_query'] ) > 1 ) {
				$args['tax_query']['relation'] = 'AND';
			}
		}
	}

	return $args;
}

/**
 * Creates an array of sermon id => sermon date.
 *
 * @return array The array.
 *
 * @since 1.0.0-beta.9
 */
function smp_rebuild_sermon_date_mapping() {
	global $wpdb;

	// Transient key.
	$option_name = 'smp_dates_mapping';

	// Get transient date mapping.
	$the_mapping = get_transient( $option_name );

	if ( false === $the_mapping ) {

		// All sermons.
		$sermons = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s", 'wpfc_sermon' ) );

		$the_mapping = array();

		foreach ( $sermons as $sermon ) {
			$sermon_date = get_post_meta( $sermon->ID, 'sermon_date', true );
			if ( ! empty( $sermon_date ) ) {
				$the_mapping[ $sermon->ID ] = intval( $sermon_date );
			}
		}

		// Order from newest to oldest.
		arsort( $the_mapping );

		set_transient( $option_name, $the_mapping, 12 * HOUR_IN_SECONDS );

	}

	return $the_mapping;
}

/**
 * Update a sermon date of post.
 *
 * @param integer $post_id Current post ID.
 *
 * @since 1.0.0-beta.9
 */
function smp_update_sermon_date_mapping( $post_id ) {

	// Transient key.
	$option_name = 'smp_dates_mapping';

	// Update mapping.
	$the_mapping = get_transient( $option_name );
	if ( false !== $the_mapping ) {

		$sermon_date = get_post_meta( $post_id, 'sermon_date', true );
		if ( ! empty( $sermon_date ) ) {
			$the_mapping[ $post_id ] = intval( $sermon_date );
		}

		set_transient( $option_name, $the_mapping, 12 * HOUR_IN_SECONDS );
	}
}

if ( ! function_exists( 'sm_pagination' ) ) {
	/**
	 * Placeholder, if SM does not have this function.
	 */
	function sm_pagination() { // phpcs:ignore
		return;
	}
}

/**
 * Returns an array of iTunes categories.
 *
 * @return array The iTunes categories.
 *
 * @since 2.0.40
 */
function smp_get_itunes_categories() {
	return array(
		''                             => '--- None ---',
		'arts'                         => __( 'Arts', 'church-sermon-manager' ),
		'business'                     => __( 'Business', 'church-sermon-manager' ),
		'comedy'                       => __( 'Comedy', 'church-sermon-manager' ),
		'education'                    => __( 'Education', 'church-sermon-manager' ),
		'games_and_hobbies'            => __( 'Games & Hobbies', 'church-sermon-manager' ),
		'government_and_organizations' => __( 'Government & Organizations', 'church-sermon-manager' ),
		'health'                       => __( 'Health', 'church-sermon-manager' ),
		'kids_and_family'              => __( 'Kids &amp; Family', 'church-sermon-manager' ),
		'music'                        => __( 'Music', 'church-sermon-manager' ),
		'news_and_politics'            => __( 'News &amp; Politics', 'church-sermon-manager' ),
		'religion_and_spirituality'    => __( 'Religion & Spirituality', 'church-sermon-manager' ),
		'science_and_medicine'         => __( 'Science & Medicine', 'church-sermon-manager' ),
		'society_and_culture'          => __( 'Society & Culture', 'church-sermon-manager' ),
		'sports_and_recreation'        => __( 'Sports & Recreation', 'church-sermon-manager' ),
		'technology'                   => __( 'Technology', 'church-sermon-manager' ),
		'tv_and_film'                  => __( 'TV & Film', 'church-sermon-manager' ),
	);
}

/**
 * Gets iTunes subcategories.
 *
 * @param string $category Category ID. Optional.
 *
 * @return array|false The subcategories. False if it does not have any.
 */
function smp_get_itunes_subcategories( $category = '' ) {
	$subcategories = array(
		'arts'                         => array(
			'design'             => 'Design',
			'fashion_and_beauty' => 'Fashion & Beauty',
			'food'               => 'Food',
			'literature'         => 'Literature',
			'performing_arts'    => 'Performing Arts',
			'visual_arts'        => 'Visual Arts',
		),
		'business'                     => array(
			'business_news'            => 'Business News',
			'careers'                  => 'Careers',
			'investing'                => 'Investing',
			'management_and_marketing' => 'Management & Marketing',
			'shopping'                 => 'Shopping',
		),
		'education'                    => array(
			'educational_technology' => 'Educational Technology',
			'higher_education'       => 'Higher Education',
			'k-12'                   => 'K-12',
			'language_courses'       => 'Language Courses',
			'training'               => 'Training',
		),
		'games_and_hobbies'            => array(
			'automotive'  => 'Automotive',
			'aviation'    => 'Aviation',
			'hobbies'     => 'Hobbies',
			'other_games' => 'Other Games',
			'video_games' => 'Video Games',
		),
		'government_and_organizations' => array(
			'local'      => 'Local',
			'national'   => 'National',
			'non-profit' => 'Non-Profit',
			'regional'   => 'Regional',
		),
		'health'                       => array(
			'alternative-health'    => 'Alternative Health',
			'fitness_and_nutrition' => 'Fitness & Nutrition',
			'self-help'             => 'Self-Help',
			'sexuality'             => 'Sexuality',
		),
		'religion_and_spirituality'    => array(
			'christianity' => 'Christianity',
			'buddhism'     => 'Buddhism',
			'hinduism'     => 'Hinduism',
			'islam'        => 'Islam',
			'judaism'      => 'Judaism',
			'other'        => 'Other',
			'spirituality' => 'Spirituality',
		),
		'science_and_medicine'         => array(
			'medicine'         => 'Medicine',
			'natural_sciences' => 'Natural Sciences',
			'social_sciences'  => 'Social Sciences',
		),
		'society_and_culture'          => array(
			'history'           => 'History',
			'personal_journals' => 'Personal Journals',
			'philosophy'        => 'Philosophy',
			'places_and_travel' => 'Places & Travel',
		),
		'sports_and_recreation'        => array(
			'amateur'                 => 'Amateur',
			'college_and_high_school' => 'College & High School',
			'outdoor'                 => 'Outdoor',
			'professional'            => 'Professional',
		),
		'technology'                   => array(
			'gadgets'         => 'Gadgets',
			'tech_news'       => 'Tech News',
			'podcasting'      => 'Podcasting',
			'software_how-to' => 'Software How-To',
		),
	);

	if ( $category ) {
		if ( ! isset( $subcategories[ $category ] ) ) {
			return false;
		}

		return $subcategories[ $category ];
	} else {
		return $subcategories;
	}
}

add_filter(
	'sm_settings_get_select_data',
	function ( $default, $category, $podcast_id, $option_name ) {
		$all_options     = get_post_meta( $podcast_id, 'sm_podcast_settings' );
		$all_options     = isset( $all_options[0] ) ? $all_options[0] : array();
		$option_name     = str_replace( 'podcast_', '', $option_name );
		$selected_option = isset( $all_options[ $option_name ] ) ? $all_options[ $option_name ] : '';
		$options         = smp_get_itunes_subcategories( $category );

		$return = array(
			'selected' => isset( $options[ $selected_option ] ) ? $selected_option : '',
			'options'  => $options,
		);

		return $return;
	},
	10,
	4
);
