<?php
/**
 * Product card used by the front page, the shop archive and the 404 page.
 *
 * The price block and the call to action follow the purchasing model of the
 * product (online cart, phone inquiry or official tender).
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_product_id = get_the_ID();
$es_product    = es_woocommerce_active() && function_exists( 'wc_get_product' ) ? wc_get_product( $es_product_id ) : null;
$es_model      = es_woocommerce_active() ? es_product_purchase_model( $es_product_id ) : 'phone_inquiry';
$es_badge      = (string) es_meta( '_es_custom_price_badge', $es_product_id, '' );
$es_cats       = get_the_terms( $es_product_id, 'product_cat' );
$es_chips      = es_woocommerce_active() ? es_product_card_chips( $es_product_id ) : array();
?>
<article <?php post_class( 'es-card es-card--product es-card--model-' . $es_model ); ?>>

	<a class="es-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php es_post_thumbnail( $es_product_id, 'es-card-wide', 'es-card__image' ); ?>

		<span class="es-card__flag es-card__flag--<?php echo esc_attr( $es_model ); ?>">
			<?php
			if ( 'online_cart' === $es_model ) {
				esc_html_e( 'قابل خرید', 'erfan-sanat' );
			} elseif ( 'official_tender' === $es_model ) {
				esc_html_e( 'مناقصه', 'erfan-sanat' );
			} else {
				esc_html_e( 'تماس بگیرید', 'erfan-sanat' );
			}
			?>
		</span>
	</a>

	<div class="es-card__body">
		<?php if ( $es_cats && ! is_wp_error( $es_cats ) ) : ?>
			<p class="es-card__eyebrow">
				<?php es_icon( 'folder', 'es-icon', 14 ); ?>
				<span><?php echo esc_html( $es_cats[0]->name ); ?></span>
			</p>
		<?php endif; ?>

		<h3 class="es-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<?php if ( $es_chips ) : ?>
			<ul class="es-chips">
				<?php foreach ( $es_chips as $es_chip ) : ?>
					<li class="es-chips__item"><?php echo esc_html( $es_chip ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<div class="es-card__foot">
			<span class="es-card__price">
				<?php if ( $es_badge ) : ?>
					<span class="es-price-badge"><?php echo esc_html( $es_badge ); ?></span>
				<?php elseif ( $es_product ) : ?>
					<?php echo wp_kses_post( $es_product->get_price_html() ); ?>
				<?php else : ?>
					<span class="es-price-badge"><?php esc_html_e( 'تماس بگیرید', 'erfan-sanat' ); ?></span>
				<?php endif; ?>
			</span>

			<?php if ( 'online_cart' === $es_model && $es_product && $es_product->is_type( 'simple' ) && $es_product->is_purchasable() ) : ?>
				<a
					class="es-card__action es-btn es-btn--primary es-btn--sm add_to_cart_button ajax_add_to_cart"
					href="<?php echo esc_url( $es_product->add_to_cart_url() ); ?>"
					data-quantity="1"
					data-product_id="<?php echo esc_attr( (string) $es_product_id ); ?>"
					data-product_sku="<?php echo esc_attr( (string) $es_product->get_sku() ); ?>"
					aria-label="<?php echo esc_attr( $es_product->add_to_cart_description() ); ?>"
					rel="nofollow"
				>
					<?php es_icon( 'cart', 'es-icon', 16 ); ?>
					<span><?php echo esc_html( $es_product->add_to_cart_text() ); ?></span>
				</a>
			<?php elseif ( 'online_cart' === $es_model ) : ?>
				<a class="es-card__action es-btn es-btn--outline es-btn--sm" href="<?php the_permalink(); ?>">
					<span><?php esc_html_e( 'مشاهدهٔ محصول', 'erfan-sanat' ); ?></span>
				</a>
			<?php else : ?>
				<?php
				$es_phone = es_woocommerce_active() ? es_product_inquiry_phone( $es_product_id ) : (string) es_opt( 'phone_primary', '' );
				$es_tel   = es_phone_href( $es_phone );
				?>
				<a class="es-card__action es-btn es-btn--outline es-btn--sm" href="<?php echo esc_url( $es_tel ? $es_tel : get_permalink() ); ?>">
					<?php es_icon( 'phone', 'es-icon', 16 ); ?>
					<span><?php esc_html_e( 'استعلام قیمت', 'erfan-sanat' ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</article>
