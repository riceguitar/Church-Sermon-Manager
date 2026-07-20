<?php
/**
 * Registers a Cards-layout skin. For taxonomies.
 *
 * @since   2.0.4
 * @package SMP\Shortcodes\Elementor
 */

namespace SMP\Shortcodes\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;

defined( 'ABSPATH' ) or exit;

/**
 * Class Skin_Cards_Taxonomy.
 *
 * @package SMP\Shortcodes\Elementor
 */
class Skin_Cards_Taxonomy extends Skin_Base {
	/**
	 * Returns the skin title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Cards', 'church-sermon-manager' );
	}

	/**
	 * Returns the skin ID. (slug)
	 *
	 * @return string
	 */
	public function get_id() {
		return 'wpfc-cards-taxonomy';
	}
	
	/**
	 * Style settings section.
	 *
	 * @param Widget_Base $widget The class instance.
	 */
	public function register_style_sections( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->register_design_controls();
	}

	/**
	 * Design controls.
	 */
	public function register_design_controls() {
		$this->register_design_layout_controls();
		$this->register_design_image_controls();
		$this->register_design_content_controls();
	}

	/**
	 * Style Tab
	 */
	protected function register_design_layout_controls() {
		$this->start_controls_section(
			'section_design_layout',
			array(
				'label' => __( 'Layout', 'elementor-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'column_gap',
			array(
				'label'     => __( 'Column Gap', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 15,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'.elementor-grid.elementor-posts--skin-cards.sm-pro-taxonomies' => 'grid-gap: {{SIZE}}px;',
				),
			)
		);
		
		$this->end_controls_section();
	}

	/**
	 * Design image controls.
	 */
	protected function register_design_image_controls() {
		$this->start_controls_section(
			'section_design_image',
			array(
				'label'     => __( 'Image', 'elementor-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					$this->get_control_id( 'show_image' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'image_padding',
			array(
				'label'     => __( 'Image Padding', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 15,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'.elementor-grid .wpfc-term-grid-image' => 'margin-bottom: {{SIZE}}px;',
				),
				'condition' => array(
					$this->get_control_id( 'show_image' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Design content controls.
	 */
	protected function register_design_content_controls() {

		$this->start_controls_section(
			'section_design_content',
			array(
				'label' => __( 'Content', 'elementor-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_title_style',
			array(
				'label'     => __( 'Title', 'elementor-pro' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'title_padding',
			array(
				'label'     => __( 'Title Padding', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 0,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'.elementor-grid .wpfc-term-title' => 'padding-bottom: {{SIZE}}px;',
				),
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);
				
		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'.elementor-grid .wpfc-term-title' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'title_typography',
				'scheme'    => Typography::TYPOGRAPHY_1,
				'selector'  => '.elementor-grid .wpfc-term-title',
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);
		
		$this->add_control(
			'title_alignment',
			array(
				'label'        => __( 'Alignment', 'elementor-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'options'      => array(
					'left'   => array(
						'title' => __( 'Left', 'elementor-pro' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'elementor-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'elementor-pro' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors' => array(
					'.elementor-grid .wpfc-term-title' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);
		
		$this->add_control(
			'heading_desc_style',
			array(
				'label'     => __( 'Description', 'elementor-pro' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					$this->get_control_id( 'show_desc' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'description_padding',
			array(
				'label'     => __( 'Description Padding', 'elementor-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 0,
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'.elementor-grid .wpfc-term-description' => 'padding-bottom: {{SIZE}}px;',
				),
				'condition' => array(
					$this->get_control_id( 'show_desc' ) => 'yes',
				),
			)
		);
		
		$this->add_control(
			'description_color',
			array(
				'label'     => __( 'Color', 'elementor-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'.elementor-grid .wpfc-term-description' => 'color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id( 'show_desc' ) => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'description_typography',
				'scheme'    => Typography::TYPOGRAPHY_1,
				'selector'  => '.elementor-grid .wpfc-term-description',
				'condition' => array(
					$this->get_control_id( 'show_desc' ) => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}
	
	/**
	 * Register Controls.
	 *
	 * @param Widget_Base $widget The widget instance.
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->register_title_controls();
		$this->register_desc_controls();
		$this->register_thumbnail_controls();
	}

	
	/**
	 * Show/hide title, title HTML tag, etc.
	 */
	protected function register_title_controls() {
		$this->add_control(
			'show_title',
			array(
				'label'        => __( 'Title', 'church-sermon-manager' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'church-sermon-manager' ),
				'label_off'    => __( 'Hide', 'church-sermon-manager' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);
		
		$this->add_control(
			'title_tag',
			array(
				'label'     => __( 'Title HTML Tag', 'church-sermon-manager' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'default'   => 'h3',
				'condition' => array(
					$this->get_control_id( 'show_title' ) => 'yes',
				),
			)
		);
	}

	/**
	 * Show/hide description, description limit, etc...
	 */
	private function register_desc_controls() {
		$this->add_control(
			'show_desc',
			array(
				'label'        => __( 'Description', 'church-sermon-manager' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'elementor-pro' ),
				'label_off'    => __( 'Hide', 'elementor-pro' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'desc_length',
			array(
				'label'     => __( 'Description Length', 'church-sermon-manager' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => apply_filters( 'excerpt_length', 25 ),
				'condition' => array(
					$this->get_control_id( 'show_desc' ) => 'yes',
				),
			)
		);
	}

	/**
	 * Registers thumbnail controls.
	 */
	protected function register_thumbnail_controls() {
		$this->add_control(
			'show_image',
			array(
				'label'       => __( 'Show Image', 'church-sermon-manager' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'render_type' => 'template',
				'separator' => 'before',
			)
		);
	}

	/**
	 * Renders term in card layout on the frontend.
	 */
	public function render() {
		if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
			define( 'SM_ENQUEUE_SCRIPTS_STYLES', true );
		}

		$terms = $this->parent->query_terms();

		$settings = $this->get_settings_for_display();
		
		$args = array(
			'thumbnail_html' => '',
			'settings'       => $settings,
			'elementor'      => true,
		);

		/**
		 * Allows to filter Elementor settings and other arguments.
		 *
		 * @since 2.0.4
		 */
		$args = apply_filters( 'smp/shortcodes/taxonomy/render_args', $args );
		
		switch ( $settings['taxonomy'] ) {
			case 'series':
				$taxonomy = 'wpfc_sermon_series';
				break;
			case 'preachers':
				$taxonomy = 'wpfc_preacher';
				break;
			case 'topics':
				$taxonomy = 'wpfc_sermon_topics';
				break;
			case 'books':
				$taxonomy = 'wpfc_bible_book';
				break;
			case 'service_types':
				$taxonomy = 'wpfc_service_type';
				break;
			case 'books_order':
				$taxonomy   = 'wpfc_bible_book';
				$order_book = true;
		}

		if ( empty( $taxonomy ) ) {
			return array();
		}
		
		?>
		<div class="elementor-posts-container elementor-posts elementor-grid elementor-posts--skin-cards elementor-has-item-ratio sm-pro-taxonomies elementor-card-shadow-yes elementor-posts--thumbnail-top">
			<?php if ( ! empty( $terms ) ) : ?>
				<?php foreach ( $terms as $term ) : ?>
					
					<div class="wpfc-term">
					
					<?php
					if ( ($settings['show_image']) ) { 
						$associations = get_option( 'sermon_image_plugin' );

						if ( ! empty( $associations[ $term->term_id ] ) ) {
							$image_id = (int) $associations[ $term->term_id ];
						} else {
							$image_id = null;
						}

						if ( $image_id ) {
							/* @noinspection CssUnknownTarget */
							echo sprintf(
								'<a href="' . get_term_link( $term, $taxonomy ) . '" class="wpfc-term-grid-image" style="background-image:url(%s);"></a>',
								wp_get_attachment_image_url( $image_id, array( 300, 300 ) )
							);
						} else {
							echo sprintf( '<a href="' . get_term_link( $term, $taxonomy ) . '" class="wpfc-term-grid-image" style="background-color:#cecece;"></a>' );
						}
					}
					?>
					
					<?php if ( $settings['show_title'] or $settings['show_desc'] ) { ?>
					
						<div class="wpfc-term-inner">
						
							<?php if ($settings['show_title']) { ?>
							<<?php echo $settings['title_tag']; ?>> 
								<a href="<?php echo get_term_link( $term, $taxonomy ); ?>"
									class="wpfc-term-title"><?php echo $term->name; ?></a>
							</<?php echo $settings['title_tag']; ?>> 
							<?php } ?>
							
							<?php if ($settings['show_desc']) { ?>
								<div class="wpfc-term-description"><?php echo wp_trim_words( $term->description, $settings['desc_length'], '...' ); ?></div>
							<?php } ?>
							
						</div>
					
						<?php } ?>
						
					</div>
							
				<?php endforeach; ?>
			<?php else : ?>
				<div class="terms-404">
					<?php echo __( 'No terms found.', 'church-sermon-manager' ); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
		$this->parent->render_pagination();
	}

	/**
	 * Init controls.
	 */
	protected function _register_controls_actions() {
		// Additional Layout settings.
		add_action( 'elementor/element/sermon_taxonomy/section_layout/before_section_end', array(
			$this,
			'register_controls',
		) );

		// Style settings.
		add_action( 'elementor/element/sermon_taxonomy/section_query/after_section_end', array( $this, 'register_style_sections' ) );
	}
}
