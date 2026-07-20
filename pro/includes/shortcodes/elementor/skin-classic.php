<?php
/**
 * Registers a Classic-layout skin.
 *
 * @since   2.0.4
 * @package SMP\Shortcodes\Elementor
 */

namespace SMP\Shortcodes\Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use SMP\Templating\Templating_Manager;

defined( 'ABSPATH' ) or exit;

/**
 * Class Skin_Classic, copies Elementor's Skin_Classic.
 *
 * @package SMP\Shortcodes\Elementor
 */
class Skin_Classic extends Skin_Base {
	/**
	 * Returns the skin ID. (slug)
	 *
	 * @return string
	 */
	public function get_id() {
		return 'wpfc-classic';
	}

	/**
	 * Returns the skin title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Classic', 'church-sermon-manager' );
	}

	/**
	 * Enqueues the scripts and styles.
	 */
	public function render() {
		if ( ! defined( 'SM_ENQUEUE_SCRIPTS_STYLES' ) ) {
			define( 'SM_ENQUEUE_SCRIPTS_STYLES', true );
		}

		parent::render();
	}

	/**
	 * Renders sermons in classic layout on the frontend.
	 */
	public function render_post() {
		if ( defined( 'SMPRO_RENDER_ERROR' ) ) {
			echo '<div class="notice notice-warning"><p>Rendering of the next item has been canceled, due to an error in the rendering of the previous item.</p></div>';

			return;
		}

		$thumbnail_key                        = $this->get_control_id( 'thumbnail_size' );
		$thumbnail_settings                   = $this->parent->get_settings();
		$thumbnail_settings[ $thumbnail_key ] = array(
			'id' => get_post_thumbnail_id(),
		);

		$settings = array(
			// Sermon columns.
			'columns'            => $this->get_instance_value( 'columns' ),
			// Thumbnail related settings.
			'thumbnail_position' => $this->get_instance_value( 'thumbnail' ),
			'thumbnail_size'     => $this->get_control_id( 'thumbnail_size' ),
			// Title related settings.
			'show_title'         => $this->get_instance_value( 'show_title' ),
			'title_tag'          => $this->get_instance_value( 'title_tag' ),
			// Metadata related settings.
			'meta_data_header'   => $this->get_instance_value( 'meta_data_header' ),
			'meta_data_footer'   => $this->get_instance_value( 'meta_data_footer' ),
			'meta_separator'     => $this->get_instance_value( 'meta_separator' ),
			// Excerpt related settings.
			'show_excerpt'       => $this->get_instance_value( 'show_excerpt' ),
			'excerpt_length'     => $this->get_instance_value( 'excerpt_length' ),
			// Read more.
			'show_read_more'     => $this->get_instance_value( 'show_read_more' ),
			'read_more_text'     => $this->get_instance_value( 'read_more_text' ),
		);

		$args = array(
			'thumbnail_html' => Group_Control_Image_Size::get_attachment_image_html( $thumbnail_settings, $thumbnail_key ),
			'settings'       => $settings,
			'elementor'      => true,
		);

		/**
		 * Allows to filter Elementor settings and other arguments.
		 *
		 * @since 2.0.4
		 */
		$args = apply_filters( 'smp/shortcodes/archive/skin_classic/render_args', $args );

		try {
			echo Templating_Manager::render( 'archive-elementor', null, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered view markup from the templating pipeline.
		} catch ( \RuntimeException $e ) {
			define( 'SMPRO_RENDER_ERROR', true );
			echo '<div class="notice notice-error"><p><strong>Church Sermon Manager</strong>: Error in rendering the view, error message: "' . esc_html( $e->getMessage() ) . '"</p></div>';
		}
	}

	/**
	 * Register settings for SM's element as well.
	 *
	 * @access protected
	 */
	protected function _register_controls_actions() {
		parent::_register_controls_actions();

		add_action( 'elementor/element/sermon_archive/section_layout/before_section_end', array(
			$this,
			'register_controls',
		) );
		add_action( 'elementor/element/sermon_archive/section_query/after_section_end', array(
			$this,
			'register_style_sections',
		) );
	}

	/**
	 * Override the default function.
	 *
	 * - Change the label.
	 */
	protected function register_post_count_control() {
		parent::register_post_count_control();
		$this->update_control(
			'posts_per_page',
			array(
				'label' => __( 'Sermons Per Page', 'church-sermon-manager' ),
			)
		);
	}

	/**
	 * Override the default function.
	 *
	 * - Add more meta fields.
	 */
	protected function register_meta_data_controls() {
		$this->add_control(
			'meta_data_header',
			array(
				'label'       => __( 'Meta Data - Header', 'church-sermon-manager' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'default'     => array( 'date', 'comments' ),
				'multiple'    => true,
				'options'     => array(
					'date'          => __( 'Publish Date', 'church-sermon-manager' ),
					'time'          => __( 'Publish Time', 'church-sermon-manager' ),
					'comments'      => __( 'Comments', 'church-sermon-manager' ),
					'preached_date' => __( 'Preached Date', 'church-sermon-manager' ),
					'preachers'     => __( 'Preachers', 'church-sermon-manager' ),
					'passage'       => __( 'Passage', 'church-sermon-manager' ),
					'series'        => __( 'Series', 'church-sermon-manager' ),
					'service_type'  => __( 'Service Type', 'church-sermon-manager' ),
					'books'         => __( 'Bible Books', 'church-sermon-manager' ),
				),
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'meta_data_footer',
			array(
				'label'       => __( 'Meta Data - Footer', 'church-sermon-manager' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT2,
				'default'     => array( 'date', 'comments' ),
				'multiple'    => true,
				'options'     => array(
					'date'          => __( 'Publish Date', 'church-sermon-manager' ),
					'time'          => __( 'Publish Time', 'church-sermon-manager' ),
					'comments'      => __( 'Comments', 'church-sermon-manager' ),
					'preached_date' => __( 'Preached Date', 'church-sermon-manager' ),
					'preachers'     => __( 'Preachers', 'church-sermon-manager' ),
					'passage'       => __( 'Passage', 'church-sermon-manager' ),
					'series'        => __( 'Series', 'church-sermon-manager' ),
					'service_type'  => __( 'Service Type', 'church-sermon-manager' ),
					'books'         => __( 'Bible Books', 'church-sermon-manager' ),
				),
			)
		);

		parent::register_meta_data_controls();
		$this->remove_control( 'meta_data' );
	}

	/**
	 * Override the default function.
	 *
	 * - Remove "right" image position.
	 */
	protected function register_thumbnail_controls() {
		parent::register_thumbnail_controls();

		$this->update_control(
			'thumbnail',
			array(
				'options'      => array(
					'top'  => __( 'Top', 'elementor-pro' ),
					'left' => __( 'Left', 'elementor-pro' ),
					'none' => __( 'None', 'elementor-pro' ),
				),
				'prefix_class' => 'sm-pro-sermon-thumbnail-',
			)
		);
	}

	/**
	 * Override the default function.
	 *
	 * - Force cards layout.
	 */
	protected function render_loop_header() {
		$this->parent->add_render_attribute( 'container', array(
			'class' => array(
				'elementor-posts-container',
				'elementor-posts',
				'elementor-grid',
				'elementor-posts--skin-cards',
				'elementor-has-item-ratio',
				'elementor-card-shadow-yes',
				$this->get_container_class(),
			),
		) );
		?>
	<div <?php echo $this->parent->get_render_attribute_string( 'container' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute string built and escaped by Elementor. ?>>
		<?php
	}
}
