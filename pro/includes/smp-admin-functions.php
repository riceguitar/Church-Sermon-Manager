<?php
/**
 * Admin area related stuff.
 *
 * @package SMP
 */

defined( 'ABSPATH' ) or die;

/**
 * Includes additional settings pages for Sermon Manager.
 *
 * @param array $settings The existing settings pages.
 *
 * @return array The modified settings.
 *
 * @since 2.0.4
 */
function smp_add_additional_setting_pages( $settings ) {
	//$settings[] = include __DIR__ . '/settings/class-smp-settings-podcasting.php';
	$settings[] = include __DIR__ . '/settings/class-smp-settings-page-setup.php';
	$settings[] = include __DIR__ . '/settings/class-smp-settings-pro.php';
	$settings[] = include __DIR__ . '/settings/class-smp-settings-css.php';
	

	return $settings;
}

add_filter( 'sm_get_settings_pages', 'smp_add_additional_setting_pages' );

/**
 * Removes podcasting tab from settings.
 *
 * @param array $settings The existing settings pages.
 *
 * @return array The modified settings.
 *
 * @since 1.0.0-beta.8
 */
function smp_remove_podcasting_class_from_settings( $settings ) {
	foreach ( $settings as $id => $setting_page ) {
		if ( $setting_page instanceof SM_Settings_Podcast ) {
			unset( $settings[ $id ] );
		}
	}

	return $settings;
}

add_filter( 'sm_get_settings_pages', 'smp_remove_podcasting_class_from_settings', 90 );

/**
 * Removes the Podcasting tab from settings tabs.
 *
 * @param array $tabs Existing tabs.
 *
 * @return array Modified tabs.
 *
 * @since 1.0.0-beta.8
 */
function smp_remove_podcasting_tab_from_settings( $tabs ) {
	//unset($tabs["debug"]);
	//unset($tabs["display"]);
	//unset($tabs["podcast"]);
	//unset($tabs["verse"]);
	//unset($tabs["podcasting"]);
	return $tabs;
}

add_filter( 'sm_settings_tabs_array', 'smp_remove_podcasting_tab_from_settings', 90 );

function smp_render_color_picker_field( $value, $option_value, $description, $tooltip_html, $custom_attributes ) {
	?>
	<tr valign="top">
		<th scope="row" class="titledesc">
			<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			<?php echo $tooltip_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tooltip markup from wc-style help-tip helper. ?>
		</th>
		<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
			<input
					name="<?php echo esc_attr( $value['id'] ); ?>"
					id="<?php echo esc_attr( $value['id'] ); ?>"
					type="color"
					style="<?php echo esc_attr( $value['css'] ); ?>"
					value="<?php echo esc_attr( $option_value ); ?>"
					class="<?php echo esc_attr( $value['class'] ); ?>"
					size="<?php echo esc_attr( $value['size'] ); ?>"
					placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
				<?php echo implode( ' ', $custom_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attribute strings pre-escaped when the settings field array is built. ?>
			/><span>Current color: <?php echo esc_attr( $option_value ); ?></span>
			<?php echo $description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- settings-field description HTML composed by the plugin. ?>
		</td>
	</tr>
	<?php
}

add_action( 'sm_admin_field_smcolor', 'smp_render_color_picker_field', 10, 5 );

/**
 * Saves query vars into a global variable.
 *
 * @param array $query_vars The vars.
 *
 * @return array
 *
 * @since 2.0.4
 */
function smp_save_query_vars( $query_vars ) {
	$GLOBALS['smp_query_vars'] = $query_vars;

	return $query_vars;
}

add_filter( 'request', 'smp_save_query_vars' );

/**
 * Returns the array of normal WordPress pages as ID => title.
 *
 * @since 1.0.0-beta.5
 *
 * @return array
 */
function smp_get_pages_array() {
	$pages    = get_pages();
	$settings = array(
		0 => '-- ' . __( 'None', 'church-sermon-manager' ) . ' --',
	);

	foreach ( $pages as $page ) {
		$settings[ $page->ID ] = $page->post_title;
	}

	return $settings;
}