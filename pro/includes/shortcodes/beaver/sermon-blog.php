<?php
/**
 * Sermon Blog module register code.
 *
 * @since   1.0.0-beta.2
 *
 * @package SMP\Shortcodes\Beaver
 */

namespace SMP\Shortcodes\Beaver;

defined( 'ABSPATH' ) or exit;

/**
 * Define Sermon Blog module.
 */
class Sermon_Blog extends \FLBuilderModule {
	/**
	 * Sermon_Blog constructor.
	 */
	public function __construct() {
		parent::__construct( array(
			'name'            => __( 'Sermons', 'fl-builder' ),
			'description'     => __( 'Display a grid of your Sermons.', 'fl-builder' ),
			'category'        => __( 'Posts', 'fl-builder' ),
			'dir'             => SMP_PATH . 'includes/shortcodes/beaver/sermon-blog/',
			'url'             => SMP_URL . 'includes/shortcodes/beaver/sermon-blog/',
			'icon'            => 'schedule.svg',
			'editor_export'   => false,
			'partial_refresh' => true,
			'enabled'         => true,
		) );

		// Enqueue the CSS.
		$this->add_css( 'sm_pro_beaver_blog', SMP_URL . 'assets/css/shortcodes/beaver/sermon-blog.css', array(), SMP_VERSION );
	}
}

