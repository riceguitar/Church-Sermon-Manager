<?php
/**
 * The Template instance.
 *
 * @since   2.0.4
 * @package SMP\Templating
 */

namespace SMP\Templating;

defined( 'ABSPATH' ) or die;

/**
 * The template.
 *
 * @since 2.0.4
 */
final class Template {
	/**
	 * Template ID.
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * Template name.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Author name.
	 *
	 * @var string
	 */
	public $author = '';

	/**
	 * Template version.
	 *
	 * @var string
	 */
	public $version = '';

	/**
	 * Template path.
	 *
	 * @var string
	 */
	public $path = '';

	/**
	 * Default template settings.
	 *
	 * @var array
	 */
	public $default_settings = array();

	/**
	 * Raw Template metadata.
	 *
	 * @var array
	 */
	public $metadata = array();

	/**
	 * If the theme is set as default.
	 *
	 * @var bool
	 */
	public $is_default = false;

	/**
	 * Template creation date.
	 *
	 * @var string
	 */
	public $date_created = '';

	/**
	 * Template last update date.
	 *
	 * @var string
	 */
	public $date_updated = '';

	/**
	 * If the Template does not exist on the filesystem, it will be set to true.
	 *
	 * @var bool
	 */
	public $is_invalid = false;

	/**
	 * Template URL.
	 *
	 * @var string
	 */
	public $url = '';

	/**
	 * Template constructor.
	 *
	 * @param \WP_Post|object $template Template object.
	 */
	public function __construct( $template ) {
		$this->id = $template->ID;

		if ( 'Sermon Manager' !== get_the_title( $this->id ) ) {
			$this->metadata         = $this->_get_metadata();
			$this->name             = $this->_get_name();
			$this->author           = $this->_get_author();
			$this->version          = $this->_get_version();
			$this->path             = $this->_get_path();
			$this->default_settings = $this->_get_default_settings();
			$this->date_created     = $this->_get_date_created();
			$this->date_updated     = $this->_get_date_updated();
			$this->url              = $this->_get_url();

			$this->is_invalid = ! file_exists( $this->path );
		} else {
			$this->metadata         = array();
			$this->name             = 'Sermon Manager';
			$this->author           = 'WP For Church';
			$this->version          = SM_VERSION;
			$this->path             = 'Default';
			$this->default_settings = null;
			$this->date_created     = 'June 9, 2018';
			$this->date_updated     = file_exists( SM_PATH . 'views/archive-wpfc_sermon.php' ) ? date( 'F j, Y', filemtime( SM_PATH . 'views/archive-wpfc_sermon.php' ) ) : 'Unknown';
			$this->url              = 'https://wpforchurch.com';

			$this->is_invalid = false;
		}

		$this->is_default = (int) get_option( 'sm_template', Templating_Manager::get_default_template_id() ) === $this->id;
	}

	/**
	 * Gets raw metadata from the database.
	 *
	 * @access private
	 *
	 * @return array The metadata.
	 */
	private function _get_metadata() {
		return get_post_meta( $this->id, 't_metadata', true );
	}

	/**
	 * Gets Template name.
	 *
	 * @access private
	 *
	 * @return string The name.
	 */
	private function _get_name() {
		return isset( $this->metadata['name'] ) ? sanitize_text_field( $this->metadata['name'] ) : '';
	}

	/**
	 * Gets Template author.
	 *
	 * @access private
	 *
	 * @return string The author.
	 */
	private function _get_author() {
		return isset( $this->metadata['author'] ) ? sanitize_text_field( $this->metadata['author'] ) : '';
	}

	/**
	 * Gets Template version.
	 *
	 * @access private
	 *
	 * @return string The version.
	 */
	private function _get_version() {
		return isset( $this->metadata['version'] ) ? sanitize_text_field( $this->metadata['version'] ) : '';
	}

	/**
	 * Gets Template path.
	 *
	 * @access private
	 *
	 * @return string The path.
	 */
	private function _get_path() {
		$stored_path = isset( $this->metadata['path'] ) ? sanitize_text_field( $this->metadata['path'] ) : '';

		if ( '' === $stored_path ) {
			return '';
		}

		// Never trust the absolute path as stored (it may have been persisted on a
		// different machine, e.g. before a site migration). Re-root just the
		// directory's basename onto the current runtime templates directory.
		$templates_dir = trailingslashit( apply_filters( 'sm_pro_templating_templates_directory', WP_CONTENT_DIR . '/data/sermon-manager-for-wordpress/' ) );

		return $templates_dir . basename( $stored_path );
	}

	/**
	 * Gets Template default settings.
	 *
	 * @access private
	 *
	 * @return array The default settings.
	 */
	private function _get_default_settings() {
		return isset( $this->metadata['default_settings'] ) && is_array( $this->metadata['default_settings'] ) ? $this->metadata['default_settings'] : array();
	}

	/**
	 * Gets Template creation date.
	 *
	 * @access private
	 *
	 * @return string The formatted date.
	 */
	private function _get_date_created() {
		return isset( $this->metadata['date_created'] ) ? $this->metadata['date_created'] : '';
	}

	/**
	 * Gets Template last updated date.
	 *
	 * @access private
	 *
	 * @return string The formatted date.
	 */
	private function _get_date_updated() {
		return isset( $this->metadata['date_updated'] ) ? $this->metadata['date_updated'] : '';
	}

	/**
	 * Gets Template URL.
	 *
	 * @access private
	 *
	 * @return string The URL.
	 */
	private function _get_url() {
		return content_url( apply_filters( 'sm_pro_templating_templates_url', '/data/sermon-manager-for-wordpress/' ) . basename( $this->path ) );
	}

	/**
	 * Retrieve Template instance.
	 *
	 * @static
	 *
	 * @param int $id Template ID.
	 *
	 * @return Template|false Template object, false otherwise.
	 */
	public static function get_instance( $id ) {
		$id = (int) $id;
		if ( ! $id ) {
			return false;
		}

		$post = get_post( $id );
		if ( $post && 'wpfc_sm_template' !== $post->post_type ) {
			return false;
		}

		if ( ! $post ) {
			return false;
		}

		return new self( $post );
	}
}
