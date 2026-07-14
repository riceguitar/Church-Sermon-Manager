<?php
/**
 * Sermon Taxonomy module register code.
 *
 * @since   1.0.0-beta.2
 *
 * @package SMP\Shortcodes\Beaver
 */

namespace SMP\Shortcodes\Beaver;

defined( 'ABSPATH' ) or exit;

/**
 * Define Sermon Taxonomy module.
 */
class Sermon_Taxonomy extends \FLBuilderModule {
	/**
	 * Sermon_Taxonomy constructor.
	 */
	public function __construct() {
		parent::__construct( array(
			'name'            => __( 'Sermon Taxonomies', 'fl-builder' ),
			'description'     => __( 'Display a grid of your Sermon Taxonomies.', 'fl-builder' ),
			'category'        => __( 'Posts', 'fl-builder' ),
			'dir'             => SMP_PATH . 'includes/shortcodes/beaver/sermon-taxonomy/',
			'url'             => SMP_URL . 'includes/shortcodes/beaver/sermon-taxonomy/',
			'icon'            => 'schedule.svg',
			'editor_export'   => false,
			'partial_refresh' => true,
			'enabled'         => true,
		) );

		// Enqueue the CSS.
		$this->add_css( 'sm_pro_beaver_taxonomy', SMP_URL . 'assets/css/shortcodes/beaver/sermon-taxonomy.css', array(), SMP_VERSION );
	}
}

