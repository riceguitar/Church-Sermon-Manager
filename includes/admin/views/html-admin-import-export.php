<?php
/**
 * HTML for import/export page.
 *
 * @package SM/Core/Admin/Views
 */

defined( 'ABSPATH' ) or die;
?>
<div class="sm wrap">
	<div class="intro">
		<h1 class="wp-heading-inline"><?php _e( 'Sermon Manager Import/Export', 'church-sermon-manager' ); ?></h1>
	</div>
	<div class="wp-list-table widefat">
		<p><?php _e( 'We have made it easy to backup, migrate or bring sermons from another plugin. Choose the relevant option below to get started.', 'church-sermon-manager' ); ?></p>
		<div id="the-list">
			<div class="plugin-card card-import-sm">
				<div class="plugin-card-top">
					<img src="<?php echo SM_URL; ?>assets/images/import-sm.jpg" class="plugin-icon"
							alt="<?php esc_attr_e( 'Import from file', 'church-sermon-manager' ); ?>">
					<div class="name column-name">
						<h3>
							<?php _e( 'Import from file', 'church-sermon-manager' ); ?>
						</h3>
					</div>
					<div class="action-links">
						<ul class="plugin-action-buttons">
							<li>
								<?php
								$bytes      = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
								$size       = size_format( $bytes );
								$upload_dir = wp_upload_dir();
								if ( ! empty( $upload_dir['error'] ) ) :
									?>
									<div class="error">
										<p>
											<?php esc_html_e( 'Before you can upload your import file, you will need to fix the following error:', 'church-sermon-manager' ); ?>
										</p>
										<p>
											<strong>
												<?php echo $upload_dir['error']; ?>
											</strong>
										</p>
									</div>
								<?php else : ?>
									<form enctype="multipart/form-data" id="sm-import-upload-form" method="post"
											class="wp-upload-form"
											action="<?php echo esc_url( wp_nonce_url( $_SERVER['REQUEST_URI'] . '&doimport=sm', 'sm' ) ); ?>">
										<p>
											<input type="file" id="upload" name="import" size="25"/>
											<input type="hidden" name="action" value="save"/>
											<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>"/>
										</p>
										<input class="button" id="submit" type="submit" name="submit"
												value="<?php esc_attr_e( 'Import from file', 'church-sermon-manager' ); ?>"/>
									</form>
									<span class="button activate-now" id="sm-import-trigger">
										<span>
											<?php _e( 'Import', 'church-sermon-manager' ); ?>
										</span>
										<span class="import-sniper">
											<img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>">
										</span>
									</span>
								<?php endif; ?>
							</li>
							<li><a href="https://github.com/riceguitar/Church-Sermon-Manager#readme" class="" target="_blank" rel="noopener"
										aria-label="<?php esc_attr_e( 'More Details', 'church-sermon-manager' ); ?>">
									<?php _e( 'More Details', 'church-sermon-manager' ); ?>
								</a></li>
						</ul>
					</div>
					<div class="desc column-description">
						<p><?php _e( 'Import sermons from another Sermon Manager installation.', 'church-sermon-manager' ); ?></p>
					</div>
				</div>
			</div>
			<div class="plugin-card card-export-sm">
				<div class="plugin-card-top">
					<img src="<?php echo SM_URL; ?>assets/images/export-sm.jpg" class="plugin-icon"
							alt="<?php esc_attr_e( 'Export to file', 'church-sermon-manager' ); ?>">
					<div class="name column-name">
						<h3>
							<?php _e( 'Export to file', 'church-sermon-manager' ); ?>
						</h3>
					</div>
					<div class="action-links">
						<ul class="plugin-action-buttons">
							<li>
								<a href="<?php echo $_SERVER['REQUEST_URI']; ?>&doimport=exsm"
										class="button activate-now" id="sm-export-content"
										aria-label="<?php esc_attr_e( 'Export to file', 'church-sermon-manager' ); ?>">
									<?php _e( 'Export', 'church-sermon-manager' ); ?>
								</a>
							</li>
							<li><a href="https://github.com/riceguitar/Church-Sermon-Manager#readme" class="" target="_blank" rel="noopener"
										aria-label="<?php esc_attr_e( 'More Details', 'church-sermon-manager' ); ?>">
									<?php _e( 'More Details', 'church-sermon-manager' ); ?></a></li>
						</ul>
					</div>
					<div class="desc column-description">
						<p><?php _e( 'Create an export for the purpose of backup or migration to another website.', 'church-sermon-manager' ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="wp-list-table widefat">
		<h2><?php _e( 'Import From 3rd Party Plugins', 'church-sermon-manager' ); ?></h2>
		<p><?php _e( 'You can import sermons from the following plugins into Sermon Manager', 'church-sermon-manager' ); ?></p>
		<div id="the-list">
			<div class="plugin-card <?php echo SM_Import_SB::is_installed() ? '' : 'not-available'; ?>">
				<h2>Plugin not installed</h2>
				<div class="plugin-card-top">
					<img src="<?php echo SM_URL; ?>assets/images/import-sb.jpg" class="plugin-icon"
							alt="<?php esc_attr_e( 'Sermon Browser', 'church-sermon-manager' ); ?>">
					<div class="name column-name">
						<h3>
							<?php _e( 'Sermon Browser', 'church-sermon-manager' ); ?>
						</h3>
					</div>
					<div class="action-links">
						<ul class="plugin-action-buttons">
							<li><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&doimport=sb"
										class="button activate-now <?php echo SM_Import_SB::is_installed() ? '' : 'disabled'; ?>"
										aria-label="<?php esc_attr_e( 'Import from Sermon Browser', 'church-sermon-manager' ); ?>">
									<?php _e( 'Import', 'church-sermon-manager' ); ?></a>
							</li>
							<li>
								<a href="https://github.com/riceguitar/Church-Sermon-Manager#readme"
										target="_blank"
										aria-label="<?php esc_attr_e( 'More Details', 'church-sermon-manager' ); ?>">
									<?php _e( 'More Details', 'church-sermon-manager' ); ?></a>
							</li>
						</ul>
					</div>
					<div class="desc column-description">
						<p>
							<?php
							// translators: %s Plugin name.
							echo wp_sprintf( __( 'Import your existing %s sermon library into Sermon Manager', 'church-sermon-manager' ), 'Sermon Browser' );
							?>
						</p>
						<p class="import-note">
							<?php
							// translators: %s Documentation URL.
							echo wp_sprintf( __( 'Note: Some restrictions apply. Click %s for more details.', 'church-sermon-manager' ), ' <a href="https://github.com/riceguitar/Church-Sermon-Manager#readme" target="_blank">here</a>' );
							?>
						</p>
					</div>
				</div>
			</div>
			<div class="plugin-card <?php echo SM_Import_SE::is_installed() ? '' : 'not-available'; ?>">
				<h2>Plugin not installed</h2>
				<div class="plugin-card-top">
					<img src="<?php echo SM_URL; ?>assets/images/import-se.jpg" class="plugin-icon"
							alt="<?php esc_attr_e( 'Series Engine', 'church-sermon-manager' ); ?>">
					<div class="name column-name">
						<h3>
							<?php _e( 'Series Engine', 'church-sermon-manager' ); ?>
						</h3>
					</div>
					<div class="action-links">
						<ul class="plugin-action-buttons">
							<li><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&doimport=se"
										class="button activate-now <?php echo SM_Import_SE::is_installed() ? '' : 'disabled'; ?>"
										aria-label="<?php esc_attr_e( 'Import from Series Engine', 'church-sermon-manager' ); ?>">
									<?php _e( 'Import', 'church-sermon-manager' ); ?></a>
							</li>
							<li>
								<a href="https://github.com/riceguitar/Church-Sermon-Manager#readme"
										target="_blank"
										aria-label="<?php esc_attr_e( 'More Details', 'church-sermon-manager' ); ?>">
									<?php _e( 'More Details', 'church-sermon-manager' ); ?></a>
							</li>
						</ul>
					</div>
					<div class="desc column-description">
						<p>
							<?php
							// translators: %s Plugin name.
							echo wp_sprintf( __( 'Import your existing %s sermon library into Sermon Manager', 'church-sermon-manager' ), 'Series Engine' );
							?>
						</p>
						<p class="import-note">
							<?php
							// translators: %s Documentation URL.
							echo wp_sprintf( __( 'Note: Some restrictions apply. Click %s for more details.', 'church-sermon-manager' ), ' <a href="https://github.com/riceguitar/Church-Sermon-Manager#readme" target="_blank">here</a>' );
							?>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<p class="description">
		<?php _e( 'Note: We recommend you create a backup of your current database just in case.', 'church-sermon-manager' ); ?>
	</p>
</div>
