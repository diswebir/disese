<?php
/**
 * Product loop item.
 *
 * The markup itself lives in template-parts/cards/product-card.php so the
 * homepage, the shop archive and the 404 page always render identical cards.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'es-woo__item', $product ); ?>>
	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 */
	do_action( 'woocommerce_before_shop_loop_item' );

	get_template_part( 'template-parts/cards/product-card' );

	/**
	 * Hook: woocommerce_after_shop_loop_item.
	 */
	do_action( 'woocommerce_after_shop_loop_item' );
	?>
</li>
