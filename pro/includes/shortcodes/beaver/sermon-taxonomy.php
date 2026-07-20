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
			'name'            => __( 'Sermon Taxonomies', 'church-sermon-manager' ),
			'description'     => __( 'Display a grid of your Sermon Taxonomies.', 'church-sermon-manager' ),
			'category'        => __( 'Posts', 'church-sermon-manager' ),
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
		'title'    => __( 'Layout', 'church-sermon-manager' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'taxonomy_layout' => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'church-sermon-manager' ),
						'default' => 'grid',
						'options' => array(
							'grid' => __( 'Grid', 'church-sermon-manager' ),
							'list' => __( 'List', 'church-sermon-manager' ),
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
				'title'  => __( 'Terms', 'church-sermon-manager' ),
				'fields' => array(
					'term_columns' => array(
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
					'term_spacing' => array(
						'type'        => 'unit',
						'label'       => __( 'Spacing Between Columns', 'church-sermon-manager' ),
						'default'     => '30',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'term_margin'  => array(
						'type'        => 'unit',
						'label'       => __( 'Term Bottom Margin', 'church-sermon-manager' ),
						'default'     => '30',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
				),
			),
			'image'   => array(
				'title'  => __( 'Featured Image', 'church-sermon-manager' ),
				'fields' => array(
					'show_term_image'    => array(
						'type'    => 'select',
						'label'   => __( 'Image', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'term_image_padding' ),
							),
						),
					),
					'term_image_padding' => array(
						'type'        => 'unit',
						'label'       => __( 'Image Bottom Padding', 'church-sermon-manager' ),
						'default'     => '10',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
				),
			),
			'content' => array(
				'title'  => __( 'Content', 'church-sermon-manager' ),
				'fields' => array(
					'show_term_title'         => array(
						'type'    => 'select',
						'label'   => __( 'Title', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
					),
					'show_term_description'   => array(
						'type'    => 'select',
						'label'   => __( 'Description', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
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
						'label'       => __( 'Content Length', 'church-sermon-manager' ),
						'default'     => '30',
						'description' => __( 'words', 'church-sermon-manager' ),
					),
					'show_term_more_link'     => array(
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
					'term_more_link_text'     => array(
						'type'    => 'text',
						'label'   => __( 'More Link Text', 'church-sermon-manager' ),
						'default' => __( 'Read More', 'church-sermon-manager' ),
					),
					'show_alphabetical_list'  => array(
						'type'    => 'select',
						'label'   => __( 'Show Alphabetical List', 'church-sermon-manager' ),
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
			'term_style'      => array(
				'title'  => __( 'Terms', 'church-sermon-manager' ),
				'fields' => array(
					'bg_color'     => array(
						'type'       => 'color',
						'label'      => __( 'Term Background Color', 'church-sermon-manager' ),
						'show_reset' => true,
						'default'    => 'ffffff',
					),
					'border_type'  => array(
						'type'    => 'select',
						'label'   => __( 'Term Border Type', 'church-sermon-manager' ),
						'default' => 'none',
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
						'label'      => __( 'Term Border Color', 'church-sermon-manager' ),
						'default'    => 'dddddd',
						'show_reset' => true,
					),
					'border_size'  => array(
						'type'        => 'unit',
						'label'       => __( 'Term Border Size', 'church-sermon-manager' ),
						'default'     => '1',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
				),
			),
			'term_text_style' => array(
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
						'default'     => '18',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'title_padding'         => array(
						'type'        => 'unit',
						'label'       => __( 'Title Bottom Padding', 'church-sermon-manager' ),
						'default'     => '10',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'title_alignment'       => array(
						'type'    => 'select',
						'label'   => __( 'Title Alignment', 'church-sermon-manager' ),
						'default' => 'center',
						'options' => array(
							'center'  => _x( 'Center', 'Border type.', 'church-sermon-manager' ),
							'left'    => _x( 'Left', 'Border type.', 'church-sermon-manager' ),
							'right'   => _x( 'Right', 'Border type.', 'church-sermon-manager' ),
							'justify' => _x( 'Justify', 'Border type.', 'church-sermon-manager' ),
						),
					),
					'content_spacing'       => array(
						'type'        => 'unit',
						'label'       => __( 'Content Spacing', 'church-sermon-manager' ),
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
					'description_alignment' => array(
						'type'    => 'select',
						'label'   => __( 'Description Alignment', 'church-sermon-manager' ),
						'default' => 'left',
						'options' => array(
							'left'    => _x( 'Left', 'Border type.', 'church-sermon-manager' ),
							'right'   => _x( 'Right', 'Border type.', 'church-sermon-manager' ),
							'center'  => _x( 'Center', 'Border type.', 'church-sermon-manager' ),
							'justify' => _x( 'Justify', 'Border type.', 'church-sermon-manager' ),
						),
					),
					'letter_color'          => array(
						'type'       => 'color',
						'label'      => __( 'Letter Color', 'church-sermon-manager' ),
						'default'    => '000000',
						'show_reset' => true,
					),
					'letter_font_size'      => array(
						'type'        => 'unit',
						'label'       => __( 'Letter Font Size', 'church-sermon-manager' ),
						'default'     => '22',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'letter_top_padding'    => array(
						'type'        => 'unit',
						'label'       => __( 'Letter Top Padding', 'church-sermon-manager' ),
						'default'     => '10',
						'maxlength'   => '3',
						'size'        => '4',
						'description' => 'px',
					),
					'letter_bottom_padding' => array(
						'type'        => 'unit',
						'label'       => __( 'Letter Bottom Padding', 'church-sermon-manager' ),
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
		'title'    => __( 'Pagination', 'church-sermon-manager' ),
		'sections' => array(
			'pagination' => array(
				'title'  => __( 'Pagination', 'church-sermon-manager' ),
				'fields' => array(
					'show_taxonomy'             => array(
						'type'    => 'select',
						'label'   => __( 'Source', 'church-sermon-manager' ),
						'default' => 'wpfc_sermon_series',
						'options' => array(
							'wpfc_sermon_series' => __( 'Series', 'church-sermon-manager' ),
							'wpfc_preacher'      => __( 'Preachers', 'church-sermon-manager' ),
							'wpfc_sermon_topics' => __( 'Topics', 'church-sermon-manager' ),
							'wpfc_bible_book'    => __( 'Books', 'church-sermon-manager' ),
							'wpfc_service_type'  => __( 'Service Types', 'church-sermon-manager' ),
						),
					),
					'taxonomy_number'           => array(
						'type'    => 'unit',
						'label'   => __( 'Terms Per Page', 'church-sermon-manager' ),
						'default' => '9',
					),
					'show_term_pagination'      => array(
						'type'    => 'select',
						'label'   => __( 'Show Pagination', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'term_pagination_total_num', 'term_show_prev_next', 'term_previous_label', 'term_next_label', 'term_pagination_alignment' ),
							),
						),
					),
					'term_pagination_total_num' => array(
						'type'    => 'unit',
						'label'   => __( 'Pagination Total Pages', 'church-sermon-manager' ),
						'default' => '5',
					),
					'term_show_prev_next'       => array(
						'type'    => 'select',
						'label'   => __( 'Prev/Next Links', 'church-sermon-manager' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'church-sermon-manager' ),
							'0' => __( 'Hide', 'church-sermon-manager' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'term_previous_label', 'term_next_label' ),
							),
						),
					),
					'term_previous_label'       => array(
						'type'    => 'text',
						'label'   => __( 'Previous Label', 'church-sermon-manager' ),
						'default' => '&laquo; Previous',
					),
					'term_next_label'           => array(
						'type'    => 'text',
						'label'   => __( 'Next Label', 'church-sermon-manager' ),
						'default' => 'Next &raquo;',
					),
					'term_pagination_alignment'       => array(
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
