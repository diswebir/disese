<?php
/**
 * Widget area renderer (used by the blog and shop layouts).
 *
 * @param array $args name, force.
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_name = 'blog';

if ( isset( $args['name'] ) ) {
	$es_name = (string) $args['name'];
} elseif ( isset( $name ) && is_string( $name ) && '' !== $name ) {
	// Older WordPress versions expose the template-part slug suffix directly.
	$es_name = (string) $name;
}

$es_force = ! empty( $args['force'] );

if ( ! $es_force && ! es_sidebar_visible( $es_name ) ) {
	return;
}

$es_sidebar_class = 'es-sidebar es-sidebar--' . ( 'shop' === $es_name ? 'shop' : 'blog' );

$es_sidebar_id = 'shop' === $es_name ? 'es-shop-sidebar' : 'es-blog-sidebar';

if ( 'project' === $es_name ) {
	// The projects archive sidebar is a filter panel, not a widget area.
	?>
	<aside class="<?php echo esc_attr( $es_sidebar_class ); ?>" role="complementary" aria-label="<?php esc_attr_e( 'فیلتر پروژه‌ها', 'erfan-sanat' ); ?>">
		<?php
		get_template_part(
			'template-parts/project/filters',
			null,
			array(
				'base' => (string) get_post_type_archive_link( 'project' ),
			)
		);
		?>
	</aside>
	<?php
	return;
}

if ( ! is_active_sidebar( $es_sidebar_id ) ) {
	// Fall back to useful defaults so the column is never empty on a fresh site.
	?>
	<aside class="<?php echo esc_attr( $es_sidebar_class ); ?>" role="complementary">
		<section class="es-widget es-widget--search">
			<h2 class="es-widget__title"><?php esc_html_e( 'جست‌وجو', 'erfan-sanat' ); ?></h2>
			<?php get_search_form(); ?>
		</section>

		<?php
		if ( 'shop' === $es_name && es_woocommerce_active() ) {
			$es_cats = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => false,
					'parent'     => 0,
					'number'     => 8,
				)
			);

			if ( ! is_wp_error( $es_cats ) && $es_cats ) {
				echo '<section class="es-widget"><h2 class="es-widget__title">' . esc_html__( 'دسته‌بندی محصولات', 'erfan-sanat' ) . '</h2><ul class="es-widget__list">';
				foreach ( $es_cats as $es_cat ) {
					printf(
						'<li><a href="%1$s">%2$s<span class="es-count">%3$s</span></a></li>',
						esc_url( (string) get_term_link( $es_cat ) ),
						esc_html( $es_cat->name ),
						esc_html( es_num( number_format_i18n( $es_cat->count ) ) )
					);
				}
				echo '</ul></section>';
			}
		} else {
			$es_recent = get_posts(
				array(
					'post_type'      => 'post',
					'posts_per_page' => 5,
					'no_found_rows'  => true,
				)
			);

			if ( $es_recent ) {
				echo '<section class="es-widget"><h2 class="es-widget__title">' . esc_html__( 'تازه‌ترین مقالات', 'erfan-sanat' ) . '</h2><ul class="es-widget__list">';
				foreach ( $es_recent as $es_post_item ) {
					printf(
						'<li><a href="%1$s">%2$s</a></li>',
						esc_url( (string) get_permalink( $es_post_item ) ),
						esc_html( get_the_title( $es_post_item ) )
					);
				}
				echo '</ul></section>';
			}

			$es_tags = get_tags( array( 'number' => 12, 'orderby' => 'count', 'order' => 'DESC' ) );

			if ( ! is_wp_error( $es_tags ) && $es_tags ) {
				echo '<section class="es-widget"><h2 class="es-widget__title">' . esc_html__( 'برچسب‌ها', 'erfan-sanat' ) . '</h2><div class="es-pills">';
				foreach ( $es_tags as $es_tag ) {
					printf(
						'<a class="es-pill" href="%1$s">%2$s</a>',
						esc_url( (string) get_term_link( $es_tag ) ),
						esc_html( $es_tag->name )
					);
				}
				echo '</div></section>';
			}
		}
		?>
	</aside>
	<?php
	return;
}
?>
<aside class="<?php echo esc_attr( $es_sidebar_class ); ?>" role="complementary" aria-label="<?php esc_attr_e( 'ستون کناری', 'erfan-sanat' ); ?>">
	<?php dynamic_sidebar( $es_sidebar_id ); ?>
</aside>
