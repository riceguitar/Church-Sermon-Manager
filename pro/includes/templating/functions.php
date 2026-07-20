<?php
/**
 * Templating related functions.
 *
 * @since   2.0.4
 * @package SMP\Templating
 */

defined( 'ABSPATH' ) or exit;

/**
 * Sets the settings that are used in Templating screen.
 *
 * @return array The settings.
 *
 * @since 2.0.4
 */
function smp_templating_set_settings() {
	return array(
		'General'          => array(
			array(
				'title'    => 'Date format',
				'type'     => 'select',
				'id'       => 'date_format',
				'options'  => array(
					0            => 'Default', // Checks what's in Settings.
					'Y-m-d'      => date( 'Y-m-d' ),
					'm-d-Y'      => date( 'm-d-Y' ),
					'd-m-Y'      => date( 'd-m-Y' ),
					'M d, Y'     => date( 'M d, Y' ),
					'D, M d, Y'  => date( 'D, M d, Y' ),
					'M d'        => date( 'M d' ),
					'F d, Y'     => date( 'F d, Y' ),
					'l, F d, Y'  => date( 'l, F d, Y' ),
					'd. M. Y'    => date( 'd. M. Y' ),
					'd. F Y'     => date( 'd. F Y' ),
					'd.m.Y'      => date( 'd.m.Y' ),
					'D, d. M. Y' => date( 'D, d. M. Y' ),
					'l, j. F Y'  => date( 'l, j. F Y' ),
				),
				'default'  => 0,
				'desc_tip' => 'It will use format defined in Sermon Manager settings by default',
			),
			array(
				'title'   => 'No. of sermons per page',
				'id'      => 'no_of_sermons',
				'type'    => 'number',
				'default' => 10,
			),
			array(
				'title'           => 'Layout columns',
				'id'              => 'layout_columns',
				'type'            => 'select',
				'options'         => array(
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
				),
				'default'         => 1,
				'dynamic_message' => 'smp_maybe_show_notice',
				'disabled'        => SermonManager::getOption( 'theme_compatibility' ),
			),
			array(
				'title'   => 'Masonry Layout',
				'id'      => 'masonry_layout',
				'type'    => 'checkbox',
				'default' => 'no',
			),
			array(
				'title'    => 'Mobile template (development in progress)',
				'id'       => 'mobile_template',
				'type'     => 'select',
				'options'  => array(
					'none' => 'No template',
					// @todo - add list of templates gotten by "Templating_Manager::get_templates()"
				),
				'disabled' => true,
			),
		),
		'Archive/Taxonomy' => array(
			array(
				'title' => 'Styling Options',
				'type'  => 'title',
			),
			array(
				'title'   => 'Sermon card bottom margin',
				'id'      => 'card_bottom_margin',
				'type'    => 'number',
				'default' => 10,
			),
			array(
				'title'   => 'Sermon card padding',
				'id'      => 'card_padding',
				'type'    => 'number',
				'default' => 0,
			),
			array(
				'title'   => 'Image bottom padding',
				'id'      => 'image_bottom_padding',
				'type'    => 'number',
				'default' => 0,
			),
			array(
				'title'   => 'Image position',
				'id'      => 'image_position',
				'type'    => 'select',
				'options' => array(
					'left'  => 'Left',
					'right' => 'Right',
					'top'   => 'Top',
				),
				'default' => 'left',
			),
			array(
				'title'   => 'Main content padding',
				'id'      => 'card_main_padding',
				'type'    => 'number',
				'default' => 20,
			),
			array(
				'title'   => 'Title bottom margin',
				'id'      => 'title_bottom_margin',
				'type'    => 'number',
				'default' => 5,
			),
			array(
				'title'   => 'Description top padding',
				'id'      => 'description_top_padding',
				'type'    => 'number',
				'default' => 5,
			),
			array(
				'title' => 'Show/Hide Information',
				'type'  => 'title',
			),
			array(
				'title'   => 'Show date',
				'id'      => 'show_date',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show series',
				'id'      => 'show_series',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show preacher',
				'id'      => 'show_preacher',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show preacher image',
				'id'      => 'show_preacher_image',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Comment count',
				'id'      => 'comment_count',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show audio icon',
				'id'      => 'show_audio',
				'type'    => 'checkbox',
				'default' => 'no',
			),
			array(
				'title'   => 'Show audio player',
				'id'      => 'show_audio_player',
				'type'    => 'checkbox',
				'default' => 'no',
			),
			array(
				'title'   => 'Show notes',
				'id'      => 'show_notes',
				'type'    => 'checkbox',
				'default' => 'no',
			),
			array(
				'title'   => 'Show bulletin',
				'id'      => 'show_bulletin',
				'type'    => 'checkbox',
				'default' => 'no',
			),
			array(
				'title'   => 'Show topics',
				'id'      => 'show_topics',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show books',
				'id'      => 'show_books',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show service type',
				'id'      => 'show_service_type',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show description',
				'id'      => 'show_description',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show bible passage',
				'id'      => 'show_bible_passage',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Enable social sharing',
				'id'      => 'enable_social_sharing',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show filtering',
				'id'      => 'show_fitering',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show book filter',
				'id'      => 'show_book_filter',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show topics filter',
				'id'      => 'show_topics_filter',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show preacher filter',
				'id'      => 'show_preacher_filter',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show series filter',
				'id'      => 'show_series_filter',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show service type filter',
				'id'      => 'show_service_type_filter',
				'type'    => 'checkbox',
				'default' => 'no',
			),
			array(
				'title'   => 'Show date filter',
				'id'      => 'show_date_filter',
				'type'    => 'checkbox',
				'default' => 'no',
			),
			array(
				'title'   => 'Sort list by',
				'id'      => 'sort_list_by',
				'type'    => 'select',
				'options' => array(
					'date_desc' => 'Date (Desc)',
					'date_asc'  => 'Date (Asc)',
				),
			),
			array(
				'title'   => 'Show Image',
				'id'      => 'show_image',
				'type'    => 'select',
				'options' => array(
					'none'   => 'None',
					'series' => 'Series',
					'sermon' => 'Sermon',
				),
				'default' => 'sermon',
			),
		),
		'Single'           => array(
			array(
				'title'   => 'Enable social sharing',
				'id'      => 'enable_social_sharing_single',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show image',
				'id'      => 'show_image_single',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show video',
				'id'      => 'show_video_single',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show audio',
				'id'      => 'show_audio_single',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show title',
				'id'      => 'show_title_single',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show date',
				'id'      => 'show_date_single',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show series',
				'id'      => 'show_series_single',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show preacher',
				'id'      => 'show_preacher_single',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show topics',
				'id'      => 'show_topics_single',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show books',
				'id'      => 'show_books_single',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show service type',
				'id'      => 'show_service_type_single',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show comment count',
				'id'      => 'show_comment_count_single',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show description',
				'id'      => 'show_description_single',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show bible passage',
				'id'      => 'show_bible_passage_single',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Show download link',
				'id'      => 'show_download_link_single',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title'   => 'Label of Notes',
				'id'      => 'label_notes',
				'type'    => 'text',
				'default' => 'Notes',
			),
			array(
				'title'   => 'Label of Bulletin',
				'id'      => 'label_bulletin',
				'type'    => 'text',
				'default' => 'Bulletin',
			),
			array(
				'title' => 'Theme Specific Options',
				'type'  => 'title',
			),
			array(
				'title'   => 'Remove sidebar on Divi theme',
				'id'      => 'remove_sidebar',
				'type'    => 'checkbox',
				'default' => 'no',
			),
		),
		'Bible'            => array(
			array(
				'title'   => 'Enable bible verse links',
				'id'      => 'bible_enable',
				'type'    => 'checkbox',
				'default' => 'yes',
			),
			array(
				'title' => 'Heading style',
				'type'  => 'title',
			),
			array(
				'title'   => 'Font color',
				'type'    => 'color',
				'id'      => 'bible_heading_font_color',
				// Color settings default to #000000; the heading background is black,
				// so the title text must default to white to stay readable.
				'default' => '#ffffff',
			),
			array(
				'title'   => 'Default font',
				'type'    => 'select',
				'id'      => 'bible_heading_font_family',
				'options' => array(
					'default'         => '(Default)',
					'arial'           => 'Arial',
					'courier_new'     => 'Courier New',
					'georgia'         => 'Georgia',
					'palantino'       => 'Palantino',
					'tahoma'          => 'Tahoma',
					'times_new_roman' => 'Times New Roman',
					'verdana'         => 'Verdana',
				),
			),
			array(
				'title'   => 'Font size',
				'type'    => 'select',
				'id'      => 'bible_heading_font_size',
				'options' => array(
					'default' => '(Default)',
					12        => '12px',
					14        => '14px',
					16        => '16px',
					18        => '18px',
				),
			),
			array(
				'title' => 'Background',
				'type'  => 'color',
				'id'    => 'bible_heading_background_color',
			),
			array(
				'title' => 'Body style',
				'type'  => 'title',
			),
			array(
				'title' => 'Font color',
				'type'  => 'color',
				'id'    => 'bible_body_font_color',
			),
			array(
				'title'   => 'Default font',
				'type'    => 'select',
				'id'      => 'bible_body_font_family',
				'options' => array(
					'default'         => '(Default)',
					'arial'           => 'Arial',
					'courier_new'     => 'Courier New',
					'georgia'         => 'Georgia',
					'palantino'       => 'Palantino',
					'tahoma'          => 'Tahoma',
					'times_new_roman' => 'Times New Roman',
					'verdana'         => 'Verdana',
				),
			),
			array(
				'title'   => 'Font size',
				'type'    => 'select',
				'id'      => 'bible_body_font_size',
				'options' => array(
					'default' => '(Default)',
					12        => '12px',
					14        => '14px',
					16        => '16px',
					18        => '18px',
				),
			),
			array(
				'title'   => 'Background',
				'type'    => 'radio',
				'id'      => 'bible_body_background_color',
				'options' => array(
					'light' => 'Light',
					'dark'  => 'Dark',
				),
				'default' => 'light',
			),
			array(
				'title' => 'Link color',
				'type'  => 'color',
				'id'    => 'bible_body_link_color',
			),
			array(
				'title' => 'Bible translation',
				'type'  => 'title',
			),
			array(
				'type' => 'description',
				'desc' => 'You can find translation option in the <a href="' . admin_url( 'edit.php?post_type=wpfc_sermon&page=sm-settings&tab=verse' ) . '">plugin settings</a>.',
			),
			array(
				'title' => 'Additional styling',
				'type'  => 'title',
			),
			array(
				'title'   => 'Drop shadow',
				'type'    => 'checkbox',
				'id'      => 'bible_drop_shadow',
				'default' => true,
			),
			array(
				'title'   => 'Rounded corners',
				'type'    => 'checkbox',
				'id'      => 'bible_rounded_corners',
				'default' => true,
			),
			array(
				'title' => 'Social share',
				'type'  => 'title',
			),
			array(
				'title'   => 'Twitter',
				'type'    => 'checkbox',
				'id'      => 'bible_twitter',
				'default' => true,
			),
			array(
				'title'   => 'Facebook',
				'type'    => 'checkbox',
				'id'      => 'bible_facebook',
				'default' => true,
			),
			array(
				'title'   => 'Google+',
				'type'    => 'checkbox',
				'id'      => 'bible_googleplus',
				'default' => true,
			),
			array(
				'title'   => 'Faithlife',
				'type'    => 'checkbox',
				'id'      => 'bible_faithlife',
				'default' => true,
			),
			array(
				'title' => 'Online Bible reader',
				'type'  => 'title',
			),
			array(
				'title'   => '',
				'type'    => 'radio',
				'id'      => 'bible_reader',
				'options' => array(
					'biblia'    => 'Biblia',
					'faithlife' => 'Faithlife Study Bible',
				),
				'default' => 'biblia',
			),
			array(
				'title' => 'More advanced settings',
				'type'  => 'title_divider',
			),
			array(
				'title' => 'Exclude content',
				'type'  => 'title',
			),
			array(
				'title'   => 'Tags to exclude',
				'type'    => 'text',
				'id'      => 'bible_exclude_heading',
				'default' => 'h1, h2, h3',
			),
			array(
				'title' => 'Classes to exclude',
				'type'  => 'text',
				'id'    => 'bible_exclude_classes',
			),
			array(
				'title' => 'Logos integration',
				'type'  => 'title',
			),
			array(
				'title' => 'Add Logos buttons to tooltip',
				'type'  => 'checkbox',
				'id'    => 'bible_logos',
			),
			array(
				'title'   => 'Logos buttons colors',
				'type'    => 'radio',
				'id'      => 'bible_logos_color',
				'options' => array(
					'light' => 'Light',
					'dark'  => 'Dark',
				),
			),
			array(
				'title' => 'Advanced options',
				'type'  => 'title',
			),
			array(
				'title'   => 'Show tooltip on hover',
				'type'    => 'checkbox',
				'id'      => 'bible_tooltip',
				'default' => true,
			),
			array(
				'title'   => 'Open Bible in new window',
				'type'    => 'checkbox',
				'id'      => 'bible_new_window',
				'default' => true,
			),
			array(
				'title'   => 'Case sensitivity',
				'type'    => 'checkbox',
				'id'      => 'bible_case_sensitivity',
				'default' => true,
			),
			array(
				'title' => 'Enable on existing Biblia links',
				'type'  => 'checkbox',
				'id'    => 'bible_existing_biblia',
			),
			array(
				'title' => 'Chapter-level tagging',
				'type'  => 'checkbox',
				'id'    => 'bible_chapter_level_tagging',
			),
		),
		'Video'            => array(
			array(
				'title'   => 'Video Player',
				'type'    => 'select',
				'id'      => 'video_player',
				'options' => array(
					'plyr' => 'Plyr',
				),
			),
		),
		'Audio'            => array(
			array(
				'title'   => 'Audio Player',
				'type'    => 'select',
				'id'      => 'audio_player',
				'options' => array(
					'wp'   => 'WordPress',
					'plyr' => 'Plyr',
				),
			),
			array(
				'title' => 'Show download button for Plyr player',
				'type'  => 'checkbox',
				'id'    => 'show_download_button',
			),
		),
	);
}

