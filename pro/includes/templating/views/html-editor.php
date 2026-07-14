<?php
/**
 * The editor page for Templating, overrides whole HTML
 *
 * @since   2.0.4
 * @package SMP\Templating\Editor
 */

defined( 'ABSPATH' ) or die;

wp_enqueue_script( 'wpfc-sm-pro-templating-uikit', SMP_URL . 'assets/vendor/uikit/js/uikit.min.js' );
wp_enqueue_style( 'wpfc-sm-pro-templating', SMP_URL . 'assets/css/admin/editor.min.css' );
wp_enqueue_style( 'wpfc-sm-pro-templating-uikit', SMP_URL . 'assets/vendor/uikit/css/uikit.css' );
wp_enqueue_script( 'wp-color-picker' );

$all_settings              = apply_filters( 'sm_pro_get_templating_settings', array() );
$tabs                      = \SMP\Templating\Settings::get_tabs();
$template                  = \SMP\Templating\Templating_Manager::get_template( $_GET['post'] );
$post                      = get_post( $_GET['post'] );
$template_settings         = get_post_meta( $post->ID, 'sm_template_settings', true );
$default_template_settings = $template->default_settings;

$show_notice = get_option( 'sm_templating_saved_notice', 0 );

require_once ABSPATH . 'wp-admin/admin-header.php';
?>

<div class="wpfc-templating">

	<!-- Notice -->
	<?php if ( $show_notice ) : ?>
		<div class="notice notice-success">
			<p>Settings successfully saved.</p>
		</div>
		<?php update_option( 'sm_templating_saved_notice', 0 ); ?>
	<?php endif; ?>

	<div class="uk-container" style="margin: 15px 0 0; padding-left: 15px; padding-right: 15px; max-width: 1000px">

	<!-- Navigation -->
		<div class=" uk-flex uk-flex-between uk-flex-wrap  uk-background-secondary  side-menu">
		
			<h2 style="color: white; padding-top:10px;padding-left: 15px;">Edit Template</h2>
			


			<ul style="padding: 19px" class="uk-nav  wpfc-nav" uk-switcher="connect: #settings-container">
				<?php foreach ( $tabs as $tab ) : ?>
					<li style="float:left;"><a href="#<?php echo sanitize_title( $tab ); ?>"><?php echo $tab; ?></a></li>
				<?php endforeach; ?>
			</ul>

		</div>
		
	
