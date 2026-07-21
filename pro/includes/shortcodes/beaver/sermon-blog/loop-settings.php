<?php
/**
 * Loop settings file for Sermon Blog.
 *
 * @since   1.0.0-beta.2
 *
 * @package SMP\Shortcodes\Beaver
 */

defined( 'ABSPATH' ) or exit;

// Default Settings.
$defaults = array(
	'data_source'  => 'custom_query',
	'post_type'    => 'wpfc_sermon',
	'orderby'      => 'meta_value_num',
	'meta_compare' => '<=',
	'meta_value'   => time(),
	'meta_key'     => 'sermon_date',
	'order'        => 'DESC',
	'offset'       => 0,
	'users'        => '',
);

$settings = (object) array_merge( $defaults, (array) $settings );

?>
	<div class="fl-custom-query fl-loop-data-source" data-source="custom_query">
		<div id="fl-builder-settings-section-general" class="fl-builder-settings-section">
			<h3 class="fl-builder-settings-title">
				<span class="fl-builder-settings-title-text-wrap"><?php esc_html_e( 'Custom Query', 'church-sermon-manager' ); ?></span>
			</h3>
			<table class="fl-form-table">
				<?php

				// Order.
				FLBuilder::render_settings_field( 'order', array(
					'type'    => 'select',
					'label'   => __( 'Order', 'church-sermon-manager' ),
					'options' => array(
						'DESC' => __( 'Descending', 'church-sermon-manager' ),
						'ASC'  => __( 'Ascending', 'church-sermon-manager' ),
					),
				), $settings );

				// Order by.
				FLBuilder::render_settings_field( 'order_by', array(
					'type'    => 'select',
					'label'   => __( 'Order By', 'church-sermon-manager' ),
					'options' => array(
						'author'         => __( 'Author', 'church-sermon-manager' ),
						'comment_count'  => __( 'Comment Count', 'church-sermon-manager' ),
						'date'           => __( 'Date', 'church-sermon-manager' ),
						'modified'       => __( 'Date Last Modified', 'church-sermon-manager' ),
						'ID'             => __( 'ID', 'church-sermon-manager' ),
						'menu_order'     => __( 'Menu Order', 'church-sermon-manager' ),
						'meta_value'     => __( 'Meta Value (Alphabetical)', 'church-sermon-manager' ),
						'meta_value_num' => __( 'Meta Value (Numeric)', 'church-sermon-manager' ),
						'rand'           => __( 'Random', 'church-sermon-manager' ),
						'title'          => __( 'Title', 'church-sermon-manager' ),
						'post__in'       => __( 'Selection Order', 'church-sermon-manager' ),
					),
					'toggle'  => array(
						'meta_value'     => array(
							'fields' => array( 'order_by_meta_key' ),
						),
						'meta_value_num' => array(
							'fields' => array( 'order_by_meta_key' ),
						),
					),
				), $settings );

				// Meta Key.
				FLBuilder::render_settings_field( 'order_by_meta_key', array(
					'type'  => 'text',
					'label' => __( 'Meta Key', 'church-sermon-manager' ),
				), $settings );

				// Offset.
				FLBuilder::render_settings_field( 'offset', array(
					'type'    => 'text',
					'label'   => _x( 'Offset', 'How many posts to skip.', 'church-sermon-manager' ),
					'default' => '0',
					'size'    => '4',
					'help'    => __( 'Skip this many posts that match the specified criteria.', 'church-sermon-manager' ),
				), $settings );

				?>
			</table>
		</div>
		<div id="fl-builder-settings-section-filter" class="fl-builder-settings-section">
			<h3 class="fl-builder-settings-title">
				<span class="fl-builder-settings-title-text-wrap"><?php esc_html_e( 'Filter', 'church-sermon-manager' ); ?></span>
			</h3>
			<?php foreach ( FLBuilderLoop::post_types() as $slug => $type ) : ?>
				<table class="fl-form-table fl-custom-query-filter fl-custom-query-<?php echo esc_attr( $slug ); ?>-filter" <?php echo $slug == $settings->post_type ? 'style="display:table;"' : ''; ?>>
					<?php

					// Posts.
					FLBuilder::render_settings_field( 'posts_' . $slug, array(
						'type'     => 'suggest',
						'action'   => 'fl_as_posts',
						'data'     => $slug,
						'label'    => $type->label,
						/* translators: %1$s: post type or taxonomy label. */
						'help'     => sprintf( __( 'Enter a list of %1$s.', 'church-sermon-manager' ), $type->label ),
						'matching' => true,
					), $settings );

					// Taxonomies.
					$taxonomies = FLBuilderLoop::taxonomies( $slug );

					foreach ( $taxonomies as $tax_slug => $tax ) {
						FLBuilder::render_settings_field( 'tax_' . $slug . '_' . $tax_slug, array(
							'type'     => 'suggest',
							'action'   => 'fl_as_terms',
							'data'     => $tax_slug,
							'label'    => $tax->label,
							/* translators: %1$s: post type or taxonomy label. */
							'help'     => sprintf( __( 'Enter a list of %1$s.', 'church-sermon-manager' ), $tax->label ),
							'matching' => true,
						), $settings );
					}

					?>
				</table>
			<?php endforeach; ?>
			<table class="fl-form-table">
				<?php

				// Author.
				FLBuilder::render_settings_field( 'users', array(
					'type'     => 'suggest',
					'action'   => 'fl_as_users',
					'label'    => __( 'Authors', 'church-sermon-manager' ),
					'help'     => __( 'Enter a list of authors usernames.', 'church-sermon-manager' ),
					'matching' => true,
				), $settings );

				?>
			</table>
		</div>
	</div>
<?php