add_filter( 'sm_pro_get_templating_settings', 'smp_templating_set_settings' );

/**
 * Checks if templating is used and if the default template is not Sermon Manager's views.
 *
 * @since 2.0.4
 *
 * @return bool
 */
function smp_is_templating_being_used() {
	return \SMP\Templating\Templating_Manager::is_active();
}


/**
 * Configure Bible Verse.
 *
 * @see   https://faithlife.com/products/reftagger/customize
 *
 * @since 1.0.0-beta.0
 */
function smp_template_config() {
	if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
		return;
	}

	$settings       = \SMP\Templating\Settings::get_settings();
	$social_sharing = array(
		$settings['bible_twitter'] ? 'twitter' : '',
		$settings['bible_facebook'] ? 'facebook' : '',
		$settings['bible_googleplus'] ? 'googleplus' : '',
		$settings['bible_faithlife'] ? 'faithlife' : '',
	);

	?>
	<?php if ( 'yes' === $settings['bible_enable'] && ! SermonManager::getOption( 'verse_popup' ) ) : ?>

	<?php endif; ?>
	<?php
}

add_action( 'wp_footer', 'smp_template_config' );

/**
 * Adds SM Pro settings to the WordPress query.
 *
 * @param WP_Query|array $query The query or query args.
 *
 * @return array Query vars.
 *
 * @since 1.0.0-beta.0
 */