</div>

	<!-- Editor -->
	<div class="uk-container" style="margin: 15px 0 0; padding-left: 15px; padding-right: 15px; max-width: 1000px">
		<div class="uk-grid uk-grid-small uk-grid-match" uk-margin>

			

			<!-- Content -->
			<div class="uk-width-expand">
				<form class="uk-form-stacked" name="post" action="post.php" method="post">
					<div class="uk-flex uk-flex-between uk-flex-wrap uk-margin-bottom" uk-margin>
						<div class="">
							<input class="uk-input" type="text" id="title"
									value="<?php echo $post->post_title; ?>" name="title"
									title="Template Title">
						</div>

						<div class="" uk-margin>
							<a href="<?php echo admin_url( 'edit.php?post_type=wpfc_sm_template' ); ?>"
									class="uk-button uk-button-default">
								Cancel
							</a>
							<button class="uk-button uk-button-danger"
									uk-toggle="target: #delete-confirmation" type="button">Delete
							</button>
							<button class="uk-button uk-button-primary" type="submit" name="save">Save
								Changes
							</button>
						</div>
					</div>

					<ul id="settings-container" class="uk-switcher uk-background-muted uk-padding">
						<?php foreach ( $all_settings as $tab => $settings ) : ?>
							<li>
								<h2 class="uk-heading-divider"><?php echo $tab; ?></h2>

								<?php foreach ( $settings as $field ) : ?>
									<div class="uk-margin-bottom">
										<?php
										// Populate field with default data if it's not set.
										$field =
											$field + array(
												'title'           => '',
												'type'            => '',
												'id'              => '',
												'options'         => array(),
												'default'         => '',
												'disabled'        => '',
												'dynamic_message' => '',
											);

										// This might not be required.
										$form_controls_style = in_array( $field['type'], array(
											'checkbox',
											'radio',
										) ) ? 'display:inline-block;' : '';

										// Ternary doesn't work here for some reason, too tired to investigate. This will work fine as well.
										if ( isset( $template_settings[ $field['id'] ] ) ) {
											$value = $template_settings[ $field['id'] ];
										} elseif ( isset( $default_template_settings[ $field['id'] ] ) ) {
											$value = $default_template_settings[ $field['id'] ];
										} else {
											$value = \SMP\Templating\Settings::get_setting( $field['id'], $field['default'] );
										}
										?>

										<?php
										if ( in_array( $field['type'], array(
											'title',
											'title_divider',
										) ) ) :
											?>
											<h1 class="<?php echo 'title' === $field['type'] ? 'uk-heading-divider uk-text-left' : 'uk-heading-divider uk-margin-medium-top'; ?> uk-text-large">
												<span><?php echo $field['title']; ?></span></h1>
											<?php continue; ?>
										<?php endif; ?>

										<?php if ( in_array( $field['type'], array( 'checkbox', 'color' ) ) ) : ?>
											<?php echo '<div class="uk-grid"><div class="uk-width-2-3">'; ?>
										<?php endif; ?>

										<?php if ( $field['title'] ) : ?>
											<label class="uk-form-label"
													for="<?php echo $field['id']; ?>"
												<?php echo isset( $field['desc_tip'] ) ? 'uk-tooltip="title: ' . $field['desc_tip'] . '; pos: top-left"' : ''; ?>>
												<?php echo $field['title']; ?>
											</label>
										<?php endif; ?>

										<?php if ( in_array( $field['type'], array( 'checkbox', 'color' ) ) ) : ?>
											<?php echo '</div>'; ?>
										<?php endif; ?>

										<div class="uk-form-controls">

											<?php
											switch ( $field['type'] ) {
												case 'select':
												case 'multiselect':
													?>
													<select class="uk-select"
															id="<?php echo esc_attr( $field['id'] ); ?>"
															name="<?php echo esc_attr( $field['id'] ); ?><?php echo ( 'multiselect' === $field['type'] ) ? '[]' : ''; ?>"
															class="<?php echo $field['disabled'] ? 'uk-disabled' : ''; ?>"
														<?php echo $field['disabled'] ? 'disabled="disabled"' : ''; ?>
														<?php echo ( 'multiselect' === $field['type'] ) ? 'multiple="multiple"' : ''; ?>
													>
														<?php foreach ( $field['options'] as $key => $name ) : ?>
															<option <?php echo $value == $key || ( ! $value && isset( $field['default'] ) && $field['default'] == $key ) ? 'selected' : ''; ?>
																	value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $name ); ?></option>
														<?php endforeach; ?>
													</select>
													<?php
													break;
												case 'image':
													?>
													<div class="js-upload" uk-form-custom>
														<input type="file"/>
														<button class="uk-button uk-button-default <?php echo $field['disabled'] ? 'uk-disabled' : ''; ?>"
																type="button" tabindex="-1">Select image
														</button>
													</div>
													<?php
													break;
												case 'radio':
													$keys = array_keys( $field['options'] );
													$value = $value ?: $keys[0];
													foreach ( $field['options'] as $key => $option ) :
														?>
														<label class="uk-margin-right">
															<input
																	name="<?php echo esc_attr( $field['id'] ); ?>"
																	type="<?php echo esc_attr( $field['type'] ); ?>"
																	id="<?php echo esc_attr( $field['id'] ); ?>"
																	class="<?php echo $field['disabled'] ? 'uk-disabled' : ''; ?>"
																<?php echo $field['disabled'] ? 'disabled="disabled"' : ''; ?>
																<?php checked( $value, $key ); ?>
															/>
															<?php echo esc_attr( $option ); ?>
														</label>
													<?php
													endforeach;
													break;
												case 'text':
												case 'email':
												case 'number':
												case 'password':
												case 'color':
												case 'checkbox':
													?>
													<input name="<?php echo esc_attr( $field['id'] ); ?>"
															type="<?php echo esc_attr( $field['type'] ); ?>"
															id="<?php echo esc_attr( $field['id'] ); ?>"
															class="<?php echo $field['disabled'] ? 'uk-disabled' : ''; ?>"
														<?php echo 'checkbox' !== $field['type'] ? ( 'value="' . esc_attr( $value ) . '"' ) : ''; ?>
														<?php echo $field['disabled'] ? 'disabled="disabled"' : ''; ?>
														<?php echo 'color' === $field['type'] && $field['default'] ? 'data-default-color="' . esc_attr( $field['default'] ) . '"' : ''; ?>
														<?php echo 'checkbox' === $field['type'] ? checked( is_bool( $value ) ? ( $value ? 'yes' : 'no' ) : $value, 'yes', false ) : ''; ?>
													>
													<?php
													break;
												case 'description':
													?>
													<tr valign="top">
														<td class="forminp forminp-<?php echo sanitize_title( $field['type'] ); ?>"
																colspan="2">
															<p><?php echo $field['desc']; ?></p>
														</td>
													</tr>
													<?php
													break;
												default:
													var_dump( $field );
													break;
											}
											?>
										</div>
										<?php if ( in_array( $field['type'], array( 'checkbox', 'color' ) ) ) : ?>
											<?php echo '</div>'; ?>
										<?php endif; ?>

										<?php
										if ( $field['dynamic_message'] ) {
											$result = null;

											if ( is_string( $field['dynamic_message'] ) && function_exists( $field['dynamic_message'] ) ) {
												$result = call_user_func( $field['dynamic_message'] );
											} elseif ( is_array( $field['dynamic_message'] ) && function_exists( $field['dynamic_message'][0] ) ) {
												$function = $field['dynamic_message'];
												unset( $field['dynamic_message'][0] );
												$result = call_user_func_array( $function, $field['dynamic_message'] );
											}

											echo $result ?: '';
										}
										?>
									</div>
								<?php endforeach; ?>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php wp_nonce_field( 'update-post_' . $post->ID ); ?>
					<input type="hidden" id="post-id" name="post_ID"
							value="<?php echo $post->ID; ?>">
					<input type="hidden" id="user-id" name="user_ID"
							value="<?php echo (int) get_current_user_id(); ?>"/>
					<input type="hidden" id="hiddenaction" name="action"
							value="<?php echo esc_attr( 'editpost' ); ?>"/>
					<input type="hidden" id="originalaction" name="originalaction"
							value="<?php echo esc_attr( 'editpost' ); ?>"/>
					<input type="hidden" id="post_author" name="post_author"
							value="<?php echo esc_attr( $post->post_author ); ?>"/>
					<input type="hidden" id="post_type" name="post_type"
							value="<?php echo esc_attr( $post->post_type ); ?>"/>
					<input type="hidden" id="original_post_status" name="original_post_status"
							value="<?php echo esc_attr( $post->post_status ); ?>"/>
					<input type="hidden" id="referredby" name="referredby"
							value="<?php echo wp_get_referer() ? esc_url( wp_get_referer() ) : ''; ?>"/>
				</form>
				<div id="delete-confirmation" uk-modal>
					<div class="uk-modal-dialog uk-modal-body">
						<h2 class="uk-modal-title">Are you sure?</h2>
						<p>Are you sure that you want to delete the template named
							<b><?php echo $post->post_title; ?></b>? </p>
						<p class="uk-text-danger">All <b>changes</b> and <b>filesystem modifications</b> will be
							permanently lost.</p>
						<p class="uk-text-right">
							<a href="<?php echo get_delete_post_link( $post->ID, '', true ); ?>"
									class="uk-button uk-button-danger" type="button">Yes</a>
							<button class="uk-button uk-button-default uk-modal-close"
									type="button">
								No
							</button>
						</p>
					</div>
				</div>
			</div>

		</div>
	</div>

</div>
