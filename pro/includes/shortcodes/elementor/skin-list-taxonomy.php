<?php
/**
 * Registers a List-layout skin. For taxonomies.
 *
 * @since   2.0.4
 * @package SMP\Shortcodes\Elementor
 */

namespace SMP\Shortcodes\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;

defined( 'ABSPATH' ) or exit;

/**
 * Class Skin_List_Taxonomy.
 *
 * @package SMP\Shortcodes\Elementor
 */
class Skin_List_Taxonomy extends Skin_Base {
	/**
	 * Returns the skin title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'List', 'church-sermon-manager' );
	}

	/**
	 * Returns the skin ID. (slug)
	 *
	 * @return string
	 */
	public function get_id() {
		return 'wpfc-list-taxonomy';
	}

	/**
	 * Style settings section.
	 *
	 * @param Widget_Base $widget The class instance.
	 */
	public function register_style_sections( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->register_design_content_controls();
	}

	/**
	 * Design content controls.
	 */
	protected function register_design_content_controls() {

		$this->start_controls_section(
			'section_design_content',
			array(
				'label' => __( 'Title', 'church-sermon-manager' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_padding',
			array(
				'label'     => __( 'Title Padding', 'church-sermon-manager' ),
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
					'.wpfc-term-list .wpfc-term' => 'padding-bottom: {{SIZE}}px;',
				)
			)
		);
				
		$this->add_control(
			'title_color',
			array(
				'label'     => __( 'Color', 'church-sermon-manager' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'.wpfc-term-list .wpfc-term a' => 'color: {{VALUE}};',
				)
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'title_typography',
				'scheme'    => Typography::TYPOGRAPHY_1,
				'selector'  => '.wpfc-term-list .wpfc-term',
			)
		);
		
		$this->add_control(
			'title_alignment',
			array(
				'label'        => __( 'Alignment', 'church-sermon-manager' ),
				'type'         => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'options'      => array(
					'left'   => array(
						'title' => __( 'Left', 'church-sermon-manager' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'church-sermon-manager' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'church-sermon-manager' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors' => array(
					'.wpfc-term-list .wpfc-term' => 'text-align: {{VALUE}};',
				)
			)
		);
		
		$this->end_controls_section();
	}
	
	/**
	 * Renders term in list layout on the frontend.
	 */
	public function render() {
		if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
			define( 'SM_ENQUEUE_SCRIPTS_STYLES', true );
		}

		$terms    = $this->parent->query_terms();
		$settings = $this->parent->get_settings();

		echo '<div class="wpfc-term-view">';

		if ( $settings['show_alphabetically'] ) {
			$terms_separated_by_first_letter = array();

			foreach ( $terms as $term ) {
				$terms_separated_by_first_letter[ strtoupper( substr( ( trim( preg_replace( '/^\d+/', '', $term->name ) ?: $term->name ) ), 0, 1 ) ) ][] = $term;
			}

			echo '<div class="wpfc-term-content wpfc-term-letter-content elementor-grid">';
			foreach ( $terms_separated_by_first_letter as $letter => $terms ) {
				?>
				<div class="wpfc-term-letter-section">
					<div class="wpfc-term-letter"><?php echo esc_html( $letter ); ?></div>
					<div class="wpfc-term-list">
						<?php foreach ( $terms as $term ) : ?>
							<div class="wpfc-term">
								<a href="<?php echo esc_url( get_term_link( $term, $settings['taxonomy'] ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php
			}
			echo '</div>';
		} else {
			?>
			<div class="wpfc-term-content">
				<div class="wpfc-term-list elementor-grid">
					<?php foreach ( $terms as $term ) : ?>
						<div class="wpfc-term">
							<a href="<?php echo esc_url( get_term_link( $term, $settings['taxonomy'] ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="wpfc-term-pagination">
				<?php $this->parent->render_pagination(); ?>
			</div>
			<?php
		}

		echo '</div>';
	}

	/**
	 * Init controls.
	 */
	protected function _register_controls_actions() {
				
		// Style settings.
		add_action( 'elementor/element/sermon_taxonomy/section_query/after_section_end', array( $this, 'register_style_sections' ) );
	}
}