function smp_add_query_settings( $query ) {
	$settings   = \SMP\Templating\Settings::get_settings();
	$query_vars = is_array( $query ) ? $query : array();
	$is_query   = $query instanceof WP_Query;

	if ( ! empty( $settings['no_of_sermons'] ) ) {
		$query_vars['posts_per_page'] = $settings['no_of_sermons'];

		if ( $is_query ) {
			$query->set( 'posts_per_page', $settings['no_of_sermons'] );
		}
	}

	if ( ! empty( $settings['sort_list_by'] ) ) {
		switch ( $settings['sort_list_by'] ) {
			case 'date_desc':
				$query_vars['order'] = 'DESC';

				if ( $is_query ) {
					$query->set( 'order', 'DESC' );
				}
				break;
			case 'date_asc':
				$query_vars['order'] = 'ASC';

				if ( $is_query ) {
					$query->set( 'order', 'ASC' );
				}
				break;
		}
	}

	return $query_vars;
}

add_filter( 'smp/shortcodes/wordpress/query_args', 'smp_add_query_settings' );
//add_action( 'sm_query', 'smp_add_query_settings' );

/**
 * Adds SM Pro settings to Sermon Manager's filtering.
 *
 * @param array $args The args.
 *
 * @return array Modified args.
 *
 * @since 1.0.0-beta.0
 */
