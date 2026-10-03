<?php
/**
 * Product category archive.
 *
 * Product categories are rendered by the shared archive template, with the
 * category description and the child category navigation added on top.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );
?>
<div class="es-woo__term-head">
	<?php
	$es_term = get_queried_object();

	if ( $es_term && ! is_wp_error( $es_term ) && taxonomy_exists( 'product_cat' ) ) {
		$es_children = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => $es_term->term_id,
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $es_children ) && $es_children ) {
			echo '<nav class="es-cat-nav es-cat-nav--children" aria-label="' . esc_attr__( 'زیر‌دسته‌ها', 'erfan-sanat' ) . '"><ul class="es-cat-nav__list">';

			foreach ( $es_children as $es_child ) {
				printf(
					'<li><a class="es-cat-nav__item" href="%1$s"><span class="es-cat-nav__name">%2$s</span><span class="es-cat-nav__count">%3$s</span></a></li>',
					esc_url( (string) get_term_link( $es_child ) ),
					esc_html( $es_child->name ),
					esc_html( es_num( number_format_i18n( $es_child->count ) ) )
				);
			}

			echo '</ul></nav>';
		}
	}
	?>
</div>
<?php

if ( woocommerce_product_loop() ) {
	do_action( 'woocommerce_before_shop_loop' );

	echo '<div class="es-woo__layout' . ( es_opt( 'product_archive_sidebar', true ) && is_active_sidebar( 'es-shop-sidebar' ) ? ' has-sidebar' : '' ) . '">';
	echo '<div class="es-woo__main">';

	woocommerce_product_loop_start();

	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();
			do_action( 'woocommerce_shop_loop' );
			wc_get_template_part( 'content', 'product' );
		}
	}

	woocommerce_product_loop_end();
	do_action( 'woocommerce_after_shop_loop' );

	echo '</div>';

	if ( es_opt( 'product_archive_sidebar', true ) ) {
		echo '<aside class="es-sidebar es-sidebar--shop">';
		get_template_part( 'template-parts/global/sidebar', 'shop' );
		echo '</aside>';
	}

	echo '</div>';
} else {
	do_action( 'woocommerce_no_products_found' );
}

do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
