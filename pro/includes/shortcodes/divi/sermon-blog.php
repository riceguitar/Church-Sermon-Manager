<?php
/**
 * Sermon blog element for Divi builder.
 *
 * @since   1.0.0-beta.0
 *
 * @package SMP\Shortcodes\Divi
 */

namespace SMP\Shortcodes\Divi;

use ET_Builder_Module;

/**
 * Class Sermon_Blog
 *
 * @package SMP\Shortcodes\Divi
 */
class Sermon_Blog extends ET_Builder_Module {
	/**
	 * The module slug.
	 *
	 * @var string $slug
	 */
	public $slug = 'smp_sermon_blog';

	/**
	 * Facebook support. (or Frontpage Builder?)
	 *
	 * @var bool $fb_support
	 */
	public $fb_support = true;

	/**
	 * Visual Builder support
	 *
	 * @var string $vb_support
	 */
	public $vb_support = 'on';

	/**
	 * Whitelisted fields.
	 *
	 * @var array $whitelisted_fields
	 */
	public $whitelisted_fields = array();

	/**
	 * Field defaults.
	 *
	 * @var    array $fields_defaults
	 */
	public $fields_defaults = array();

	/**
	 * Advanced options.
	 *
	 * @var array $advanced_options
	 */
	public $advanced_options = array();

	/**
	 * Option toggles.
	 *
	 * @var array $options_toggles
	 */
	public $options_toggles = array();