function smp_add_filtering_settings( $args ) {
	$settings = \SMP\Templating\Settings::get_settings();

	$mapping = array(
		'show_book_filter'         => 'hide_books',
		'show_topics_filter'       => 'hide_topics',
		'show_preacher_filter'     => 'hide_preachers',
		'show_series_filter'       => 'hide_series',
		'show_service_type_filter' => 'hide_service_types',
		'show_date_filter'         => 'hide_dates',
	);

	foreach ( $mapping as $setting => $option ) {
		// Check if setting is set.
		if ( empty( $settings[ $setting ] ) ) {
			continue;
		}

		if ( isset( $args['smp_override_settings'] ) && ! $args['smp_override_settings'] ) {
			// Template settings have higher priority, so just ignore what SM setting is.
			$args[ $option ] = 'yes' === $settings[ $setting ] ? '' : 'yes';
		} else {
			if ( '' === $args[ $option ] ) {
				$args[ $option ] = 'yes' === $settings[ $setting ] ? '' : 'yes';
			}
		}
	}

	if ( ! empty( $settings['show_fitering'] ) && 'no' === $settings['show_fitering'] ) {
		add_filter( 'sm_render_wpfc_sorting', '__return_false' );
	}

	return $args;
}

add_filter( 'sm_render_wpfc_sorting_args', 'smp_add_filtering_settings' );

