<?php
/**
 * Sermon taxonomy element for Divi builder.
 *
 * @since   1.0.0-beta.0
 *
 * @package SMP\Shortcodes\Divi
 */

namespace SMP\Shortcodes\Divi;

defined( 'ABSPATH' ) or die;

use ET_Builder_Module;

/**
 * Class Sermon_Taxonomy
 *
 * @package SMP\Shortcodes\Divi
 */
class Sermon_Taxonomy extends ET_Builder_Module {
	/**
	 * The module slug.
	 *
	 * @var string $slug
	 */
	public $slug = 'smp_sermon_taxonomy';

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
	 * Option toggles.
	 *
	 * @var array $options_toggles
	 */
	public $options_toggles = array();

	/**
	 * Advanced options.
	 *
	 * @var array $advanced_options
	 */
	protected $advanced_options = array();

	/**
	 * Function init()
	 */
	function init() {
		$this->name = __( 'Sermons Taxonomy', 'church-sermon-manager' );

		$this->whitelisted_fields = array(
			'show_taxonomy',
			'taxonomy_number',
			'show_pagination',
			'pagination_total_num',
			'show_prev_next',
			'previous_label',
			'next_label',
			'pagination_alignment',
			'show_grid',
			'grid_columns',
			'list_columns',
			'show_grid_image',
			'grid_image_height',
			'grid_image_padding',
			'show_grid_title',
			'show_grid_description',
			'title_padding',
			'description_padding',
			'alphabetical_list',
			'letter_padding_b',
			'letter_padding_t',
		);

		$this->main_css_element = '%%order_class%% .wpfc-term';

		$this->options_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'church-sermon-manager' ),
					'elements'     => esc_html__( 'Elements', 'church-sermon-manager' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => esc_html__( 'Layout', 'church-sermon-manager' ),
					'text'   => array(
						'title'    => esc_html__( 'Text', 'church-sermon-manager' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->fields_defaults = array(
			'show_taxonomy'         => array( 'series', 'add_default_setting' ),
			'taxonomy_number'       => array( 6, 'add_default_setting' ),
			'show_pagination'       => array( 'off' ),
			'pagination_total_num'  => array( 5, 'add_default_setting' ),
			'show_prev_next'        => array( 'off' ),
			'previous_label'        => array( '&laquo; Previous', 'add_default_setting' ),
			'next_label'            => array( 'Next &raquo;', 'add_default_setting' ),
			'pagination_alignment'  => array( 'left', 'add_default_setting' ),
			'show_grid'             => array( 'off' ),
			'grid_columns'          => array( 3, 'add_default_setting' ),
			'list_columns'          => array( 1, 'add_default_setting' ),
			'show_grid_image'       => array( 'on' ),
			'grid_image_height'     => array( 250, 'add_default_setting' ),
			'grid_image_padding'    => array( 20, 'add_default_setting' ),
			'show_grid_title'       => array( 'on' ),
			'show_grid_description' => array( 'off' ),
			'title_padding'         => array( 10, 'add_default_setting' ),
			'description_padding'   => array( 0, 'add_default_setting' ),
			'alphabetical_list'     => array( 'off' ),
			'letter_padding_b'      => array( 5, 'add_default_setting' ),
			'letter_padding_t'      => array( 10, 'add_default_setting' ),
		);
	}

	/**
	 * Returns the module fields.
	 *
	 * @return array The fields.
	 */
	function get_fields() {
		return array(
			'show_taxonomy'         => array(
				'label'           => esc_html__( 'Source', 'church-sermon-manager' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'description'     => esc_html__( 'Choose between the various taxonomies.', 'church-sermon-manager' ),
				'toggle_slug'     => 'main_content',
				'tab_slug'        => 'general',
				'options'         => array(
					'wpfc_sermon_series' => esc_html__( 'Series', 'church-sermon-manager' ),
					'wpfc_preacher'      => esc_html__( 'Preachers', 'church-sermon-manager' ),
					'wpfc_sermon_topics' => esc_html__( 'Topics', 'church-sermon-manager' ),
					'wpfc_bible_book'    => esc_html__( 'Books', 'church-sermon-manager' ),
					'wpfc_service_type'  => esc_html__( 'Service Types', 'church-sermon-manager' ),
				),
			),
			'taxonomy_number'       => array(
				'label'            => esc_html__( 'Terms Per Page', 'church-sermon-manager' ),
				'type'             => 'number',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'Choose how much terms you would like to display per page.', 'church-sermon-manager' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'main_content',
			),
			'show_pagination'       => array(
				'label'           => esc_html__( 'Show Pagination', 'church-sermon-manager' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This will turn pagination on and off.', 'church-sermon-manager' ),
				'toggle_slug'     => 'main_content',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'church-sermon-manager' ),
					'off' => esc_html__( 'No', 'church-sermon-manager' ),
				),
			),
			'pagination_total_num'  => array(
				'label'           => esc_html__( 'Page Limit', 'church-sermon-manager' ),
				'type'            => 'number',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Chose how many pages to display.', 'church-sermon-manager' ),
				'toggle_slug'     => 'main_content',
				'show_if'         => array(
					'show_pagination' => 'on',
				),

			),
			'show_prev_next'        => array(
				'label'           => esc_html__( 'Show Prev/Next Links', 'church-sermon-manager' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'This will turn prev/next links on and off.', 'church-sermon-manager' ),
				'toggle_slug'     => 'main_content',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'church-sermon-manager' ),
					'off' => esc_html__( 'No', 'church-sermon-manager' ),
				),
				'show_if'         => array(
					'show_pagination' => 'on',
				),
			),
			'previous_label'        => array(
				'label'           => esc_html__( 'Previous Label', 'church-sermon-manager' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Chose previous label.', 'church-sermon-manager' ),
				'toggle_slug'     => 'main_content',
				'show_if'         => array(
					'show_prev_next'  => 'on',
					'show_pagination' => 'on',
				),
			),
			'next_label'            => array(
				'label'           => esc_html__( 'Next Label', 'church-sermon-manager' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Chose next label.', 'church-sermon-manager' ),
				'toggle_slug'     => 'main_content',
				'show_if'         => array(
					'show_prev_next'  => 'on',
					'show_pagination' => 'on',
				),
			),
			'pagination_alignment'  => array(
				'label'           => esc_html__( 'Pagination Alignment', 'church-sermon-manager' ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Choose pagination alignment.', 'church-sermon-manager' ),
				'toggle_slug'     => 'main_content',
				'options'         => array(
					'left'   => esc_html__( 'Left', 'church-sermon-manager' ),
					'center' => esc_html__( 'Center', 'church-sermon-manager' ),
					'right'  => esc_html__( 'Right', 'church-sermon-manager' ),
				),
				'show_if'         => array(
					'show_pagination' => 'on',
				),
			),
			'show_grid'             => array(
				'label'           => esc_html__( 'Layout', 'church-sermon-manager' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'description'     => esc_html__( 'Toggle between the various sermons layout types.', 'church-sermon-manager' ),
				'toggle_slug'     => 'layout',
				'tab_slug'        => 'advanced',
				'options'         => array(
					'on'  => esc_html__( 'Grid', 'church-sermon-manager' ),
					'off' => esc_html__( 'List', 'church-sermon-manager' ),
				),
			),
			'list_columns'          => array(
				'label'           => esc_html__( 'Number of Colums', 'church-sermon-manager' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'description'     => esc_html__( 'Choose how many columns to display.', 'church-sermon-manager' ),
				'toggle_slug'     => 'layout',
				'tab_slug'        => 'advanced',
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '6',
					'step' => '1',
				),
				'show_if'         => array(
					'show_grid' => 'off',
				),

			),
			'alphabetical_list'     => array(
				'label'           => esc_html__( 'Alphabetical List', 'church-sermon-manager' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'toggle_slug'     => 'layout',
				'tab_slug'        => 'advanced',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'church-sermon-manager' ),
					'off' => esc_html__( 'No', 'church-sermon-manager' ),
				),
				'show_if'         => array(
					'show_grid' => 'off',
				),
			),
			'letter_padding_b'      => array(
				'label'           => esc_html__( 'Letter Bottom Padding', 'church-sermon-manager' ),
				'type'            => 'number',
				'option_category' => 'layout',
				'toggle_slug'     => 'letter',
				'tab_slug'        => 'advanced',
			),
			'letter_padding_t'      => array(
				'label'           => esc_html__( 'Letter Top Padding', 'church-sermon-manager' ),
				'type'            => 'number',
				'option_category' => 'layout',
				'toggle_slug'     => 'letter',
				'tab_slug'        => 'advanced',
			),
			'grid_columns'          => array(
				'label'           => esc_html__( 'Number of Colums', 'church-sermon-manager' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'description'     => esc_html__( 'Choose how many columns to display.', 'church-sermon-manager' ),
				'toggle_slug'     => 'layout',
				'tab_slug'        => 'advanced',
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '6',
					'step' => '1',
				),
				'show_if'         => array(
					'show_grid' => 'on',
				),

			),
			'show_grid_image'       => array(
				'label'           => esc_html__( 'Show Image', 'church-sermon-manager' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'toggle_slug'     => 'layout',
				'tab_slug'        => 'advanced',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'church-sermon-manager' ),
					'off' => esc_html__( 'No', 'church-sermon-manager' ),
				),
				'show_if'         => array(
					'show_grid' => 'on',
				),
			),
			'grid_image_height'     => array(
				'label'           => esc_html__( 'Image height', 'church-sermon-manager' ),
				'type'            => 'number',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'layout',
				'tab_slug'        => 'advanced',
				'show_if'         => array(
					'show_grid'       => 'on',
					'show_grid_image' => 'on',
				),
			),
			'grid_image_padding'    => array(
				'label'           => esc_html__( 'Image Bottom Padding', 'church-sermon-manager' ),
				'type'            => 'range',
				'option_category' => 'text',
				'toggle_slug'     => 'layout',
				'tab_slug'        => 'advanced',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'show_if'         => array(
					'show_grid'       => 'on',
					'show_grid_image' => 'on',
				),
			),
			'show_grid_title'       => array(
				'label'           => esc_html__( 'Show Title', 'church-sermon-manager' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'toggle_slug'     => 'layout',
				'tab_slug'        => 'advanced',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'church-sermon-manager' ),
					'off' => esc_html__( 'No', 'church-sermon-manager' ),
				),
				'show_if'         => array(
					'show_grid' => 'on',
				),
			),
			'show_grid_description' => array(
				'label'           => esc_html__( 'Show Description', 'church-sermon-manager' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'toggle_slug'     => 'layout',
				'tab_slug'        => 'advanced',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'church-sermon-manager' ),
					'off' => esc_html__( 'No', 'church-sermon-manager' ),
				),
				'show_if'         => array(
					'show_grid' => 'on',
				),
			),
			'title_padding'         => array(
				'label'           => esc_html__( 'Term Title Bottom Padding', 'church-sermon-manager' ),
				'type'            => 'range',
				'option_category' => 'text',
				'toggle_slug'     => 'header',
				'tab_slug'        => 'advanced',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
			),
			'description_padding'   => array(
				'label'           => esc_html__( 'Term Info Padding', 'church-sermon-manager' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'toggle_slug'     => 'layout',
				'tab_slug'        => 'advanced',
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'show_if'         => array(
					'show_grid' => 'on',
				),
			),
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
	public function render( $unprocessed_props, $content = '', $render_slug = '' ) {
		if ( ! defined( 'SMP_VIEW_OVERRIDDEN' ) ) { // Fix for wrong rendering.
			define( 'SMP_VIEW_OVERRIDDEN', true );
		}

		if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
			define( 'SM_ENQUEUE_SCRIPTS_STYLES', true );
		}

		$show_taxonomy         = $this->props['show_taxonomy'];
		$taxonomy_number       = $this->props['taxonomy_number'];
		$show_pagination       = $this->props['show_pagination'];
		$pagination_total_num  = $this->props['pagination_total_num'];
		$show_prev_next        = $this->props['show_prev_next'];
		$previous_label        = $this->props['previous_label'];
		$next_label            = $this->props['next_label'];
		$pagination_alignment  = $this->props['pagination_alignment'];
		$show_grid             = $this->props['show_grid'];
		$list_columns          = $this->props['list_columns'];
		$grid_columns          = $this->props['grid_columns'];
		$show_grid_image       = $this->props['show_grid_image'];
		$grid_image_height     = $this->props['grid_image_height'];
		$grid_image_padding    = $this->props['grid_image_padding'];
		$show_grid_title       = $this->props['show_grid_title'];
		$show_grid_description = $this->props['show_grid_description'];
		$title_padding         = $this->props['title_padding'];
		$description_padding   = $this->props['description_padding'];
		$alphabetical_list     = $this->props['alphabetical_list'];
		$letter_padding_b      = $this->props['letter_padding_b'];
		$letter_padding_t      = $this->props['letter_padding_t'];

		ob_start();

		$url          = $_SERVER['REQUEST_URI'];
		$segments     = explode( '/', $url );
		$page         = is_numeric( $segments[ count( $segments ) - 2 ] ) ? $segments[ count( $segments ) - 2 ] : 1;
		$next         = $page + 1;
		$prev         = $page - 1;
		$terms_number = min( $taxonomy_number, wp_count_terms( $show_taxonomy ) );
		$lastpage     = min( ceil( wp_count_terms( $show_taxonomy ) / $terms_number ), $pagination_total_num );

		$terms = get_terms( array(
			'taxonomy'   => $show_taxonomy,
			'hide_empty' => false,
			'offset'     => ( $page - 1 ) * $terms_number,
			'number'     => $terms_number,
		) );

		$term_classes = 'on' === $show_grid ? 'et_pb_sermon_term_grid_column et_pb_sermon_term_grid_column_' . $grid_columns : 'et_pb_sermon_term_list_column et_pb_sermon_term_list_column_' . $list_columns;

		$column_terms = round( intval( $terms_number ) / intval( $list_columns ) );

		$full_column = $terms_number % $list_columns;

		if ( 0 != $full_column ) {
			$column_terms ++;
		}

		$first_letter = '';
		$col          = 1;
		$i            = 1;

		?>

		<div class="wpfc-term-content">
			<?php echo 'on' === $show_grid ? '<div class="wpfc-term-grid">' : '<div class="wpfc-term-list">'; ?>
			<?php foreach ( $terms as $term ) : ?>
				<?php
				if ( 'off' == $show_grid && 1 == $i ) {
					echo '<div class="wpfc-term ' . esc_attr( $term_classes ) . '" style="width: calc((100% - ' . esc_attr( 30 * ( $list_columns - 1 ) ) . 'px) / ' . esc_attr( $list_columns ) . ');">';
				}

				if ( 'on' == $alphabetical_list && 'off' == $show_grid ) {
					if ( $first_letter != $term->name[0] || 1 == $i ) {
						$first_letter = $term->name[0];
						echo '<div class="wpfc-term-first-letter" style="padding-bottom:' . esc_attr( $letter_padding_b ) . 'px;padding-top:' . esc_attr( $letter_padding_t ) . 'px;">' . esc_html( $first_letter ) . '</div>';
					}
				}
				?>

				<?php echo 'on' === $show_grid ? '<div class="wpfc-term ' . esc_attr( $term_classes ) . '" style="width: calc((100% - ' . esc_attr( 30 * ( $grid_columns - 1 ) ) . 'px) / ' . esc_attr( $grid_columns ) . ');">' : '<div class="wpfc-term-inner" >'; ?>

				<?php
				if ( ( 'on' == $show_grid ) && ( 'on' == $show_grid_image ) ) {
					$associations = get_option( 'sermon_image_plugin' );

					if ( ! empty( $associations[ $term->term_id ] ) ) {
						$image_id = (int) $associations[ $term->term_id ];
					} else {
						$image_id = null;
					}

					$css_height = $grid_image_height . 'px';
					$css_margin = $grid_image_padding . 'px';

					if ( ! is_wp_error( get_term_link( $term, $show_taxonomy ) ) ) {
						if ( $image_id ) {
							/* @noinspection CssUnknownTarget */
							echo sprintf(
								'<a href="' . esc_url( get_term_link( $term, $show_taxonomy ) ) . '" class="wpfc-term-grid-image" style="background-image:url(%s);height:' . esc_attr( $css_height ) . ';margin-bottom:' . esc_attr( $css_margin ) . ';"></a>',
								esc_url( wp_get_attachment_image_url( $image_id, array( 300, 300 ) ) )
							);
						} else {
							echo sprintf( '<a href="' . esc_url( get_term_link( $term, $show_taxonomy ) ) . '" class="wpfc-term-grid-image" style="background-color:#cecece;height:' . esc_attr( $css_height ) . ';margin-bottom:' . esc_attr( $css_margin ) . ';"></a>' );
						}
					}
				}
				?>

				<div class="wpfc-term-inner"
					<?php
					if ( ( 'on' == $show_grid ) && ( ( 'on' == $show_grid_title ) or ( 'on' == $show_grid_description ) ) ) {
						echo 'style="padding:' . esc_attr( $description_padding ) . 'px;"';
					}
					?>
				>

					<?php
					if ( ( ( 'on' == $show_grid ) && ( 'on' == $show_grid_title ) ) or ( ( 'off' == $show_grid ) ) ) {
						?>
						<a href="<?php echo esc_url( get_term_link( $term, $show_taxonomy ) ); ?>"
								class="wpfc-term-title" <?php echo 'style="padding-bottom:' . esc_attr( $title_padding ) . 'px;"'; ?>><?php echo esc_html( $term->name ); ?></a>

					<?php } ?>

					<?php
					if ( ( 'on' == $show_grid ) && ( 'on' == $show_grid_description ) ) {
						?>
						<div class="wpfc-term-description"><?php echo esc_html( wp_trim_words( $term->description, 25, '...' ) ); ?></div>
					<?php } ?>

				</div>

				<?php echo 'on' === $show_grid ? '</div>' : '</div>'; ?>

				<?php
				if ( 'off' == $show_grid ) {
					if ( $i == $column_terms ) {
						echo '</div>';
						$i = 1;
						if ( $col == $full_column ) {
							$column_terms --;
						}
						$col ++;
					} else {
						$i ++;
					}
				}
				?>

			<?php endforeach; ?>
			<?php echo 'on' === $show_grid ? '</div>' : '</div>'; ?>
		</div>

		<?php
		if ( ( 'on' == $show_pagination ) && ( 1 != $lastpage ) ) { ?>
			<div class="wpfc-term-pagination" style="text-align:<?php echo esc_attr( $pagination_alignment ); ?>;">
				<?php
				if ( ( $prev > 0 ) && ( $show_prev_next ) ) {
					?>
					<a href="?page=<?php echo esc_attr( $prev ); ?>"><?php echo esc_html( $previous_label ); ?></a>
					<?php
				}

				for ( $i = 1; $i <= $lastpage; $i ++ ) {

					if ( $page == $i ) {
						?>
						<span><?php echo esc_html( $i ); ?></span>
						<?php
					} else {
						?>
						<a href="?page=<?php echo esc_attr( $i ); ?>" class="page-numbers"><?php echo esc_html( $i ); ?></a>
						<?php
					}
				}

				if ( ( $page < $lastpage ) && ( $show_prev_next ) ) {
					?>
					<a href="?page=<?php echo esc_attr( $next ); ?>"><?php echo esc_html( $next_label ); ?></a>
					<?php
				} ?>
			</div>
			<?php
		}

		$posts = ob_get_contents();

		ob_end_clean();

		$output = sprintf(
			'<div>
				%1$s
			</div> <!-- .et_pb_posts -->', $posts
		);

		return $output;
	}

	/**
	 * Gets advanced fields config.
	 *
	 * @return array
	 */
	public function get_advanced_fields_config() {
		return array(
			'fonts'          => array(
				'header' => array(
					'css'         => array(
						'main'      => "{$this->main_css_element} .wpfc-term-title",
						'important' => 'all',
					),
					'font_size'   => array(
						'default' => '14',
					),
					'text_align'  => array(
						'default' => 'left',
					),
					'line_height' => array(
						'default' => '1.7',
					),
					'label'       => esc_html__( 'Term Title', 'church-sermon-manager' ),
				),
				'body'   => array(
					'css'         => array(
						'main'      => "{$this->main_css_element} .wpfc-term-description",
						'important' => 'all',
					),
					'font_size'   => array(
						'default' => '14',
					),
					'text_align'  => array(
						'default' => 'left',
					),
					'line_height' => array(
						'default' => '1.7',
					),
					'label'       => esc_html__( 'Term Description', 'church-sermon-manager' ),
				),
				'letter' => array(
					'css'         => array(
						'main'      => "{$this->main_css_element} .wpfc-term-first-letter",
						'important' => 'all',
					),
					'font_size'   => array(
						'default' => '14',
					),
					'text_align'  => array(
						'default' => 'left',
					),
					'line_height' => array(
						'default' => '1.7',
					),
					'label'       => esc_html__( 'Alphabetical Letter', 'church-sermon-manager' ),
				),
			),
			'background'     => false,
			'borders'        => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_styles' => "{$this->main_css_element}",
						),
					),
					'defaults' => array(
						'border_radii'  => 'on|0px|0px|0px|0px',
						'border_styles' => array(
							'style' => 'none',
							'width' => '0',
							'color' => '#ddd',
						),
					),
				),
			),
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'main' => "{$this->main_css_element}",
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

new Sermon_Taxonomy;
