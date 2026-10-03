<?php
/**
 * Product archive (shop, product categories, product tags, searches).
 *
 * Overridden to add the theme's archive hero and layout while keeping every
 * WooCommerce hook in place, so third-party extensions and the native loop
 * continue to work unchanged.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 */
do_action( 'woocommerce_before_main_content' );

if ( woocommerce_product_loop() ) {

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked es_woocommerce_archive_heading - 5 (theme heading)
	 */
	do_action( 'woocommerce_before_shop_loop' );

	echo '<div class="es-woo__layout' . ( es_sidebar_visible( 'shop' ) ? ' has-sidebar' : '' ) . '">';
	echo '<div class="es-woo__main">';

	woocommerce_product_loop_start();

	if ( wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook: woocommerce_shop_loop.
			 */
			do_action( 'woocommerce_shop_loop' );

			wc_get_template_part( 'content', 'product' );
		}
	}

	woocommerce_product_loop_end();

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );

	echo '</div><!-- .es-woo__main -->';

	if ( es_sidebar_visible( 'shop' ) ) {
		get_template_part( 'template-parts/global/sidebar', 'shop', array( 'name' => 'shop' ) );
	}

	echo '</div><!-- .es-woo__layout -->';
} else {
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );
}

/**
 * Hook: woocommerce_after_main_content.
 */
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