/**
 * Add date filter to filters.
 *
 * @param array $args The args.
 *
 * @return array Modified args.
 *
 * @since 1.0.0-beta.9
 */
function smp_add_additional_filtering( $args ) {
	$args[] = array(
		'className' => 'sortDates',
		'taxonomy'  => 'wpfc_dates',
		'title'     => __( 'Date', 'church-sermon-manager' ),
	);

	return $args;
}

add_filter( 'render_wpfc_sorting_filters', 'smp_add_additional_filtering' );

/**
 * Modifies the existing mapping for Sermon Manager filtering, so we can hide new/additonal filters.
 *
 * @param array $mapping The existing mapping.
 *
 * @return array The modified mapping.
 */
function smp_add_additional_filtering_hiding( $mapping ) {
	$mapping['wpfc_dates'] = 'hide_dates';

	return $mapping;
}

add_filter( 'render_wpfc_sorting_visibility_mapping', 'smp_add_additional_filtering_hiding' );

/**
 * Allows to add the date to the sermon query.
 *
 * @param array $query Sermon query.
 *
 * @return mixed
 *
 * @since 1.0.0-beta.9
 */
function sm_query_filtering_shortcode( $query ) {
	$taxonomy      = 'wpfc_dates';
	$current_value = get_query_var( $taxonomy ) ?: ( isset( $_GET[ $taxonomy ] ) ? $_GET[ $taxonomy ] : '' );

	if ( ! empty( $current_value ) ) {

		$date = explode( '-', $current_value );

		if ( ! empty( $date[1] ) && ! empty( $date[1] ) ) {

			$month = $date[0];
			$year  = $date[1];

			// First day of the month.
			$start_date = strtotime( $year . $month . '01' );

			// Last day of the month.
			$end_date = strtotime( date( $year . $month . 't' ) );

			$query['meta_key']     = 'sermon_date';
			$query['meta_value']   = array( $start_date, $end_date );
			$query['meta_compare'] = 'BETWEEN';
			$query['orderby']      = 'meta_value';
			$query['order']        = 'DESC';
		}
	}

	return $query;
}