\FLBuilder::register_module( '\SMP\Shortcodes\Beaver\Sermon_Taxonomy', array(
	'layout'     => array(
		'title'    => __( 'Layout', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'taxonomy_layout' => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'grid',
						'options' => array(
							'grid' => __( 'Grid', 'fl-builder' ),
							'list' => __( 'List', 'fl-builder' ),
						),
						'toggle'  => array(
							'grid' => array(
								'sections' => array( 'terms', 'image', 'content', 'term_style', 'term_text_style' ),
								'fields'   => array(
									'show_term_title',
									'show_term_description',
									'term_description_length',
									'show_term_more_link',
									'term_more_link_text',
									'content_spacing',
									'title_alignment',
									'description_color',
									'description_font_size',
									'description_padding',
									'description_alignment',
								),
							),
							'list' => array(
								'sections' => array( 'terms', 'content', 'term_text_style' ),
								'fields'   => array(
									'show_alphabetical_list',
									'letter_color',
									'letter_font_size',
									'letter_top_padding',
									'letter_bottom_padding',
								),
							),
						),
					),
				),
			),
			'terms'   => array(
				'title'  => __( 'Terms', 'fl-builder' ),
				'fields' => array(
					'term_columns' => array(
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
					'term_spacing' => array(
						'type'        => 'unit',
						'label'       => __( 'Spacing Between Columns', 'fl-builder' ),
						'default'     => '30',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'term_margin'  => array(
						'type'        => 'unit',
						'label'       => __( 'Term Bottom Margin', 'fl-builder' ),
						'default'     => '30',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
				),
			),
			'image'   => array(
				'title'  => __( 'Featured Image', 'fl-builder' ),
				'fields' => array(
					'show_term_image'    => array(
						'type'    => 'select',
						'label'   => __( 'Image', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'term_image_padding' ),
							),
						),
					),
					'term_image_padding' => array(
						'type'        => 'unit',
						'label'       => __( 'Image Bottom Padding', 'fl-builder' ),
						'default'     => '10',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
				),
			),
			'content' => array(
				'title'  => __( 'Content', 'fl-builder' ),
				'fields' => array(
					'show_term_title'         => array(
						'type'    => 'select',
						'label'   => __( 'Title', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'show_term_description'   => array(
						'type'    => 'select',
						'label'   => __( 'Description', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array(
									'term_description_length',
									'show_term_more_link',
									'term_more_link_text',
								),
							),
						),
					),
					'term_description_length' => array(
						'type'        => 'unit',
						'label'       => __( 'Content Length', 'fl-builder' ),
						'default'     => '30',
						'description' => __( 'words', 'fl-builder' ),
					),
					'show_term_more_link'     => array(
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
					'term_more_link_text'     => array(
						'type'    => 'text',
						'label'   => __( 'More Link Text', 'fl-builder' ),
						'default' => __( 'Read More', 'fl-builder' ),
					),
					'show_alphabetical_list'  => array(
						'type'    => 'select',
						'label'   => __( 'Show Alphabetical List', 'fl-builder' ),
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
			'term_style'      => array(
				'title'  => __( 'Terms', 'fl-builder' ),
				'fields' => array(
					'bg_color'     => array(
						'type'       => 'color',
						'label'      => __( 'Term Background Color', 'fl-builder' ),
						'show_reset' => true,
						'default'    => 'ffffff',
					),
					'border_type'  => array(
						'type'    => 'select',
						'label'   => __( 'Term Border Type', 'fl-builder' ),
						'default' => 'none',
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
						'label'      => __( 'Term Border Color', 'fl-builder' ),
						'default'    => 'dddddd',
						'show_reset' => true,
					),
					'border_size'  => array(
						'type'        => 'unit',
						'label'       => __( 'Term Border Size', 'fl-builder' ),
						'default'     => '1',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
				),
			),
			'term_text_style' => array(
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
						'default'     => '18',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'title_padding'         => array(
						'type'        => 'unit',
						'label'       => __( 'Title Bottom Padding', 'fl-builder' ),
						'default'     => '10',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'title_alignment'       => array(
						'type'    => 'select',
						'label'   => __( 'Title Alignment', 'fl-builder' ),
						'default' => 'center',
						'options' => array(
							'center'  => _x( 'Center', 'Border type.', 'fl-builder' ),
							'left'    => _x( 'Left', 'Border type.', 'fl-builder' ),
							'right'   => _x( 'Right', 'Border type.', 'fl-builder' ),
							'justify' => _x( 'Justify', 'Border type.', 'fl-builder' ),
						),
					),
					'content_spacing'       => array(
						'type'        => 'unit',
						'label'       => __( 'Content Spacing', 'fl-builder' ),
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
					'description_alignment' => array(
						'type'    => 'select',
						'label'   => __( 'Description Alignment', 'fl-builder' ),
						'default' => 'left',
						'options' => array(
							'left'    => _x( 'Left', 'Border type.', 'fl-builder' ),
							'right'   => _x( 'Right', 'Border type.', 'fl-builder' ),
							'center'  => _x( 'Center', 'Border type.', 'fl-builder' ),
							'justify' => _x( 'Justify', 'Border type.', 'fl-builder' ),
						),
					),
					'letter_color'          => array(
						'type'       => 'color',
						'label'      => __( 'Letter Color', 'fl-builder' ),
						'default'    => '000000',
						'show_reset' => true,
					),
					'letter_font_size'      => array(
						'type'        => 'unit',
						'label'       => __( 'Letter Font Size', 'fl-builder' ),
						'default'     => '22',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'letter_top_padding'    => array(
						'type'        => 'unit',
						'label'       => __( 'Letter Top Padding', 'fl-builder' ),
						'default'     => '10',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'letter_bottom_padding' => array(
						'type'        => 'unit',
						'label'       => __( 'Letter Bottom Padding', 'fl-builder' ),
						'default'     => '5',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
				),
			),
		),
	),
	'pagination' => array(
		'title'    => __( 'Pagination', 'fl-builder' ),
		'sections' => array(
			'pagination' => array(
				'title'  => __( 'Pagination', 'fl-builder' ),
				'fields' => array(
					'show_taxonomy'             => array(
						'type'    => 'select',
						'label'   => __( 'Source', 'fl-builder' ),
						'default' => 'wpfc_sermon_series',
						'options' => array(
							'wpfc_sermon_series' => __( 'Series', 'fl-builder' ),
							'wpfc_preacher'      => __( 'Preachers', 'fl-builder' ),
							'wpfc_sermon_topics' => __( 'Topics', 'fl-builder' ),
							'wpfc_bible_book'    => __( 'Books', 'fl-builder' ),
							'wpfc_service_type'  => __( 'Service Types', 'fl-builder' ),
						),
					),
					'taxonomy_number'           => array(
						'type'    => 'unit',
						'label'   => __( 'Terms Per Page', 'fl-builder' ),
						'default' => '9',
					),
					'show_term_pagination'      => array(
						'type'    => 'select',
						'label'   => __( 'Show Pagination', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'term_pagination_total_num', 'term_show_prev_next', 'term_previous_label', 'term_next_label', 'term_pagination_alignment' ),
							),
						),
					),
					'term_pagination_total_num' => array(
						'type'    => 'unit',
						'label'   => __( 'Pagination Total Pages', 'fl-builder' ),
						'default' => '5',
					),
					'term_show_prev_next'       => array(
						'type'    => 'select',
						'label'   => __( 'Prev/Next Links', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'term_previous_label', 'term_next_label' ),
							),
						),
					),
					'term_previous_label'       => array(
						'type'    => 'text',
						'label'   => __( 'Previous Label', 'fl-builder' ),
						'default' => '&laquo; Previous',
					),
					'term_next_label'           => array(
						'type'    => 'text',
						'label'   => __( 'Next Label', 'fl-builder' ),
						'default' => 'Next &raquo;',
					),
					'term_pagination_alignment'       => array(
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
