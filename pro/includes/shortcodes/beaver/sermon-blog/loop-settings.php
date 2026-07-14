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
				<span class="fl-builder-settings-title-text-wrap"><?php _e( 'Custom Query', 'fl-builder' ); ?></span>
			</h3>
			<table class="fl-form-table">
				<?php

				// Order.
				FLBuilder::render_settings_field( 'order', array(
					'type'    => 'select',
					'label'   => __( 'Order', 'fl-builder' ),
					'options' => array(
						'DESC' => __( 'Descending', 'fl-builder' ),
						'ASC'  => __( 'Ascending', 'fl-builder' ),
					),
				), $settings );

				// Order by.
				FLBuilder::render_settings_field( 'order_by', array(
					'type'    => 'select',
					'label'   => __( 'Order By', 'fl-builder' ),
					'options' => array(
						'author'         => __( 'Author', 'fl-builder' ),
						'comment_count'  => __( 'Comment Count', 'fl-builder' ),
						'date'           => __( 'Date', 'fl-builder' ),
						'modified'       => __( 'Date Last Modified', 'fl-builder' ),
						'ID'             => __( 'ID', 'fl-builder' ),
						'menu_order'     => __( 'Menu Order', 'fl-builder' ),
						'meta_value'     => __( 'Meta Value (Alphabetical)', 'fl-builder' ),
						'meta_value_num' => __( 'Meta Value (Numeric)', 'fl-builder' ),
						'rand'           => __( 'Random', 'fl-builder' ),
						'title'          => __( 'Title', 'fl-builder' ),
						'post__in'       => __( 'Selection Order', 'fl-builder' ),
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
					'label' => __( 'Meta Key', 'fl-builder' ),
				), $settings );

				// Offset.
				FLBuilder::render_settings_field( 'offset', array(
					'type'    => 'text',
					'label'   => _x( 'Offset', 'How many posts to skip.', 'fl-builder' ),
					'default' => '0',
					'size'    => '4',
					'help'    => __( 'Skip this many posts that match the specified criteria.', 'fl-builder' ),
				), $settings );

				?>
			</table>
		</div>
		<div id="fl-builder-settings-section-filter" class="fl-builder-settings-section">
			<h3 class="fl-builder-settings-title">
				<span class="fl-builder-settings-title-text-wrap"><?php _e( 'Filter', 'fl-builder' ); ?></span>
			</h3>
			<?php foreach ( FLBuilderLoop::post_types() as $slug => $type ) : ?>
				<table class="fl-form-table fl-custom-query-filter fl-custom-query-<?php echo $slug; ?>-filter" <?php echo $slug == $settings->post_type ? 'style="display:table;"' : ''; ?>>
					<?php

					// Posts.
					FLBuilder::render_settings_field( 'posts_' . $slug, array(
						'type'     => 'suggest',
						'action'   => 'fl_as_posts',
						'data'     => $slug,
						'label'    => $type->label,
						'help'     => sprintf( __( 'Enter a list of %1$s.', 'fl-builder' ), $type->label ),
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
							'help'     => sprintf( __( 'Enter a list of %1$s.', 'fl-builder' ), $tax->label ),
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
					'label'    => __( 'Authors', 'fl-builder' ),
					'help'     => __( 'Enter a list of authors usernames.', 'fl-builder' ),
					'matching' => true,
				), $settings );

				?>
			</table>
		</div>
	</div>
<?php