add_filter( 'smp/shortcodes/beaver/sermon_query', 'sm_query_filtering_shortcode' );
add_filter( 'smp/shortcodes/divi/sermon_query', 'sm_query_filtering_shortcode' );
add_filter( 'smp/shortcodes/elementor/sermon_query', 'sm_query_filtering_shortcode' );
add_filter( 'smp/shortcodes/wordpress/query_args', 'sm_query_filtering_shortcode' );
add_filter( 'smp/shortcodes/wpbakery/sermon_query', 'sm_query_filtering_shortcode' );

/**
 * Allows to add the date to the sermon query.
 *
 * @param WP_Query $query Sermon query.
 *
 * @since 1.0.0-beta.9
 */
function sm_query_filtering( $query ) {
	if ( ! is_admin() ) {
		$taxonomy      = 'wpfc_dates';
		$current_value = get_query_var( $taxonomy ) ?: ( isset( $_GET[ $taxonomy ] ) ? $_GET[ $taxonomy ] : '' );

		if ( ! empty( $current_value ) ) {
			$date = explode( '-', $current_value );

			if ( ! empty( $date[1] ) && ! empty( $date[1] ) ) {

				$month = $date[0];
				$year  = $date[1];

				// First day of the month.
				$start_date = strtotime( $year . $month . '01' );

				// Last day of the month.
				$end_date = strtotime( date( $year . $month . 't' ) );

				$query->set( 'meta_key', 'sermon_date' );
				$query->set( 'meta_value', array( $start_date, $end_date ) );
				$query->set( 'meta_compare', 'BETWEEN' );
				$query->set( 'orderby', 'meta_value' );
				$query->set( 'order', 'DESC' );
			}
		}
	}
}

add_action( 'sm_query', 'sm_query_filtering', 0 );

function sm_query_date_filter_pro( $query ) {
	if ( ! is_admin() ) {
			$taxonomy      = 'wpfc_dates';
			$current_value = get_query_var( $taxonomy ) ?: ( isset( $_GET[ $taxonomy ] ) ? $_GET[ $taxonomy ] : '' );
			if ( ! empty( $current_value ) ) {
				$date = explode( '-', $current_value );
				if ( ! empty( $date[1] ) && ! empty( $date[1] ) ) {
					$month = $date[0];
					$year  = $date[1];
					// First day of the month.
					$start_date = strtotime( $year . $month . '01' );
					// Last day of the month.
					$end_date = strtotime( date( $year . $month . 't' ) );
					$query['meta_query'][] = array(
							'key'     => 'sermon_date',
							'value'   =>array( $start_date, $end_date ),
							'compare' => 'BETWEEN',
						);
				}
			}
		}

	return $query;
}

/**
 * Output date dropdown.
 *
 * @param string $html         Content.
 * @param string $taxonomy     Taxonomy name from the arguments.
 * @param string $default      The forced default value. See function PHPDoc.
 * @param array  $terms        The array of terms, books will already be ordered.
 * @param string $current_slug The term that is being requested.
 *
 * @return string Modified options.
 *
 * @since 1.0.0-beta.9
 */
function wpfc_get_term_dropdown_date( $html, $taxonomy, $default, $terms, $current_slug ) {
	if ( 'wpfc_dates' !== $taxonomy ) {
		return $html;
	}

	// Init vars.
	$sermon_dates = smp_rebuild_sermon_date_mapping(); // @todo - This is a very intensive operation.
	$month_year   = array();
	$output       = '';

	// Grab dates.
	foreach ( $sermon_dates as $sermon_id => $sermon_date ) {
		$date      = date( 'm-Y', $sermon_date );
		$nice_date = date( 'M Y', $sermon_date );

		$month_year[ $date ] = $nice_date;
	}

	// Build options.
	foreach ( $month_year as $value => $title ) {
		$output .= '<option value="' . $value . '" ' . ( ( '' === $default ? $current_slug === $value : $default === $value ) ? 'selected' : '' ) . '>' . $title . '</option>';
	}

	return $output;
}

