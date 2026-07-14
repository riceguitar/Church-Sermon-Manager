<?php
/**
 * Adds new shortcode "sermon_taxonomy" and registers it to
 * the Visual Composer plugin
 *
 */

if ( ! class_exists( 'Sermon_Taxonomy_Shortcode' ) ) {

	class Sermon_Taxonomy_Shortcode {

		/**
		 * Main constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Registers the shortcode in WordPress
			add_shortcode( 'sermon_taxonomy', array( 'Sermon_Taxonomy_Shortcode', 'output' ) );

			// Map shortcode to Visual Composer
			if ( function_exists( 'vc_lean_map' ) ) {
				vc_lean_map( 'sermon_taxonomy', array( 'Sermon_Taxonomy_Shortcode', 'map' ) );
			}

		}

		/**
		 * Shortcode output
		 *
		 * @since 1.0.0
		 */
		public static function output( $atts, $content = null ) {

			if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
				define( 'SM_ENQUEUE_SCRIPTS_STYLES', true );
			}

			// Extract shortcode attributes (based on the vc_lean_map function - see next function)
			extract( vc_map_get_attributes( 'sermon_taxonomy', $atts ) );

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

		$term_classes = 'on' === $show_grid ? 'sermon_term_grid_column sermon_term_grid_column_' . $grid_columns : 'sermon_term_list_column sermon_term_list_column_' . $list_columns;

		$column_terms = round( intval( $terms_number ) / intval( $list_columns ) );

		$full_column = $terms_number % $list_columns;

		if ( 0 != $full_column ) {
			$column_terms ++;
		}

		$first_letter = '';
		$col          = 1;
		$i            = 1;

		extract(shortcode_atts(array(
				'css' => ''
			), $atts));
			$sermons_css = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), 'sermon_blog', $atts );

		?>

		<div class="wpfc-term-content">
			<?php echo 'on' === $show_grid ? '<div class="wpfc-term-grid">' : '<div class="wpfc-term-list">'; ?>
			<?php foreach ( $terms as $term ) : ?>
				<?php
				if ( 'off' == $show_grid && 1 == $i ) {
					echo '<div class="wpfc-term ' . $term_classes . '" style="width: calc((100% - ' . 30 * ( $list_columns - 1 ) . 'px) / ' . $list_columns . ');">';
				}

				if ( $alphabetical_list && 'off' == $show_grid ) {
					if ( $first_letter != $term->name[0] || 1 == $i ) {
						$first_letter = $term->name[0];
						echo '<div class="wpfc-term-first-letter" style="padding-bottom:' . $letter_padding_b . 'px;padding-top:' . $letter_padding_t . 'px;font-size:' . $letter_font_size . 'px;color:' . $letter_color . ';">' . $first_letter . '</div>';
					}
				}
				?>

				<?php echo 'on' === $show_grid ? '<div class="wpfc-term ' . $term_classes . '" style="width: calc((100% - ' . 30 * ( $grid_columns - 1 ) . 'px) / ' . $grid_columns . ');">' : '<div class="wpfc-term-inner" >'; ?>

				<?php
				if ( ( 'on' == $show_grid ) && ( $show_grid_image ) ) {
					$associations = get_option( 'sermon_image_plugin' );

					if ( ! empty( $associations[ $term->term_id ] ) ) {
						$image_id = (int) $associations[ $term->term_id ];
					} else {
						$image_id = null;
					}

					$css_height = $grid_image_height . 'px';
					$css_margin = $grid_image_padding . 'px';

					if ( $image_id ) {
						/* @noinspection CssUnknownTarget */
						echo sprintf(
							'<a href="' . get_term_link( $term, $show_taxonomy ) . '" class="wpfc-term-grid-image" style="background-image:url(%s);height:' . $css_height . ';margin-bottom:' . $css_margin . ';"></a>',
							wp_get_attachment_image_url( $image_id, array( 300, 300 ) )
						);
					} else {
						echo sprintf( '<a href="' . get_term_link( $term, $show_taxonomy ) . '" class="wpfc-term-grid-image" style="background-color:#cecece;height:' . $css_height . ';margin-bottom:' . $css_margin . ';"></a>' );
					}
				}
				?>

				<div class="wpfc-term-inner"
					<?php
					if ( ( 'on' == $show_grid ) && ( $show_grid_title or $show_grid_description  ) ) {
						echo 'style="padding:' . $description_padding . 'px;"';
					}
					?>
				>

					<?php
					if ( ( ( 'on' == $show_grid ) && ( $show_grid_title ) ) or ( ( 'off' == $show_grid ) ) ) {
						?>
						<a href="<?php echo get_term_link( $term, $show_taxonomy ); ?>"
								class="wpfc-term-title"
								<?php echo 'style="padding-bottom:' . $title_padding . 'px;color:' . $title_color . ';text-align:' . $title_alignment . ';font-size:' . $title_font_size . 'px;"'; ?>
								><?php echo $term->name; ?></a>

					<?php } ?>

					<?php
					if ( ( 'on' == $show_grid ) && ( $show_grid_description ) ) {
						?>
						<div class="wpfc-term-description"
						<?php echo 'style="color:' . $description_color . ';text-align:' . $description_alignment . ';font-size:' . $description_font_size . 'px;"'; ?>
						><?php echo wp_trim_words( $term->description, 25, '...' ); ?></div>
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
		if ( ( $show_pagination ) && ( 1 != $lastpage ) ) { ?>
			<div class="wpfc-term-pagination" style="text-align:<?php echo $pagination_alignment; ?>;">
				<?php
					if ( ( $prev > 0 ) && ( $show_prev_next ) ) {
						?>
						<a href="?page=<?php echo $prev; ?>"><?php echo $previous_label; ?></a>
						<?php
					}

					for ( $i = 1; $i <= $lastpage; $i ++ ) {

						if ( $page == $i ) {
							?>
							<span><?php echo $i; ?></span>
							<?php
						} else {
							?>
							<a href="?page=<?php echo $i; ?>" class="page-numbers"><?php echo $i; ?></a>
							<?php
						}
					}

					if ( ( $page < $lastpage ) && ( $show_prev_next ) ) {
						?>
						<a href="?page=<?php echo $next; ?>"><?php echo $next_label; ?></a>
						<?php
					} ?>
			</div>
			<?php
		}

		$posts = ob_get_contents();

		ob_end_clean();

		$output = sprintf(
			'<div class="smp_sermon_taxonomy wpb_content_element %1$s%2$s" id="%3$s">
					%4$s
				</div>',
			$el_class,$sermons_css,$el_id,$posts
		);

		return $output;

		}

		/**
		 * Map shortcode to VC
		 *
		 * This is an array of all your settings which become the shortcode attributes ($atts)
		 * for the output.
		 *
		 */
		public static function map() {
			return array(
				'name'        => esc_html__( 'Sermons Taxonomy', 'locale' ),
				'description' => esc_html__( 'Display Sermons Terms', 'locale' ),
				'base'        => 'sermon_taxonomy',
				'icon' 		  => 'icon-wpb-ui-accordion',
				'params'      => array(
					array(
						'type' => 'textfield',
						'heading' => __( 'Widget title', 'locale' ),
						'param_name' => 'title',
						'description' => __( 'What text use as a widget title. Leave blank to use default widget title.', 'locale' ),
						'value' => __( 'Sermons Taxonomy' ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Source', 'locale' ),
						'param_name' => 'show_taxonomy',
						'value'      => array(
							esc_html__( 'Series', 'locale' ) 		=> 'wpfc_sermon_series',
							esc_html__( 'Preachers', 'locale' )		=> 'wpfc_preacher',
							esc_html__( 'Topics', 'locale' )		=> 'wpfc_sermon_topics',
							esc_html__( 'Books', 'locale' )			=> 'wpfc_bible_book',
							esc_html__( 'Service Types', 'locale' )	=> 'wpfc_service_type',
						),
						'description' => __( 'Choose between the various taxonomies.', 'locale' ),
						'admin_label' => true,
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Terms Per Page', 'locale' ),
						'description' => __( 'Choose how much terms you would like to display per page.', 'locale' ),
						'param_name' => 'taxonomy_number',
						'value' => 12,
						'admin_label' => true,
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Layout', 'locale' ),
						'param_name' => 'show_grid',
						'value'      => array(
							esc_html__( 'Grid', 'locale' ) => 'on',
							esc_html__( 'List', 'locale' ) => 'off',
						),
						'description' => __( 'Toggle between the various sermons layout types.', 'locale' ),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'List Columns', 'locale' ),
						'param_name' => 'list_columns',
						'value'      => array(
							esc_html__( '1', 'locale' ) => '1',
							esc_html__( '2', 'locale' ) => '2',
							esc_html__( '3', 'locale' ) => '3',
							esc_html__( '4', 'locale' ) => '4',
							esc_html__( '5', 'locale' ) => '5',
							esc_html__( '6', 'locale' ) => '6',
						),
						'description' => __( 'Choose how many columns to display.', 'locale' ),
						'std'=>'1',
						'dependency' => array(
							'element' => 'show_grid',
							'value' => 'off',
						),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Alphabetical List', 'locale' ),
						'param_name' => 'alphabetical_list',
						'value' => array( __( 'Yes', 'locale' ) => true ),
						'std'=>'value',
						'dependency' => array(
							'element' => 'show_grid',
							'value' => 'off',
						),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Letter Color', 'locale' ),
						'param_name' => 'letter_color',
						'value' => '#000000',
						'dependency' => array(
							'element' => 'alphabetical_list',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Letter Font Size', 'locale' ),
						'param_name' => 'letter_font_size',
						'value'      => array(
							esc_html__( '12px', 'locale' ) => '12',
							esc_html__( '13px', 'locale' ) => '13',
							esc_html__( '14px', 'locale' ) => '14',
							esc_html__( '15px', 'locale' ) => '15',
							esc_html__( '16px', 'locale' ) => '16',
							esc_html__( '17px', 'locale' ) => '17',
							esc_html__( '18px', 'locale' ) => '18',
							esc_html__( '19px', 'locale' ) => '19',
							esc_html__( '20px', 'locale' ) => '20',
							esc_html__( '21px', 'locale' ) => '21',
							esc_html__( '22px', 'locale' ) => '22',
						),
						'std'=>'22',
						'dependency' => array(
							'element' => 'alphabetical_list',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Letter Bottom Padding', 'locale' ),
						'param_name' => 'letter_padding_b',
						'value'      => array(
							esc_html__( 'None', 'locale' ) => '0',
							esc_html__( '1px', 'locale' ) => '1',
							esc_html__( '2px', 'locale' ) => '2',
							esc_html__( '3px', 'locale' ) => '3',
							esc_html__( '4px', 'locale' ) => '4',
							esc_html__( '5px', 'locale' ) => '5',
							esc_html__( '10px', 'locale' ) => '10',
							esc_html__( '15px', 'locale' ) => '15',
							esc_html__( '20px', 'locale' ) => '20',
							esc_html__( '25px', 'locale' ) => '25',
							esc_html__( '30px', 'locale' ) => '30',
							esc_html__( '35px', 'locale' ) => '35',
						),
						'std'=>'5',
						'dependency' => array(
							'element' => 'alphabetical_list',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Letter Bottom Padding', 'locale' ),
						'param_name' => 'letter_padding_t',
						'value'      => array(
							esc_html__( 'None', 'locale' ) => '0',
							esc_html__( '1px', 'locale' ) => '1',
							esc_html__( '2px', 'locale' ) => '2',
							esc_html__( '3px', 'locale' ) => '3',
							esc_html__( '4px', 'locale' ) => '4',
							esc_html__( '5px', 'locale' ) => '5',
							esc_html__( '10px', 'locale' ) => '10',
							esc_html__( '15px', 'locale' ) => '15',
							esc_html__( '20px', 'locale' ) => '20',
							esc_html__( '25px', 'locale' ) => '25',
							esc_html__( '30px', 'locale' ) => '30',
							esc_html__( '35px', 'locale' ) => '35',
						),
						'std'=>'10',
						'dependency' => array(
							'element' => 'alphabetical_list',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Number of Colums', 'locale' ),
						'param_name' => 'grid_columns',
						'value'      => array(
							esc_html__( '1', 'locale' ) => '1',
							esc_html__( '2', 'locale' ) => '2',
							esc_html__( '3', 'locale' ) => '3',
							esc_html__( '4', 'locale' ) => '4',
							esc_html__( '5', 'locale' ) => '5',
							esc_html__( '6', 'locale' ) => '6',
						),
						'description' => __( 'Choose how many columns to display.', 'locale' ),
						'std'=>'3',
						'dependency' => array(
							'element' => 'show_grid',
							'value' => 'on',
						),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Show Image', 'locale' ),
						'param_name' => 'show_grid_image',
						'value' => array( __( 'Yes', 'locale' ) => true ),
						'std'=>'value',
						'dependency' => array(
							'element' => 'show_grid',
							'value' => 'on',
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Image height', 'locale' ),
						'param_name' => 'grid_image_height',
						'value'      => array(
							esc_html__( '50px', 'locale' ) => '50',
							esc_html__( '100px', 'locale' ) => '100',
							esc_html__( '150px', 'locale' ) => '150',
							esc_html__( '200px', 'locale' ) => '200',
							esc_html__( '250px', 'locale' ) => '250',
							esc_html__( '300px', 'locale' ) => '300',
							esc_html__( '350px', 'locale' ) => '350',
							esc_html__( '400px', 'locale' ) => '400',
							esc_html__( '450px', 'locale' ) => '450',
							esc_html__( '500px', 'locale' ) => '500',
						),
						'std'=>'250',
						'dependency' => array(
							'element' => 'show_grid_image',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Image Bottom Padding', 'locale' ),
						'param_name' => 'grid_image_padding',
						'value'      => array(
							esc_html__( 'None', 'locale' ) => '0',
							esc_html__( '1px', 'locale' ) => '1',
							esc_html__( '2px', 'locale' ) => '2',
							esc_html__( '3px', 'locale' ) => '3',
							esc_html__( '4px', 'locale' ) => '4',
							esc_html__( '5px', 'locale' ) => '5',
							esc_html__( '10px', 'locale' ) => '10',
							esc_html__( '15px', 'locale' ) => '15',
							esc_html__( '20px', 'locale' ) => '20',
							esc_html__( '25px', 'locale' ) => '25',
							esc_html__( '30px', 'locale' ) => '30',
							esc_html__( '35px', 'locale' ) => '35',
						),
						'std'=>'20',
						'dependency' => array(
							'element' => 'show_grid_image',
							'not_empty' => true,
						),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Show Title', 'locale' ),
						'param_name' => 'show_grid_title',
						'value' => array( __( 'Yes', 'locale' ) => true ),
						'std'=>'value',
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Title Color', 'locale' ),
						'param_name' => 'title_color',
						'value' => '#000000',
						'dependency' => array(
							'element' => 'show_grid_title',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Term Title Font Size', 'locale' ),
						'param_name' => 'title_font_size',
						'value'      => array(
							esc_html__( '12px', 'locale' ) => '12',
							esc_html__( '13px', 'locale' ) => '13',
							esc_html__( '14px', 'locale' ) => '14',
							esc_html__( '15px', 'locale' ) => '15',
							esc_html__( '16px', 'locale' ) => '16',
							esc_html__( '17px', 'locale' ) => '17',
							esc_html__( '18px', 'locale' ) => '18',
							esc_html__( '19px', 'locale' ) => '19',
							esc_html__( '20px', 'locale' ) => '20',
							esc_html__( '21px', 'locale' ) => '21',
							esc_html__( '22px', 'locale' ) => '22',
						),
						'std'=>'18',
						'dependency' => array(
							'element' => 'show_grid_title',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Term Title Alignment', 'locale' ),
						'param_name' => 'title_alignment',
						'value'      => array(
							esc_html__( 'Left', 'locale' ) => 'left',
							esc_html__( 'Center', 'locale' ) => 'center',
							esc_html__( 'Right', 'locale' ) => 'right',
						),
						'std'=>'center',
						'dependency' => array(
							'element' => 'show_grid',
							'value' => 'on',
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Term Title Bottom Padding', 'locale' ),
						'param_name' => 'title_padding',
						'value'      => array(
							esc_html__( 'None', 'locale' ) => '0',
							esc_html__( '1px', 'locale' ) => '1',
							esc_html__( '2px', 'locale' ) => '2',
							esc_html__( '3px', 'locale' ) => '3',
							esc_html__( '4px', 'locale' ) => '4',
							esc_html__( '5px', 'locale' ) => '5',
							esc_html__( '10px', 'locale' ) => '10',
							esc_html__( '15px', 'locale' ) => '15',
							esc_html__( '20px', 'locale' ) => '20',
							esc_html__( '25px', 'locale' ) => '25',
							esc_html__( '30px', 'locale' ) => '30',
							esc_html__( '35px', 'locale' ) => '35',
						),
						'std'=>'10',
						'dependency' => array(
							'element' => 'show_grid_title',
							'not_empty' => true,
						),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Show Description', 'locale' ),
						'param_name' => 'show_grid_description',
						'value' => array( __( 'Yes', 'locale' ) => true ),
						'std'=>'value',
						'dependency' => array(
							'element' => 'show_grid',
							'value' => 'on',
						),
					),
					array(
						'type' => 'colorpicker',
						'heading' => __( 'Description Color', 'locale' ),
						'param_name' => 'description_color',
						'value' => '#000000',
						'dependency' => array(
							'element' => 'show_grid_description',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Description Font Size', 'locale' ),
						'param_name' => 'description_font_size',
						'value'      => array(
							esc_html__( '12px', 'locale' ) => '12',
							esc_html__( '13px', 'locale' ) => '13',
							esc_html__( '14px', 'locale' ) => '14',
							esc_html__( '15px', 'locale' ) => '15',
							esc_html__( '16px', 'locale' ) => '16',
							esc_html__( '17px', 'locale' ) => '17',
							esc_html__( '18px', 'locale' ) => '18',
							esc_html__( '19px', 'locale' ) => '19',
							esc_html__( '20px', 'locale' ) => '20',
							esc_html__( '21px', 'locale' ) => '21',
							esc_html__( '22px', 'locale' ) => '22',
						),
						'std'=>'14',
						'dependency' => array(
							'element' => 'show_grid_description',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Description Alignment', 'locale' ),
						'param_name' => 'description_alignment',
						'value'      => array(
							esc_html__( 'Left', 'locale' ) => 'left',
							esc_html__( 'Center', 'locale' ) => 'center',
							esc_html__( 'Right', 'locale' ) => 'right',
						),
						'std'=>'left',
						'dependency' => array(
							'element' => 'show_grid_description',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Term Info Padding', 'locale' ),
						'param_name' => 'description_padding',
						'value'      => array(
							esc_html__( 'None', 'locale' ) => '0',
							esc_html__( '1px', 'locale' ) => '1',
							esc_html__( '2px', 'locale' ) => '2',
							esc_html__( '3px', 'locale' ) => '3',
							esc_html__( '4px', 'locale' ) => '4',
							esc_html__( '5px', 'locale' ) => '5',
							esc_html__( '10px', 'locale' ) => '10',
							esc_html__( '15px', 'locale' ) => '15',
							esc_html__( '20px', 'locale' ) => '20',
							esc_html__( '25px', 'locale' ) => '25',
							esc_html__( '30px', 'locale' ) => '30',
							esc_html__( '35px', 'locale' ) => '35',
						),
						'std'=>'0',
						'dependency' => array(
							'element' => 'show_grid_description',
							'not_empty' => true,
						),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Show Pagination', 'locale' ),
						'param_name' => 'show_pagination',
						'value' => array( __( 'Yes', 'locale' ) => true ),
						'std'=>'value',
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Page Limit', 'locale' ),
						'param_name' => 'pagination_total_num',
						'value' => 5,
						'dependency' => array(
							'element' => 'show_pagination',
							'not_empty' => true,
						),
					),
					array(
						'type' => 'checkbox',
						'heading' => __( 'Show Prev/Next Links', 'locale' ),
						'param_name' => 'show_prev_next',
						'value' => array( __( 'Yes', 'locale' ) => true ),
						'std'=>'value',
						'dependency' => array(
							'element' => 'show_pagination',
							'not_empty' => true,
						),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Previous Label', 'locale' ),
						'param_name' => 'previous_label',
						'value' => '&laquo; Previous',
						'dependency' => array(
							'element' => 'show_prev_next',
							'not_empty' => true,
						),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Next Label', 'locale' ),
						'param_name' => 'next_label',
						'value' => 'Next &raquo;',
						'dependency' => array(
							'element' => 'show_prev_next',
							'not_empty' => true,
						),
					),
					array(
						'type'       => 'dropdown',
						'heading'    => esc_html__( 'Pagination Alignment', 'locale' ),
						'param_name' => 'pagination_alignment',
						'value'      => array(
							esc_html__( 'Left', 'locale' ) => 'left',
							esc_html__( 'Center', 'locale' ) => 'center',
							esc_html__( 'Right', 'locale' ) => 'right',
						),
						'dependency' => array(
							'element' => 'show_pagination',
							'not_empty' => true,
						),
					),
					array(
						'type' => 'el_id',
						'heading' => __( 'Element ID', 'locale' ),
						'param_name' => 'el_id',
						'description' => sprintf( __( 'Enter element ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">w3c specification</a>).', 'locale' ), 'http://www.w3schools.com/tags/att_global_id.asp' ),
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Extra class name', 'locale' ),
						'param_name' => 'el_class',
						'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'locale' ),
					),
					array(
						'type' => 'css_editor',
						'heading' => __( 'Css', 'locale' ),
						'param_name' => 'css',
						'group' => __( 'Design options', 'locale' ),
					),
				),
			);
		}

	}

}
new Sermon_Taxonomy_Shortcode;
