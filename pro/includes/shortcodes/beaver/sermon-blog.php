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
			'name'            => __( 'Sermons', 'church-sermon-manager' ),
			'description'     => __( 'Display a grid of your Sermons.', 'church-sermon-manager' ),
			'category'        => __( 'Posts', 'church-sermon-manager' ),
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
		'title'    => __( 'Layout', 'church-sermon-manager' ),
		'sections' => array(
			'general'     => array(
				'title'  => '',
				'fields' => array(
					'layout' => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'church-sermon-manager' ),
						'default' => 'columns',
						'options' => array(
							'columns' => __( 'Columns', 'church-sermon-manager' ),
							'list'    => __( 'List', 'church-sermon-manager' ),
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
				'title'  => __( 'Sermons', 'church-sermon-manager' ),
				'fields' => array(
					'match_height'        => array(
						'type'    => 'select',
						'label'   => __( 'Equal Heights', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Yes', 'church-sermon-manager' ),
							'0' => __( 'No', 'church-sermon-manager' ),
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
						'label'   => __( 'Masonry', 'church-sermon-manager' ),
						'default' => '0',
						'options' => array(
							'1' => __( 'On', 'church-sermon-manager' ),
							'0' => __( 'Off', 'church-sermon-manager' ),
						),
						'toggle'  => array(
							'0' => array(
								'fields' => array( 'sermon_spacing' , 'sermon_margin' ),
							),
						),
					),
					'sermon_columns'      => array(
						'type'       => 'unit',
						'label'      => __( 'Columns', 'church-sermon-manager' ),
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
						'label'       => __( 'Spacing Between Columns', 'church-sermon-manager' ),
						'default'     => '30',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'list_sermon_spacing' => array(
						'type'        => 'unit',
						'label'       => __( 'Spacing Between Sermons', 'church-sermon-manager' ),
						'default'     => '40',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'sermon_margin'       => array(
						'type'        => 'unit',
						'label'       => __( 'Sermon Bottom Margin', 'church-sermon-manager' ),
						'default'     => '20',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
				),
			),
			'image'       => array(
				'title'  => __( 'Featured Image', 'church-sermon-manager' ),
				'fields' => array(
					'featured_type' => array(
						'type'    => 'select',
						'label'   => __( 'Featured Type', 'church-sermon-manager' ),
						'default' => 'image',
						'options' => array(
							'image' => __( 'Image', 'church-sermon-manager' ),
							'video' => __( 'Video', 'church-sermon-manager' ),
							'none'  => __( 'None', 'church-sermon-manager' ),
						),
						'toggle'  => array(
							'image' => array(
								'fields' => array( 'image_spacing' ),
							),
						),
					),
					'image_spacing' => array(
						'type'        => 'dimension',
						'label'       => __( 'Image Spacing', 'church-sermon-manager' ),
						'default'     => '0',
						'description' => 'px',
					),
				),
			),
			'info'        => array(
				'title'  => __( 'Sermon Info', 'church-sermon-manager' ),
				'fields' => array(
					'show_series'         => array(
						'type'    => 'select',
						'label'   => __( 'Series', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
					),
					'show_title'          => array(
						'type'    => 'select',
						'label'   => __( 'Title', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
					),
					'show_date'           => array(
						'type'    => 'select',
						'label'   => __( 'Date', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'date_format' ),
							),
						),
					),
					'date_format'         => array(
						'type'    => 'select',
						'label'   => __( 'Date Format', 'church-sermon-manager' ),
						'default' => 'M j, Y',
						'options' => array(
							'M j, Y' => gmdate( 'M j, Y' ),
							'F j, Y' => gmdate( 'F j, Y' ),
							'm/d/Y'  => gmdate( 'm/d/Y' ),
							'm-d-Y'  => gmdate( 'm-d-Y' ),
							'd M Y'  => gmdate( 'd M Y' ),
							'd F Y'  => gmdate( 'd F Y' ),
							'Y-m-d'  => gmdate( 'Y-m-d' ),
							'Y/m/d'  => gmdate( 'Y/m/d' ),
						),
					),
					'show_audio'          => array(
						'type'    => 'select',
						'label'   => __( 'Audio', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'show_download_audio' ),
							),
						),
					),
					'show_download_audio' => array(
						'type'    => 'select',
						'label'   => __( 'Audio Download Link', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
					),
					'show_preacher'       => array(
						'type'    => 'select',
						'label'   => __( 'Preacher', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
					),
					'show_passage'        => array(
						'type'    => 'select',
						'label'   => __( 'Bible Passage', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
					),
					'show_service_type'   => array(
						'type'    => 'select',
						'label'   => __( 'Service Type', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
					),
				),
			),
			'description' => array(
				'title'  => __( 'Description', 'church-sermon-manager' ),
				'fields' => array(
					'show_description'   => array(
						'type'    => 'select',
						'label'   => __( 'Description', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'description_length', 'show_more_link', 'more_link_text' ),
							),
						),
					),
					'description_length' => array(
						'type'        => 'unit',
						'label'       => __( 'Description Length', 'church-sermon-manager' ),
						'default'     => '30',
						'description' => __( 'words', 'church-sermon-manager' ),
					),
					'show_more_link'     => array(
						'type'    => 'select',
						'label'   => __( 'More Link', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'more_link_text' ),
							),
						),
					),
					'more_link_text'     => array(
						'type'    => 'text',
						'label'   => __( 'More Link Text', 'church-sermon-manager' ),
						'default' => __( 'Read More', 'church-sermon-manager' ),
					),
				),
			),
		),
	),
	'filters'    => array(
		'title'    => __( 'Filters', 'church-sermon-manager' ),
		'sections' => array(
			'sm_filters' => array(
				'title'  => 'Filters',
				'fields' => array(
					'show_filters'             => array(
						'type'    => 'select',
						'label'   => __( 'Filters', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
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
						'label'       => __( 'Filter Bottom Margin', 'church-sermon-manager' ),
						'default'     => '20',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'show_filter_topics'       => array(
						'type'    => 'select',
						'label'   => __( 'Filter Topics', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
					),
					'show_filter_series'       => array(
						'type'    => 'select',
						'label'   => __( 'Filter Series', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
					),
					'show_filter_preacher'     => array(
						'type'    => 'select',
						'label'   => __( 'Filter Preacher', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
					),
					'show_filter_book'         => array(
						'type'    => 'select',
						'label'   => __( 'Filter Books', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
					),
					'show_filter_service_type' => array(
						'type'    => 'select',
						'label'   => __( 'Filter Service Types', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
					),
				),
			),
		),
	),
	'style'      => array(
		'title'    => __( 'Style', 'church-sermon-manager' ),
		'sections' => array(
			'post_style' => array(
				'title'  => __( 'Sermons', 'church-sermon-manager' ),
				'fields' => array(
					'bg_color'     => array(
						'type'       => 'color',
						'label'      => __( 'Sermon Background Color', 'church-sermon-manager' ),
						'show_reset' => true,
						'default'    => 'ffffff',
					),
					'border_type'  => array(
						'type'    => 'select',
						'label'   => __( 'Sermon Border Type', 'church-sermon-manager' ),
						'default' => 'solid',
						'options' => array(
							'solid'  => _x( 'Solid', 'Border type.', 'church-sermon-manager' ),
							'dashed' => _x( 'Dashed', 'Border type.', 'church-sermon-manager' ),
							'dotted' => _x( 'Dotted', 'Border type.', 'church-sermon-manager' ),
							'double' => _x( 'Double', 'Border type.', 'church-sermon-manager' ),
							'none'   => _x( 'None', 'Border type.', 'church-sermon-manager' ),
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
						'label'      => __( 'Sermon Border Color', 'church-sermon-manager' ),
						'default'    => 'dddddd',
						'show_reset' => true,
					),
					'border_size'  => array(
						'type'        => 'unit',
						'label'       => __( 'Sermon Border Size', 'church-sermon-manager' ),
						'default'     => '1',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
				),
			),
			'text_style' => array(
				'title'  => __( 'Text', 'church-sermon-manager' ),
				'fields' => array(
					'title_color'           => array(
						'type'       => 'color',
						'label'      => __( 'Title Color', 'church-sermon-manager' ),
						'default'    => '000000',
						'show_reset' => true,
					),
					'title_font_size'       => array(
						'type'        => 'unit',
						'label'       => __( 'Title Font Size', 'church-sermon-manager' ),
						'default'     => '22',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'title_padding'         => array(
						'type'        => 'unit',
						'label'       => __( 'Title Bottom Padding', 'church-sermon-manager' ),
						'default'     => '0',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'description_color'     => array(
						'type'       => 'color',
						'label'      => __( 'Description Color', 'church-sermon-manager' ),
						'default'    => '000000',
						'show_reset' => true,
					),
					'description_font_size' => array(
						'type'        => 'unit',
						'label'       => __( 'Description Font Size', 'church-sermon-manager' ),
						'default'     => '14',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'description_padding'   => array(
						'type'        => 'unit',
						'label'       => __( 'Description Bottom Padding', 'church-sermon-manager' ),
						'default'     => '10',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'link_color'            => array(
						'type'       => 'color',
						'label'      => __( 'Link Color', 'church-sermon-manager' ),
						'default'    => '2ea3f2',
						'show_reset' => true,
					),
					'link_hover_color'      => array(
						'type'       => 'color',
						'label'      => __( 'Link Hover Color', 'church-sermon-manager' ),
						'default'    => '2ea3f2',
						'show_reset' => true,
					),
				),
			),
		),
	),
	'content'    => array(
		'title' => __( 'Content', 'church-sermon-manager' ),
		'file'  => SMP_PATH . 'includes/shortcodes/beaver/sermon-blog/loop-settings.php',
	),
	'pagination' => array(
		'title'    => __( 'Pagination', 'church-sermon-manager' ),
		'sections' => array(
			'pagination' => array(
				'title'  => __( 'Pagination', 'church-sermon-manager' ),
				'fields' => array(
					'sermons_per_page'     => array(
						'type'    => 'unit',
						'label'   => __( 'Sermons Per Page', 'church-sermon-manager' ),
						'default' => '9',
					),
					'show_pagination'      => array(
						'type'    => 'select',
						'label'   => __( 'Show Pagination', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'pagination_total_num', 'show_prev_next', 'previous_label', 'next_label', 'pagination_alignment' ),
							),
						),
					),
					'pagination_total_num' => array(
						'type'    => 'unit',
						'label'   => __( 'Pagination Total Pages', 'church-sermon-manager' ),
						'default' => '5',
					),
					'show_prev_next'       => array(
						'type'    => 'select',
						'label'   => __( 'Prev/Next Links', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'previous_label', 'next_label' ),
							),
						),
					),
					'previous_label'       => array(
						'type'    => 'text',
						'label'   => __( 'Previous Label', 'church-sermon-manager' ),
						'default' => '&laquo; Previous',
					),
					'next_label'           => array(
						'type'    => 'text',
						'label'   => __( 'Next Label', 'church-sermon-manager' ),
						'default' => 'Next &raquo;',
					),
					'pagination_alignment'       => array(
						'type'    => 'select',
						'label'   => __( 'Pagination Alignment', 'church-sermon-manager' ),
						'default' => 'left',
						'options' => array(
							'left' 	 => __( 'Left', 'church-sermon-manager' ),
							'center' => __( 'Center', 'church-sermon-manager' ),
							'right'  => __( 'Right', 'church-sermon-manager' ),
						),
					),
				),
			),
		),
	),
) );
