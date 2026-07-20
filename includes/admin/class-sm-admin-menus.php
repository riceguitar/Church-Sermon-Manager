<?php
/**
 * Registers SM related menus.
 *
 * @package SM/Core/Admin/Menus
 */

defined( 'ABSPATH' ) or die;

/**
 * Setup menus in WP admin.
 *
 * @since 2.9
 */
class SM_Admin_Menus {
	/**
	 * SM_Admin_Menus constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 60 );
		add_action( 'admin_menu', array( $this, 'import_export_menu' ), 70 );
		add_action( 'admin_menu', array( $this, 'migrate_pro_content_menu' ), 80 );

		add_action( 'admin_enqueue_scripts', array( $this, 'fix_icon' ) );

		// Fix first submenu menu name (Sermons => All Sermons).
		add_action( 'admin_menu', array( $this, 'fix_sermons_title' ), 100 );
	}

	/**
	 * Add menu item.
	 */
	public function settings_menu() {
		add_submenu_page( 'edit.php?post_type=wpfc_sermon', __( 'Sermon Manager Settings', 'church-sermon-manager' ), __( 'Settings', 'church-sermon-manager' ), 'manage_wpfc_sm_settings', 'sm-settings', array(
			$this,
			'settings_page',
		) );
	}

	/**
	 * Add menu item.
	 */
	public function import_export_menu() {
		add_submenu_page( 'edit.php?post_type=wpfc_sermon', __( 'Sermon Manager Import/Export', 'church-sermon-manager' ), __( 'Import/Export', 'church-sermon-manager' ), 'manage_wpfc_sm_settings', 'sm-import-export', array(
			$this,
			'import_export_page',
		) );
	}

	/**
	 * Add menu item for Pro Content Migration.
	 */
	public function migrate_pro_content_menu() {
		add_submenu_page(
			'edit.php?post_type=wpfc_sermon',
			__( 'Migrate Pro Content', 'church-sermon-manager' ),
			__( 'Migrate Pro Content', 'church-sermon-manager' ),
			'manage_wpfc_sm_settings',
			'sm-migrate-pro-content',
			array( $this, 'migrate_pro_content_page' )
		);
	}

	/**
	 * Init the settings page.
	 */
	public function settings_page() {
		SM_Admin_Settings::output();
	}

	/**
	 * Init the settings page.
	 */
	public function import_export_page() {
		wp_enqueue_script( 'import-export-js', SM_URL . 'assets/js/admin/import-export' . ( ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) ? '' : '.min' ) . '.js', array(), SM_VERSION );
		SM_Admin_Import_Export::output();
	}

	/**
	 * Output the migration tool page.
	 */
	public function migrate_pro_content_page() {
		if ( ! current_user_can( 'manage_wpfc_sm_settings' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'church-sermon-manager' ) );
		}

		$updated = false;
		$overwrite = isset( $_POST['sm_migrate_overwrite'] ) ? (bool) $_POST['sm_migrate_overwrite'] : false;
		$results = array();

		if ( isset( $_POST['sm_migrate_pro_content'] ) && check_admin_referer( 'sm_migrate_pro_content_action', 'sm_migrate_pro_content_nonce' ) ) {
			$results = sm_migrate_pro_content( $overwrite );
			$updated = true;
		}
		?>
		<div class="wrap">
			<h1><?php _e( 'Migrate Pro Content to Native', 'church-sermon-manager' ); ?></h1>
			<p><?php _e( 'This tool will copy the content from the Pro plugin\'s "sermon_description" meta field into the native post_content field for all sermons. Use with caution.', 'church-sermon-manager' ); ?></p>
			<form method="post">
				<?php wp_nonce_field( 'sm_migrate_pro_content_action', 'sm_migrate_pro_content_nonce' ); ?>
				<label><input type="checkbox" name="sm_migrate_overwrite" value="1" <?php checked( $overwrite ); ?> /> <?php _e( 'Overwrite existing post content (if any)', 'church-sermon-manager' ); ?></label><br><br>
				<input type="submit" name="sm_migrate_pro_content" class="button button-primary" value="<?php esc_attr_e( 'Run Migration', 'church-sermon-manager' ); ?>" />
			</form>
			<?php if ( $updated ) : ?>
				<h2><?php _e( 'Migration Results', 'church-sermon-manager' ); ?></h2>
				<ul>
					/* translators: %d: number of sermons checked. */
					<li><?php printf( __( 'Total sermons checked: %d', 'church-sermon-manager' ), $results['total'] ); ?></li>
					/* translators: %d: number of posts updated. */
					<li><?php printf( __( 'Posts updated: %d', 'church-sermon-manager' ), $results['updated'] ); ?></li>
					/* translators: %d: number of posts skipped. */
					<li><?php printf( __( 'Posts skipped: %d', 'church-sermon-manager' ), $results['skipped'] ); ?></li>
				</ul>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Fixes Sermon Manager top-level icon.
	 */
	public function fix_icon() {
		wp_enqueue_style( 'sm-icon', SM_URL . 'assets/css/admin-icon.css', array(), SM_VERSION );
	}

	/**
	 * Changes child menu item name to All Sermons.
	 */
	public function fix_sermons_title() {
		global $submenu;

		if ( ! isset( $submenu['edit.php?post_type=wpfc_sermon'] ) ) {
			return;
		}

		foreach ( $submenu['edit.php?post_type=wpfc_sermon'] as &$sermon_item ) {
			if ( 'edit.php?post_type=wpfc_sermon' === $sermon_item[2] ) {
				$sermon_item[0] = __( 'All Sermons', 'church-sermon-manager' );
				return;
			}
		}
	}
}

return new SM_Admin_Menus();