	/**
	 * Function init()
	 */
	function init() {
		$this->name = __( 'Sermons', 'sermon-manager' );

		$this->whitelisted_fields = array(
			'sermons_number',
			'sermons_order',
			'meta_date',
			'show_image',
			'show_series',
			'show_date',
			'show_title',
			'show_excerpt',
			'excerpt_length',
			'show_readmore',
			'read_more_text',
			'show_sermon_audio',
			'show_preacher',
			'show_passage',
			'show_service_type',
			'show_grid',
			'show_masonry',
			'grid_columns',
			'spacing_columns',
			'show_pagination',
			'pagination_total_num',
			'show_prev_next',
			'previous_label',
			'next_label',
			'pagination_alignment',
			'title_padding',
			'description_padding',
			'show_filters',
			'show_filter_preacher',
			'show_filter_series',
			'show_filter_book',
			'show_filter_service_type',
			'show_filter_topics',
			'featured_type',
		);

		$this->main_css_element = '%%order_class%% .wpfc_sermon';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'sermon-manager' ),
					'elements'     => esc_html__( 'Elements', 'sermon-manager' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => esc_html__( 'Layout', 'sermon-manager' ),
					'text'   => array(
						'title'    => esc_html__( 'Text', 'sermon-manager' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->fields_defaults = array(
			'sermons_number'           => array( 10, 'add_default_setting' ),
			'sermons_order '           => array( 'DESC' ),
			'meta_date'                => array( 'F j, Y', 'add_default_setting' ),
			'show_image'               => array( 'on' ),
			'show_series'              => array( 'on' ),
			'show_date'                => array( 'on' ),
			'show_title'               => array( 'on' ),
			'show_excerpt'             => array( 'on' ),
			'excerpt_length'           => array( 30, 'add_default_setting' ),
			'show_readmore'            => array( 'on' ),
			'read_more_text'           => array( 'Read More', 'add_default_setting' ),
			'show_sermon_audio'        => array( 'off' ),
			'show_preacher'            => array( 'on' ),
			'show_passage'             => array( 'on' ),
			'show_service_type'        => array( 'on' ),
			'show_grid'                => array( 'off' ),
			'show_masonry'             => array( 'off' ),
			'grid_columns'             => array( 3, 'add_default_setting' ),
			'spacing_columns'          => array( 30, 'add_default_setting' ),
			'show_pagination'          => array( 'off' ),
			'pagination_total_num'     => array( 5, 'add_default_setting' ),
			'show_prev_next'           => array( 'off' ),
			'previous_label'           => array( '&laquo; Previous', 'add_default_setting' ),
			'next_label'               => array( 'Next &raquo;', 'add_default_setting' ),
			'pagination_alignment'     => array( 'left', 'add_default_setting' ),
			'title_padding'            => array( 10, 'add_default_setting' ),
			'description_padding'      => array( 10, 'add_default_setting' ),
			'show_filters'             => array( 'off' ),
			'show_filter_preacher'     => array( 'on' ),
			'show_filter_series'       => array( 'on' ),
			'show_filter_book'         => array( 'on' ),
			'show_filter_service_type' => array( 'on' ),
			'show_filter_topics'       => array( 'on' ),
			'featured_type'            => array( 'image' ),
		);
	}

	/**
	 * Returns the module fields.
	 *
	 * @return array The fields.
	 */
	function get_fields() {
		return array(
			'show_filters'             => array(
				'label'           => esc_html__( 'Show Filters', 'sermon-manager' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This will turn filtering on and off.', 'sermon-manager' ),
				'toggle_slug'     => 'main_content',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
			),
			'show_filter_preacher'     => array(
				'label'           => esc_html__( 'Show Preacher', 'sermon-manager' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'main_content',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'show_if'         => array(
					'show_filters' => 'on',
				),

			),
			'show_filter_series'       => array(
				'label'           => esc_html__( 'Show Series', 'sermon-manager' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'main_content',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'show_if'         => array(
					'show_filters' => 'on',
				),

			),
			'show_filter_book'         => array(
				'label'           => esc_html__( 'Show Book', 'sermon-manager' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'main_content',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'show_if'         => array(
					'show_filters' => 'on',
				),

			),
			'show_filter_service_type' => array(
				'label'           => esc_html__( 'Show Service Type', 'sermon-manager' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'main_content',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'show_if'         => array(
					'show_filters' => 'on',
				),

			),
			'show_filter_topics'       => array(
				'label'           => esc_html__( 'Show Topics', 'sermon-manager' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'main_content',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'show_if'         => array(
					'show_filters' => 'on',
				),

			),
			'sermons_number'           => array(
				'label'            => esc_html__( 'Sermons Number', 'sermon-manager' ),
				'type'             => 'number',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'Choose how much sermons you would like to display per page.', 'sermon-manager' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'main_content',
			),
			'sermons_order'            => array(
				'label'           => esc_html__( 'Sermons Order', 'sermon-manager' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'Select order of sermons.', 'sermon-manager' ),
				'options'         => array(
					'DESC' => esc_html__( 'Descending', 'sermon-manager' ),
					'ASC'  => esc_html__( 'Ascending', 'sermon-manager' ),
				),
				'toggle_slug'     => 'main_content',
			),
			'show_image'               => array(
				'label'            => esc_html__( 'Show Featured Image/Video', 'sermon-manager' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'description'      => esc_html__( 'This will turn thumbnails on and off.', 'sermon-manager' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
			),
			'featured_type'            => array(
				'label'           => esc_html__( 'Featured Type', 'sermon-manager' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'Toggle between the featured types.', 'sermon-manager' ),
				'options'         => array(
					'image' => esc_html__( 'Image', 'sermon-manager' ),
					'video' => esc_html__( 'Video', 'sermon-manager' ),
				),
				'toggle_slug'     => 'elements',
				'show_if'         => array(
					'show_image' => 'on',
				),
			),
			'show_series'              => array(
				'label'            => esc_html__( 'Show Series', 'sermon-manager' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'description'      => esc_html__( 'This will turn series name on and off.', 'sermon-manager' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
			),
			'show_title'               => array(
				'label'            => esc_html__( 'Show Title', 'sermon-manager' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'description'      => esc_html__( 'This will turn title on and off.', 'sermon-manager' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
			),
			'show_date'                => array(
				'label'            => esc_html__( 'Show Publish Date', 'sermon-manager' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'description'      => esc_html__( 'This will turn publish date on and off.', 'sermon-manager' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
			),
			'meta_date'                => array(
				'label'            => esc_html__( 'Meta Date Format', 'sermon-manager' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'If you would like to adjust the date format, input the appropriate PHP date format here.', 'sermon-manager' ),
				'toggle_slug'      => 'elements',
				'computed_affects' => array(
					'__posts',
				),
			),
			'show_excerpt'             => array(
				'label'            => esc_html__( 'Show Sermon Excerpt', 'sermon-manager' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'description'      => esc_html__( 'This will turn sermon excerpt on and off.', 'sermon-manager' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
			),
			'excerpt_length'           => array(
				'label'            => esc_html__( 'Excerpt Length', 'sermon-manager' ),
				'type'             => 'number',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'Choose excerpt length.', 'sermon-manager' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
				'show_if'          => array(
					'show_excerpt' => 'on',
				),
			),
			'show_readmore'            => array(
				'label'            => esc_html__( 'Show Read More Button', 'sermon-manager' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'description'      => esc_html__( 'This will turn read more button on and off.', 'sermon-manager' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
				'show_if'          => array(
					'show_excerpt' => 'on',
				),
			),
			'read_more_text'           => array(
				'label'            => esc_html__( 'Read More Text', 'sermon-manager' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'Choose read more text.', 'sermon-manager' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
				'show_if'          => array(
					'show_excerpt'  => 'on',
					'show_readmore' => 'on',
				),
			),
			'show_sermon_audio'        => array(
				'label'            => esc_html__( 'Show Sermon Audio', 'sermon-manager' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'description'      => esc_html__( 'This will turn sermon audio on and off.', 'sermon-manager' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
			),
			'show_preacher'            => array(
				'label'            => esc_html__( 'Show Preacher', 'sermon-manager' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'description'      => esc_html__( 'This will turn preacher name on and off.', 'sermon-manager' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
			),
			'show_passage'             => array(
				'label'            => esc_html__( 'Show Passage', 'sermon-manager' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'description'      => esc_html__( 'This will turn passage name on and off.', 'sermon-manager' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
			),
			'show_service_type'        => array(
				'label'            => esc_html__( 'Show Service Type', 'sermon-manager' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'description'      => esc_html__( 'This will turn service type on and off.', 'sermon-manager' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
			),
			'show_grid'                => array(
				'label'           => esc_html__( 'Layout', 'sermon-manager' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'description'     => esc_html__( 'Toggle between the various sermons layout types.', 'sermon-manager' ),
				'toggle_slug'     => 'layout',
				'tab_slug'        => 'advanced',
				'options'         => array(
					'on'  => esc_html__( 'Grid', 'sermon-manager' ),
					'off' => esc_html__( 'Fullwidth', 'sermon-manager' ),
				),
			),
			'show_masonry'             => array(
				'label'           => esc_html__( 'Masonry', 'sermon-manager' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'toggle_slug'     => 'layout',
				'tab_slug'        => 'advanced',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'show_if'         => array(
					'show_grid' => 'on',
				),
			),
			'title_padding'            => array(
				'label'           => esc_html__( 'Sermon Title Bottom Padding', 'sermon-manager' ),
				'type'            => 'range',
				'option_category' => 'text',
				'toggle_slug'     => 'header',
				'tab_slug'        => 'advanced',
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '100',
					'step' => '1',
				),
			),
			'description_padding'      => array(
				'label'           => esc_html__( 'Sermon Descritpion Bottom Padding', 'sermon-manager' ),
				'type'            => 'range',
				'option_category' => 'text',
				'toggle_slug'     => 'body',
				'tab_slug'        => 'advanced',
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '100',
					'step' => '1',
				),
			),
			'grid_columns'             => array(
				'label'           => esc_html__( 'Number of Columns', 'sermon-manager' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'description'     => esc_html__( 'Choose how many columns to display.', 'sermon-manager' ),
				'toggle_slug'     => 'layout',
				'tab_slug'        => 'advanced',
				'range_settings'  => array(
					'min'  => '2',
					'max'  => '6',
					'step' => '1',
				),
				'show_if'         => array(
					'show_grid' => 'on',
				),

			),
			'spacing_columns'          => array(
				'label'            => esc_html__( 'Spacing Between Columns', 'sermon-manager' ),
				'type'             => 'number',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'Choose spacing between columns in px.', 'sermon-manager' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'layout',
				'tab_slug'         => 'advanced',
				'show_if'          => array(
					'show_grid'	   => 'on',
					'show_masonry' => 'off',
				),
			),
			'show_pagination'          => array(
				'label'           => esc_html__( 'Show Pagination', 'sermon-manager' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This will turn pagination on and off.', 'sermon-manager' ),
				'toggle_slug'     => 'main_content',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
			),
			'pagination_total_num'     => array(
				'label'           => esc_html__( 'Page Limit', 'sermon-manager' ),
				'type'            => 'number',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Choose how many pages to display.', 'sermon-manager' ),
				'toggle_slug'     => 'main_content',
				'show_if'         => array(
					'show_pagination' => 'on',
				),

			),
			'show_prev_next'           => array(
				'label'           => esc_html__( 'Show Prev/Next Links', 'sermon-manager' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This will turn prev/next links on and off.', 'sermon-manager' ),
				'toggle_slug'     => 'main_content',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'sermon-manager' ),
					'off' => esc_html__( 'No', 'sermon-manager' ),
				),
				'show_if'         => array(
					'show_pagination' => 'on',
				),
			),
			'previous_label'           => array(
				'label'           => esc_html__( 'Previous Label', 'sermon-manager' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Choose previous label.', 'sermon-manager' ),
				'toggle_slug'     => 'main_content',
				'show_if'         => array(
					'show_prev_next'  => 'on',
					'show_pagination' => 'on',
				),
			),
			'next_label'               => array(
				'label'           => esc_html__( 'Next Label', 'sermon-manager' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Choose next label.', 'sermon-manager' ),
				'toggle_slug'     => 'main_content',
				'show_if'         => array(
					'show_prev_next'  => 'on',
					'show_pagination' => 'on',
				),
			),
			'pagination_alignment'     => array(
				'label'           => esc_html__( 'Pagination Alignment', 'sermon-manager' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Choose pagination alignment.', 'sermon-manager' ),
				'toggle_slug'     => 'main_content',
				'options'         => array(
					'left'   => esc_html__( 'Left', 'sermon-manager' ),
					'center' => esc_html__( 'Center', 'sermon-manager' ),
					'right'  => esc_html__( 'Right', 'sermon-manager' ),
				),
				'show_if'         => array(
					'show_pagination' => 'on',
				),
			),
			// phpcs:disable
			// @todo - this is okay, we just don't want to display it until it's fixed.
			/*'include_taxonomies'   => array(
				'label'           => esc_html__( 'Include Taxonomies', 'sermon-manager' ),
				'renderer'        => 'smp_divi_include_taxonomies',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Select the taxonomies that you would like to include in the render.', 'sermon-manager' ),
				'toggle_slug'     => 'main_content',
			),*/
			// phpcs:enable
		);
	}

	/**
	 * Render sermons.
	 *
	 * @param array  $unprocessed_props The unprocessed props.
	 * @param null   $content           The content.
	 * @param string $render_slug       The render slug.
	 *
	 * @return string The content.
	 */
	public function render( $unprocessed_props, $content = null, $render_slug = '' ) {
		if ( ! defined( 'SMP_VIEW_OVERRIDDEN' ) ) { // Fix for wrong rendering.
			define( 'SMP_VIEW_OVERRIDDEN', true );
		}

		if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
			define( 'SM_ENQUEUE_SCRIPTS_STYLES', true );
		}

		$sermons_number           = $this->props['sermons_number'];
		$sermons_order            = $this->props['sermons_order'];
		$show_image               = $this->props['show_image'];
		$show_series              = $this->props['show_series'];
		$show_title               = $this->props['show_title'];
		$show_date                = $this->props['show_date'];
		$meta_date                = $this->props['meta_date'];
		$show_excerpt             = $this->props['show_excerpt'];
		$excerpt_length           = $this->props['excerpt_length'];
		$show_readmore            = $this->props['show_readmore'];
		$read_more_text           = $this->props['read_more_text'];
		$show_sermon_audio        = $this->props['show_sermon_audio'];
		$show_preacher            = $this->props['show_preacher'];
		$show_passage             = $this->props['show_passage'];
		$show_service_type        = $this->props['show_service_type'];
		$show_grid                = $this->props['show_grid'];
		$show_masonry             = $this->props['show_masonry'];
		$grid_columns             = $this->props['grid_columns'];
		$spacing_columns          = $this->props['spacing_columns'];
		$show_pagination          = $this->props['show_pagination'];
		$pagination_total_num     = $this->props['pagination_total_num'];
		$show_prev_next           = $this->props['show_prev_next'];
		$previous_label           = $this->props['previous_label'];
		$next_label               = $this->props['next_label'];
		$pagination_alignment     = $this->props['pagination_alignment'];
		$title_padding            = $this->props['title_padding'];
		$description_padding      = $this->props['description_padding'];
		$show_filters             = $this->props['show_filters'];
		$show_filter_preacher     = $this->props['show_filter_preacher'];
		$show_filter_series       = $this->props['show_filter_series'];
		$show_filter_book         = $this->props['show_filter_book'];
		$show_filter_service_type = $this->props['show_filter_service_type'];
		$show_filter_topics       = $this->props['show_filter_topics'];
		$featured_type            = $this->props['featured_type'];

		$page_number = (isset($_GET['page_number']))?$_GET['page_number']:1;
		$paged = get_query_var('page_number', $page_number );

		$args = array(
			'post_type'      => 'wpfc_sermon',
			'posts_per_page' => $sermons_number,
			'paged'          => $paged,
			'order'          => $sermons_order,
			'orderby'        => 'meta_value_num',
			'meta_compare'   => '<=',
			'meta_value'     => time(),
			'meta_key'       => 'sermon_date',
		);

		$args = apply_filters('smp/shortcodes/divi/sermon_query', smp_add_taxonomy_to_query( $args ));

		ob_start();

		query_posts( $args );

		if ( empty( $paged ) ) {
			$paged = 1;
		}

		global $wp_query;
		if ( $wp_query->max_num_pages < $pagination_total_num ) {
			$pagination_total_num = $wp_query->max_num_pages;
		}

		$format = 'page_number=%#%';
		if ( ! empty( $_GET ) && is_array( $_GET ) && !$_GET['page_number'] ) {
			$format = '&' . $format;
		} else {
			$format = '?' . $format;
		}

		$pagination_args = array(
			/* 'base'         => get_pagenum_link( 1 ) . '%_%', */
			'format'       => $format,
			'total'        => $pagination_total_num,
			'current'      => $paged,
			'show_all'     => true,
			'end_size'     => 1,
			'prev_text'    => $previous_label,
			'next_text'    => $next_label,
			'type'         => 'plain',
			'add_args'     => false,
			'add_fragment' => '',
		);

		if ( 'on' == $show_prev_next ) {
			$pagination_args['prev_next'] = true;
		} else {
			$pagination_args['prev_next'] = false;
		}

		// Get the additional sermon class.
		$post_classes ='wpfc-sermon grid-item et_pb_sermon_grid_column ';

		if ( ( 'on' == $show_grid ) and ( 'on' == $show_masonry ) ) {
			$post_classes = 'wpfc-sermon grid-item et_pb_sermon_grid_column et_pb_sermon_grid_column_' . $grid_columns;
		} elseif ( 'on' == $show_grid ) {
			$post_classes = 'wpfc-sermon grid-item et_pb_sermon_grid_column et_pb_sermon_grid_column_' . $grid_columns;
		}
		
		if ( 'on' == $show_filters ) {

			$content = render_wpfc_sorting(
				array(
					'hide_topics'        => 'on' === $show_filter_topics ? false : 'yes',
					'hide_series'        => 'on' === $show_filter_series ? false : 'yes',
					'hide_preachers'     => 'on' === $show_filter_preacher ? false : 'yes',
					'hide_books'         => 'on' === $show_filter_book ? false : 'yes',
					'hide_service_types' => 'on' === $show_filter_service_type ? false : 'yes',
					'classes'            => 'no-spacing',
				)
			);

			/**
			 * Allows to filter the filtering HTML.
			 *
			 * @since 2.0.4
			 *
			 * @param string $content The HTML.
			 */
			echo apply_filters( 'smp/shortcodes/divi/sermon_filtering', $content );

		}

		
		if ( ( 'on' == $show_grid ) and ( 'on' == $show_masonry ) ) {
			echo '<div class="sm-inner-grid grid js-masonry" ' . ' data-masonry=\'{ "gutter": 24 }\' >';
		} elseif ( 'on' == $show_grid ) {
			echo '<div class="sm-inner-grid">';
		}
	
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( $post_classes ); ?>
					<?php echo 'on' === $show_grid ? 'style="margin-right:' . $spacing_columns . 'px;width: calc((100% - ' . $spacing_columns * ( $grid_columns - 1 ) . 'px) / ' . $grid_columns . ');"' : ''; ?>>
					<div class="wpfc-sermon-inner">
						<?php if ( 'on' == $show_image ) : ?>
							<?php if ( 'image' == $featured_type ) : ?>
								<div class="wpfc-sermon-image">
									<a href="<?php the_permalink(); ?>">
										<img src="<?php echo get_sermon_image_url( true ); ?>"
												alt="<?php the_title(); ?>">
										<div class="wpfc-sermon-image-img" <?php echo 'style="background-image: url(' . get_sermon_image_url( true ) . ')"'; ?>>
										</div>
									</a>
								</div>
							<?php endif; ?>
							<?php if ( 'video' == $featured_type ) : ?>
								<?php if ( get_wpfc_sermon_meta( 'sermon_video_link' ) ) : ?>
									<div class="wpfc-sermon-video wpfc-sermon-video-link">
										<?php echo wpfc_render_video( get_wpfc_sermon_meta( 'sermon_video_link' ) ); ?>
									</div>
								<?php endif; ?>
								<?php if ( get_wpfc_sermon_meta( 'sermon_video' ) ) : ?>
									<div class="wpfc-sermon-video wpfc-sermon-video-embed">
										<?php echo do_shortcode( get_wpfc_sermon_meta( 'sermon_video' ) ); ?>
									</div>
								<?php endif; ?>
							<?php endif; ?>
						<?php endif; ?>
						<div class="wpfc-sermon-main <?php echo get_sermon_image_url() ? '' : 'no-image'; ?>">
							<div class="wpfc-sermon-header">
								<div class="wpfc-sermon-header-main">
									<?php if ( has_term( '', 'wpfc_sermon_series', get_the_ID() ) and ( 'on' == $show_series ) ) : ?>
										<div class="wpfc-sermon-meta-item wpfc-sermon-meta-series">
											<?php the_terms( get_the_ID(), 'wpfc_sermon_series' ); ?>
										</div>
									<?php endif; ?>
									<?php if ( 'on' == $show_title ) : ?>
										<h3 class="wpfc-sermon-title" <?php echo 'style="padding-bottom:' . $title_padding . 'px;"'; ?>>
											<a class="wpfc-sermon-title-text"
													href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
										</h3>
									<?php endif; ?>

									<?php if ( 'on' == $show_date ) : ?>
										<div class="wpfc-sermon-meta-item wpfc-sermon-meta-date">
											<?php if (\SermonManager::getOption( 'use_published_date' ) ) : ?>
												<?php the_date( $meta_date ); ?>
											<?php else : ?>
												<?php echo \SM_Dates::get( $meta_date ); ?>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</div>
							</div>
							<div class="wpfc-sermon-description">
								<?php if ( 'on' == $show_excerpt ) : ?>
									<div class="sermon-description-content" <?php echo 'style="padding-bottom:' . $description_padding . 'px;"'; ?>>
										<?php if ( has_excerpt() ) : ?>
											<?php echo get_the_excerpt(); ?>
										<?php else : ?>
											<?php echo wp_trim_words( get_wpfc_sermon_meta( 'sermon_description' ), $excerpt_length ); ?>
										<?php endif; ?>
									</div>
									<?php if ( ( str_word_count( get_wpfc_sermon_meta( 'sermon_description' ) ) > $excerpt_length ) && ( 'on' == $show_readmore ) ) : ?>
										<div class="wpfc-sermon-description-read-more">
											<a href="<?php echo get_permalink(); ?>"><?php echo $read_more_text; ?></a>
										</div>
									<?php endif; ?>
								<?php endif; ?>
							</div>
							<?php if ( ( 'on' == $show_sermon_audio ) && ( get_wpfc_sermon_meta( 'sermon_audio' ) || get_wpfc_sermon_meta( 'sermon_audio_id' ) ) ) : ?>
								<?php
								$sermon_audio_id     = get_wpfc_sermon_meta( 'sermon_audio_id' );
								$sermon_audio_url_wp = $sermon_audio_id ? wp_get_attachment_url( intval( $sermon_audio_id ) ) : false;
								$sermon_audio_url    = $sermon_audio_id && $sermon_audio_url_wp ? $sermon_audio_url_wp : get_wpfc_sermon_meta( 'sermon_audio' );
								?>
								<div class="wpfc-sermon-audio">
									<?php echo wpfc_render_audio( $sermon_audio_url ); ?>
								</div>
							<?php endif; ?>
							<?php if ( ( ( 'on' == $show_preacher ) && ( has_term( '', 'wpfc_preacher', get_the_ID() ) ) ) || ( ( 'on' == $show_passage ) && ( get_wpfc_sermon_meta( 'bible_passage' ) ) ) || ( ( 'on' == $show_service_type ) && ( has_term( '', 'wpfc_service_type', get_the_ID() ) ) ) ) : ?>
								<div class="wpfc-sermon-footer">
									<?php if ( ( 'on' == $show_preacher ) && ( has_term( '', 'wpfc_preacher', get_the_ID() ) ) ) : ?>
										<div class="wpfc-sermon-meta-item wpfc-sermon-meta-preacher">
											<?php
											echo apply_filters( 'sermon-images-list-the-terms', '', // phpcs:ignore
												array(
													'taxonomy'     => 'wpfc_preacher', // phpcs:ignore
													'after'        => '', // phpcs:ignore
													'after_image'  => '', // phpcs:ignore
													'before'       => '', // phpcs:ignore
													'before_image' => '', // phpcs:ignore
												)
											);
											?>
											<span class="wpfc-sermon-meta-prefix"><?php echo __( 'Preacher', 'sermon-manager' ); ?>
												:</span>
											<span class="wpfc-sermon-meta-text"><?php the_terms( get_the_ID(), 'wpfc_preacher' ); ?></span>
										</div>
									<?php endif; ?>
									<?php if ( ( 'on' == $show_passage ) && ( get_wpfc_sermon_meta( 'bible_passage' ) ) ) : ?>
										<div class="wpfc-sermon-meta-item wpfc-sermon-meta-passage">
											<span class="wpfc-sermon-meta-prefix"><?php echo __( 'Passage', 'sermon-manager' ); ?>
												:</span>
											<span class="wpfc-sermon-meta-text"><?php wpfc_sermon_meta( 'bible_passage' ); ?></span>
										</div>
									<?php endif; ?>
									<?php if ( ( 'on' == $show_service_type ) && ( has_term( '', 'wpfc_service_type', get_the_ID() ) ) ) : ?>
										<div class="wpfc-sermon-meta-item wpfc-sermon-meta-service">
											<span class="wpfc-sermon-meta-prefix"><?php echo __( 'Service Type', 'sermon-manager' ); ?>
												:</span>
											<span class="wpfc-sermon-meta-text"><?php the_terms( get_the_ID(), 'wpfc_service_type' ); ?></span>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</article>

				<?php
			}

			echo 'on' === $show_grid ? '</div>' : '';

			$paginate_links = paginate_links( $pagination_args );
			if ( ( $paginate_links ) && ( 'on' == $show_pagination ) ) {
				?>
				<nav class="custom-pagination" style="text-align:<?php echo $pagination_alignment; ?>;">
					<p><?php echo $paginate_links; ?></p>
				</nav>
				<?php
			}
		}

		wp_reset_query();

		$posts = ob_get_contents();

		ob_end_clean();

		$output = sprintf(
			'<div class="%1$s">
				%2$s
			</div> <!-- .et_pb_posts -->',
			( 'on' == $show_grid ? 'et_pb_sermon_grid clearfix' : 'et_pb_posts' ),
			$posts
		);

		return $output;

	}

	/**
	 * Returns advanced fields config.
	 *
	 * @return array
	 */
	public function get_advanced_fields_config() {
		return array(
			'fonts'          => array(
				'header' => array(
					'css'         => array(
						'main'      => "{$this->main_css_element} .wpfc-sermon-title",
						'important' => 'all',
					),
					'font_size'   => array(
						'default' => '22',
					),
					'line_height' => array(
						'default' => '1.3',
					),
					'label'       => esc_html__( 'Sermon Title', 'sermon-manager' ),
				),
				'body'   => array(
					'css'         => array(
						'main'      => "{$this->main_css_element} .sermon-description-content",
						'important' => 'all',
					),
					'font_size'   => array(
						'default' => '14',
					),
					'line_height' => array(
						'default' => '1.7',
					),
					'label'       => esc_html__( 'Sermon Description', 'sermon-manager' ),
				),
			),
			'background'     => false,
			'borders'        => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_styles' => "{$this->main_css_element}>.wpfc-sermon-inner",
						),
					),
					'defaults' => array(
						'border_radii'  => 'on|0px|0px|0px|0px',
						'border_styles' => array(
							'style' => 'solid',
							'width' => '1',
							'color' => '#ddd',
						),
					),
				),
			),
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'main' => "{$this->main_css_element}>.wpfc-sermon-inner",
					),
				),
			),
			'filters'        => false,
			'margin_padding' => array(
				'css' => array(
					'main'      => '%%order_class%%',
					'important' => 'all',
				),
			),
		);
	}
}

new Sermon_Blog;
