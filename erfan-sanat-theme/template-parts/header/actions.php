<?php
/**
 * Header action buttons: search toggle, cart, account, phone, primary CTA and
 * the mobile menu button.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_actions          = array();
$es_actions['search'] = (bool) es_opt( 'enable_search_in_header', true );
$es_actions['cart']   = es_woocommerce_active() && (bool) es_opt( 'header_show_cart', true );
$es_actions['login']  = es_woocommerce_active() && (bool) es_opt( 'header_show_account', true );
?>
<div class="es-header__actions">
	<?php if ( $es_actions['search'] ) : ?>
		<button type="button" class="es-header__icon-btn" data-es-search-open aria-label="<?php esc_attr_e( 'جست‌وجو', 'erfan-sanat' ); ?>" aria-expanded="false">
			<?php es_icon( 'search', 'es-icon', 22 ); ?>
		</button>
	<?php endif; ?>

	<?php if ( es_opt( 'header_show_phone', true ) ) : ?>
		<?php
		$es_phone = (string) es_opt( 'phone_primary', '' );
		$es_tel   = es_phone_href( $es_phone );

		if ( $es_tel ) :
			?>
			<a class="es-header__phone" href="<?php echo esc_url( $es_tel ); ?>">
				<span class="es-header__phone-icon"><?php es_icon( 'phone', 'es-icon', 20 ); ?></span>
				<span class="es-header__phone-text">
					<span class="es-header__phone-label"><?php esc_html_e( 'تماس با کارشناسان', 'erfan-sanat' ); ?></span>
					<span class="es-header__phone-number" dir="ltr"><?php echo esc_html( $es_phone ); ?></span>
				</span>
			</a>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( $es_actions['cart'] ) : ?>
		<?php es_header_cart_link(); ?>
	<?php endif; ?>

	<?php if ( $es_actions['login'] ) : ?>
		<?php es_header_account_link(); ?>
	<?php endif; ?>

	<button
		type="button"
		class="es-burger"
		data-es-menu-open
		aria-label="<?php esc_attr_e( 'باز کردن منو', 'erfan-sanat' ); ?>"
		aria-expanded="false"
		aria-controls="es-mobile-menu"
	>
		<span class="es-burger__bar" aria-hidden="true"></span>
		<span class="es-burger__bar" aria-hidden="true"></span>
		<span class="es-burger__bar" aria-hidden="true"></span>
	</button>
</div>