add_filter( 'wpfc_get_term_dropdown', 'wpfc_get_term_dropdown_date', 10, 5 );

/**
 * Get the columns setting - this is how many sermons will be shown per row in loops.
 *
 * @return int
 *
 * @since 1.0.0-beta.0
 */
function smp_get_sermons_per_row() {
	$settings = \SMP\Templating\Settings::get_settings();

	$columns = ! empty( $settings['layout_columns'] ) ? $settings['layout_columns'] : 0;

	$columns = absint( $columns );

	return max( 1, $columns );
}

/**
 * Installs default template(s) if they are not already installed.
 *
 * @since 1.0.0-beta.0
 */
function smp_maybe_install_default_templates() {
	/**
	 * Filesystem class declaration.
	 *
	 * @var $wp_filesystem WP_Filesystem_Direct
	 */
	global $pagenow, $wp_filesystem;
	$installed_templates_dir = apply_filters( 'sm_pro_templating_templates_directory', WP_CONTENT_DIR . '/data/sermon-manager-for-wordpress/' );
	$source_templates_dir    = SMP_PATH . 'templates/';
	$template_manager        = new \SMP\Templating\Templating_Manager();
	$installed_templates     = $template_manager::get_templates();

	// Exit if we are on frontend.
	if ( ! is_admin() ) {
		return;
	}

	// Do a rescan of templates.
	$template_manager::rescan_templates();

	// Check if we have the directory for default templates.
	if ( ! is_dir( $source_templates_dir ) ) {
		update_option( 'smp_new_templates', array() );

		return;
	}

	// Init filesystem.
	include_once ABSPATH . 'wp-admin/includes/file.php';
	$fs_initialized = WP_Filesystem();

	if ( ! $fs_initialized ) {
		add_action(
			'init',
			function () {
				\SMP\Plugin::instance()->notice_manager->add_warning( 'templating_init_fail', __( 'Could not initialize filesystem API, template updating will not work.', 'church-sermon-manager' ), 'templating' );
			}
		);
	}

	// Check if we have the directory for installed templates, and if not - create it.
	if ( ! is_dir( $installed_templates_dir ) ) {
		wp_mkdir_p( $installed_templates_dir );
	}

	// Start templates loop.
	foreach ( scandir( $source_templates_dir ) as $template ) {
		if ( in_array( $template, array( '.', '..' ) ) ) {
			continue;
		}

		// Check if template already exists.
		if ( is_dir( $installed_templates_dir . $template ) ) {
			// Maybe trigger notice that we have an update.
			foreach ( $installed_templates as $installed_template ) {
				if ( basename( $installed_template->path ) === $template ) { // Found it.
					$installed_version = $installed_template->version;
					$source_version    = null;

					foreach ( scandir( $source_templates_dir . $template ) as $file ) {
						if ( in_array( $template, array( '.', '..' ) ) ) {
							continue;
						}

						if ( pathinfo( $source_templates_dir . $template . '/' . $file, PATHINFO_EXTENSION ) === 'json' ) {
							$data           = json_decode( file_get_contents( $source_templates_dir . $template . '/' . $file ) );
							$source_version = isset( $data->version ) ? $data->version : null;

							break;
						}
					}

					if ( version_compare( $installed_version, $source_version, '<' ) ) {
						// If we should install now.
						if ( 'edit.php' === $pagenow && ( isset( $_GET['post_type'] ) && 'wpfc_sm_template' === $_GET['post_type'] ) && ( isset( $_GET['doaction'] ) && 'updateall' === $_GET['doaction'] ) ) {
							// Replace the installed template with updated one.
							$wp_filesystem->rmdir( $installed_templates_dir . $template, true );
							mkdir( $installed_templates_dir . $template );
							copy_dir( $source_templates_dir . $template, $installed_templates_dir . $template );

							// Clear the notification.
							$existing_updates = get_option( 'smp_new_templates', array(), true );
							$existing_updates = is_array( $existing_updates ) ? $existing_updates : array();
							if ( isset( $existing_updates[ $installed_template->name ] ) ) {
								unset( $existing_updates[ $installed_template->name ] );
							}

							update_option( 'smp_new_templates', $existing_updates );
							wp_redirect( admin_url( 'edit.php?post_type=wpfc_sm_template&doaction=updated' ) );
							exit;
						} else {
							$existing_updates = get_option( 'smp_new_templates', array(), true );
							$existing_updates = is_array( $existing_updates ) ? $existing_updates : array();
							if ( isset( $_GET['doaction'] ) && 'updated' === $_GET['doaction'] ) {

								if ( isset( $existing_updates[ $installed_template->name ] ) ) {
									unset( $existing_updates[ $installed_template->name ] );
								}
								update_option( 'smp_new_templates', $existing_updates );
								wp_redirect( admin_url( 'edit.php?post_type=wpfc_sm_template' ) );
								exit;
							} else {
								$existing_updates[ $installed_template->name ] = array(
									'old_version' => $installed_version,
									'new_version' => $source_version,
								);
								update_option( 'smp_new_templates', $existing_updates );
							}


						}
					} else {
						$existing_updates = get_option( 'smp_new_templates', array(), true );
						$existing_updates = is_array( $existing_updates ) ? $existing_updates : array();
						if ( isset( $existing_updates[ $installed_template->name ] ) ) {
							unset( $existing_updates[ $installed_template->name ] );
						}

						update_option( 'smp_new_templates', $existing_updates );
					}
				}
			}
		} else { // Else copy it to templates.
			mkdir( $installed_templates_dir . $template );
			copy_dir( $source_templates_dir . $template, $installed_templates_dir . $template );
		}
	}
}

