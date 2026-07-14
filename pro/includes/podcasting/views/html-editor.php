<?php
/**
 * The editor page for Podcasting, overrides whole HTML
 *
 * @since   1.0.0-beta.5
 * @package SMP\Podcasting\Editor
 */

defined( 'ABSPATH' ) or die;

$post_id          = isset( $_GET['post'] ) ? intval( $_GET['post'] ) : 0;
$show_notice      = get_option( 'sm_podcasting_saved_notice', 0 );
$all_settings     = apply_filters( 'sm_pro_get_podcasting_settings', array() );
$post             = get_post( $post_id );
$podcast_settings = $post ? get_post_meta( $post->ID, 'sm_template_settings', true ) : array();

$values = array();

foreach ( $all_settings as $setting_data => $setting ) {
	if ( ! isset( $setting['id'] ) ) {
		continue;
	}

	$setting += array(
		'default' => '',
	);

	$values[ $setting['id'] ] = \SMP\Podcasting\Settings::get_setting( $post_id, $setting['id'], $setting['default'] );
}

wp_enqueue_media();

require_once ABSPATH . 'wp-admin/admin-header.php';
?>

<div class="wrap sm wpfc-podcasting">
	<div class="intro">
		<h1 class="wp-heading-inline">Sermon Manager Settings</h1>
	</div>

	<!-- Notice -->
	<?php if ( $show_notice ) : ?>
		<div class="notice notice-success">
			<p>Podcast successfully saved.</p>
		</div>
		<?php update_option( 'sm_templating_saved_notice', 0 ); ?>
	<?php endif; ?>

	<div class="settings-main">
		<div class="settings-content">
			<form name="post"
					action="post.php?post=<?php echo $post_id; ?>&action=edit"
					method="post">
				<div class="inside">
					<?php \SM_Admin_Settings::output_fields( $all_settings, $values ); ?>

					<p>The feed URL:
						<code><?php echo $post_id ? ( site_url( '/' ) . '?feed=rss2&post_type=wpfc_sermon&id=' . $post_id ) : __( 'Please save the feed to get the URL.', 'sermon-manager' ); ?></code>
					</p>
					<p class="submit">
						<?php if ( empty( $GLOBALS['hide_save_button'] ) ) : ?>
							<input name="save_podcast" class="button-primary sm-save-button" type="submit"
									value="<?php esc_attr_e( 'Save changes', 'sermon-manager' ); ?>"/>
						<?php endif; ?>
						<input type="hidden" name="post_id"
								value="<?php echo $post_id; ?>">
						<?php wp_nonce_field( 'sm-settings-podcasting' ); ?>
					</p>
				</div>
			</form>
		</div>
	</div>
	<style>
		.forminp.forminp-text > input, .forminp.forminp-email > input {
			min-width: 400px;
		}
	</style>
	<script>
        var frame;

        function uploadImage(event) {
            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: 'Select or Upload Cover Image',
                button: {
                    text: 'Use this image',
                },
                library: {
                    type: ['image'],
                },
                multiple: false,
            });

            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();

                jQuery(event.target).prev().val(attachment.url);
            });

            frame.open();
        }

        jQuery('#upload_itunes_cover_image').on('click', uploadImage);
	</script>
</div>