\FLBuilder::register_module( '\SMP\Shortcodes\Beaver\Sermon_Blog', array(
	'layout'     => array(
		'title'    => __( 'Layout', 'fl-builder' ),
		'sections' => array(
			'general'     => array(
				'title'  => '',
				'fields' => array(
					'layout' => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'columns',
						'options' => array(
							'columns' => __( 'Columns', 'fl-builder' ),
							'list'    => __( 'List', 'fl-builder' ),
						),
						'toggle'  => array(
							'columns' => array(
								'sections' => array(
									'sermons',
									'image',
									'info',
									'description',
									'sermon_style',
									'sermon_text_style',
								),
								'fields'   => array(
									'match_height',
									'show_masonry',
									'sermon_columns',
									'sermon_spacing',
									'sermon_margin',
									'featured_type',
								),
							),
							'list'    => array(
								'sections' => array(
									'sermons',
									'image',
									'info',
									'description',
									'sermon_style',
									'sermon_text_style',
								),
								'fields'   => array( 'list_sermon_spacing' ),
							),
						),
					),
				),
			),
			'sermons'     => array(
				'title'  => __( 'Sermons', 'fl-builder' ),
				'fields' => array(
					'match_height'        => array(
						'type'    => 'select',
						'label'   => __( 'Equal Heights', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Yes', 'fl-builder' ),
							'0' => __( 'No', 'fl-builder' ),
						),
						'toggle'  => array(
							'0' => array(
								'fields' => array( 'show_masonry' ),
							),
							'1' => array(
								'fields' => array( 'sermon_spacing' , 'sermon_margin' ),
							),
						),
					),
					'show_masonry'        => array(
						'type'    => 'select',
						'label'   => __( 'Masonry', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'1' => __( 'On', 'fl-builder' ),
							'0' => __( 'Off', 'fl-builder' ),
						),
						'toggle'  => array(
							'0' => array(
								'fields' => array( 'sermon_spacing' , 'sermon_margin' ),
							),
						),
					),
					'sermon_columns'      => array(
						'type'       => 'unit',
						'label'      => __( 'Columns', 'fl-builder' ),
						'responsive' => array(
							'default' => array(
								'default'    => '3',
								'medium'     => '2',
								'responsive' => '1',
							),
						),
					),
					'sermon_spacing'      => array(
						'type'        => 'unit',
						'label'       => __( 'Spacing Between Columns', 'fl-builder' ),
						'default'     => '30',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'list_sermon_spacing' => array(
						'type'        => 'unit',
						'label'       => __( 'Spacing Between Sermons', 'fl-builder' ),
						'default'     => '40',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'sermon_margin'       => array(
						'type'        => 'unit',
						'label'       => __( 'Sermon Bottom Margin', 'fl-builder' ),
						'default'     => '20',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
				),
			),
			'image'       => array(
				'title'  => __( 'Featured Image', 'fl-builder' ),
				'fields' => array(
					'featured_type' => array(
						'type'    => 'select',
						'label'   => __( 'Featured Type', 'fl-builder' ),
						'default' => 'image',
						'options' => array(
							'image' => __( 'Image', 'fl-builder' ),
							'video' => __( 'Video', 'fl-builder' ),
							'none'  => __( 'None', 'fl-builder' ),
						),
						'toggle'  => array(
							'image' => array(
								'fields' => array( 'image_spacing' ),
							),
						),
					),
					'image_spacing' => array(
						'type'        => 'dimension',
						'label'       => __( 'Image Spacing', 'fl-builder' ),
						'default'     => '0',
						'description' => 'px',
					),
				),
			),
			'info'        => array(
				'title'  => __( 'Sermon Info', 'fl-builder' ),
				'fields' => array(
					'show_series'         => array(
						'type'    => 'select',
						'label'   => __( 'Series', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'show_title'          => array(
						'type'    => 'select',
						'label'   => __( 'Title', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'show_date'           => array(
						'type'    => 'select',
						'label'   => __( 'Date', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'date_format' ),
							),
						),
					),
					'date_format'         => array(
						'type'    => 'select',
						'label'   => __( 'Date Format', 'fl-builder' ),
						'default' => 'M j, Y',
						'options' => array(
							'M j, Y' => date( 'M j, Y' ),
							'F j, Y' => date( 'F j, Y' ),
							'm/d/Y'  => date( 'm/d/Y' ),
							'm-d-Y'  => date( 'm-d-Y' ),
							'd M Y'  => date( 'd M Y' ),
							'd F Y'  => date( 'd F Y' ),
							'Y-m-d'  => date( 'Y-m-d' ),
							'Y/m/d'  => date( 'Y/m/d' ),
						),
					),
					'show_audio'          => array(
						'type'    => 'select',
						'label'   => __( 'Audio', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'show_download_audio' ),
							),
						),
					),
					'show_download_audio' => array(
						'type'    => 'select',
						'label'   => __( 'Audio Download Link', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'show_preacher'       => array(
						'type'    => 'select',
						'label'   => __( 'Preacher', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'show_passage'        => array(
						'type'    => 'select',
						'label'   => __( 'Bible Passage', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'show_service_type'   => array(
						'type'    => 'select',
						'label'   => __( 'Service Type', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
				),
			),
			'description' => array(
				'title'  => __( 'Description', 'fl-builder' ),
				'fields' => array(
					'show_description'   => array(
						'type'    => 'select',
						'label'   => __( 'Description', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'description_length', 'show_more_link', 'more_link_text' ),
							),
						),
					),
					'description_length' => array(
						'type'        => 'unit',
						'label'       => __( 'Description Length', 'fl-builder' ),
						'default'     => '30',
						'description' => __( 'words', 'fl-builder' ),
					),
					'show_more_link'     => array(
						'type'    => 'select',
						'label'   => __( 'More Link', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'more_link_text' ),
							),
						),
					),
					'more_link_text'     => array(
						'type'    => 'text',
						'label'   => __( 'More Link Text', 'fl-builder' ),
						'default' => __( 'Read More', 'fl-builder' ),
					),
				),
			),
		),
	),
	'filters'    => array(
		'title'    => __( 'Filters', 'fl-builder' ),
		'sections' => array(
			'sm_filters' => array(
				'title'  => 'Filters',
				'fields' => array(
					'show_filters'             => array(
						'type'    => 'select',
						'label'   => __( 'Filters', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array(
									'filter_spacing',
									'show_filter_topics',
									'show_filter_series',
									'show_filter_preacher',
									'show_filter_book',
									'show_filter_service_type',
								),
							),
						),
					),
					'filter_spacing'           => array(
						'type'        => 'unit',
						'label'       => __( 'Filter Bottom Margin', 'fl-builder' ),
						'default'     => '20',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'show_filter_topics'       => array(
						'type'    => 'select',
						'label'   => __( 'Filter Topics', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'show_filter_series'       => array(
						'type'    => 'select',
						'label'   => __( 'Filter Series', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'show_filter_preacher'     => array(
						'type'    => 'select',
						'label'   => __( 'Filter Preacher', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'show_filter_book'         => array(
						'type'    => 'select',
						'label'   => __( 'Filter Books', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'show_filter_service_type' => array(
						'type'    => 'select',
						'label'   => __( 'Filter Service Types', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
				),
			),
		),
	),
	'style'      => array(
		'title'    => __( 'Style', 'fl-builder' ),
		'sections' => array(
			'post_style' => array(
				'title'  => __( 'Sermons', 'fl-builder' ),
				'fields' => array(
					'bg_color'     => array(
						'type'       => 'color',
						'label'      => __( 'Sermon Background Color', 'fl-builder' ),
						'show_reset' => true,
						'default'    => 'ffffff',
					),
					'border_type'  => array(
						'type'    => 'select',
						'label'   => __( 'Sermon Border Type', 'fl-builder' ),
						'default' => 'solid',
						'options' => array(
							'solid'  => _x( 'Solid', 'Border type.', 'fl-builder' ),
							'dashed' => _x( 'Dashed', 'Border type.', 'fl-builder' ),
							'dotted' => _x( 'Dotted', 'Border type.', 'fl-builder' ),
							'double' => _x( 'Double', 'Border type.', 'fl-builder' ),
							'none'   => _x( 'None', 'Border type.', 'fl-builder' ),
						),
						'toggle'  => array(
							'solid'  => array(
								'fields' => array( 'border_color', 'border_size' ),
							),
							'dashed' => array(
								'fields' => array( 'border_color', 'border_size' ),
							),
							'dotted' => array(
								'fields' => array( 'border_color', 'border_size' ),
							),
							'double' => array(
								'fields' => array( 'border_color', 'border_size' ),
							),
						),
					),
					'border_color' => array(
						'type'       => 'color',
						'label'      => __( 'Sermon Border Color', 'fl-builder' ),
						'default'    => 'dddddd',
						'show_reset' => true,
					),
					'border_size'  => array(
						'type'        => 'unit',
						'label'       => __( 'Sermon Border Size', 'fl-builder' ),
						'default'     => '1',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
				),
			),
			'text_style' => array(
				'title'  => __( 'Text', 'fl-builder' ),
				'fields' => array(
					'title_color'           => array(
						'type'       => 'color',
						'label'      => __( 'Title Color', 'fl-builder' ),
						'default'    => '000000',
						'show_reset' => true,
					),
					'title_font_size'       => array(
						'type'        => 'unit',
						'label'       => __( 'Title Font Size', 'fl-builder' ),
						'default'     => '22',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'title_padding'         => array(
						'type'        => 'unit',
						'label'       => __( 'Title Bottom Padding', 'fl-builder' ),
						'default'     => '0',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'description_color'     => array(
						'type'       => 'color',
						'label'      => __( 'Description Color', 'fl-builder' ),
						'default'    => '000000',
						'show_reset' => true,
					),
					'description_font_size' => array(
						'type'        => 'unit',
						'label'       => __( 'Description Font Size', 'fl-builder' ),
						'default'     => '14',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'description_padding'   => array(
						'type'        => 'unit',
						'label'       => __( 'Description Bottom Padding', 'fl-builder' ),
						'default'     => '10',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'link_color'            => array(
						'type'       => 'color',
						'label'      => __( 'Link Color', 'fl-builder' ),
						'default'    => '2ea3f2',
						'show_reset' => true,
					),
					'link_hover_color'      => array(
						'type'       => 'color',
						'label'      => __( 'Link Hover Color', 'fl-builder' ),
						'default'    => '2ea3f2',
						'show_reset' => true,
					),
				),
			),
		),
	),
	'content'    => array(
		'title' => __( 'Content', 'fl-builder' ),
		'file'  => SMP_PATH . 'includes/shortcodes/beaver/sermon-blog/loop-settings.php',
	),
	'pagination' => array(
		'title'    => __( 'Pagination', 'fl-builder' ),
		'sections' => array(
			'pagination' => array(
				'title'  => __( 'Pagination', 'fl-builder' ),
				'fields' => array(
					'sermons_per_page'     => array(
						'type'    => 'unit',
						'label'   => __( 'Sermons Per Page', 'fl-builder' ),
						'default' => '9',
					),
					'show_pagination'      => array(
						'type'    => 'select',
						'label'   => __( 'Show Pagination', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'pagination_total_num', 'show_prev_next', 'previous_label', 'next_label', 'pagination_alignment' ),
							),
						),
					),
					'pagination_total_num' => array(
						'type'    => 'unit',
						'label'   => __( 'Pagination Total Pages', 'fl-builder' ),
						'default' => '5',
					),
					'show_prev_next'       => array(
						'type'    => 'select',
						'label'   => __( 'Prev/Next Links', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'previous_label', 'next_label' ),
							),
						),
					),
					'previous_label'       => array(
						'type'    => 'text',
						'label'   => __( 'Previous Label', 'fl-builder' ),
						'default' => '&laquo; Previous',
					),
					'next_label'           => array(
						'type'    => 'text',
						'label'   => __( 'Next Label', 'fl-builder' ),
						'default' => 'Next &raquo;',
					),
					'pagination_alignment'       => array(
						'type'    => 'select',
						'label'   => __( 'Pagination Alignment', 'fl-builder' ),
						'default' => 'left',
						'options' => array(
							'left' 	 => __( 'Left', 'fl-builder' ),
							'center' => __( 'Center', 'fl-builder' ),
							'right'  => __( 'Right', 'fl-builder' ),
						),
					),
				),
			),
		),
	),
) );