smp_maybe_install_default_templates();

/**
 * Shows notice if Theme Compatibility is used.
 *
 * @return null|string The notice or null if option is not checked.
 *
 * @since 1.0.0-beta.6
 */
function smp_maybe_show_notice() {
	if ( SermonManager::getOption( 'theme_compatibility' ) ) {
		return '<p style="color:red;display:inline;font-style:italic;">Notice: "Theme Compatibility" option is checked in <a href="' . admin_url( 'edit.php?post_type=wpfc_sermon&page=sm-settings&tab=debug' ) . '">the plugin settings</a>. Please uncheck it for columns to work.</p>';
	}

	$overridden_views = array();
	$views            = array(
		'archive-wpfc_sermon.php',
		'taxonomy-wpfc_preacher.php',
		'taxonomy-wpfc_sermon_series.php',
		'taxonomy-wpfc_sermon_topics.php',
		'taxonomy-wpfc_sermon_service_type.php',
	);

	foreach ( $views as $view ) {
		if ( file_exists( get_stylesheet_directory() . '/' . $view ) ) {
			$view_file = file_get_contents( get_stylesheet_directory() . '/' . $view );

			if ( strpos( $view_file, '-before-sermons' ) === false ) {
				$overridden_views[] = $view;
			}
		}
	}

	if ( count( $overridden_views ) > 0 ) {
		$message = '<p style="color:red;display:inline;font-style:italic;">Notice: These views are overridden and do not contain the required code for columns to work:</p>';

		$message .= '<ul>';

		foreach ( $overridden_views as $overridden_view ) {
			$message .= '<li>' . $overridden_view . '</li>';
		}

		$message .= '</ul>';

		$message .= '<p>To learn how to fix the issue, please follow <a href="#">this article</a>.</p>';

		return $message;
	}

	return null;
}


/**
 * Disables comments in Sermon Manager view when Divi is active.
 *
 * @param bool $original_value The original value.
 *
 * @return bool If we should disable or not.
 *
 * @since 1.0.0-beta.7
 */
function smp_disable_divi_comments( $original_value ) {
	if ( 'Divi' === get_option( 'template' ) && function_exists( 'et_get_option' ) ) {
		return true;
	}

	return $original_value;
}

add_filter( 'single-wpfc_sermon-disable-comments', 'smp_disable_divi_comments' );
