<?php
/**
 * Off-canvas mobile navigation.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_phone   = (string) es_opt( 'phone_primary', '' );
$es_tel     = es_phone_href( $es_phone );
$es_socials = es_get_social_items();
$es_footer  = (string) es_opt( 'nav_mobile_footer_text', '' );
?>
<div class="es-drawer" id="es-mobile-menu" data-es-drawer hidden>
	<div class="es-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'منوی موبایل', 'erfan-sanat' ); ?>">

		<div class="es-drawer__head">
			<span class="es-drawer__title"><?php esc_html_e( 'منو', 'erfan-sanat' ); ?></span>
			<button type="button" class="es-drawer__close" data-es-menu-close aria-label="<?php esc_attr_e( 'بستن منو', 'erfan-sanat' ); ?>">
				<?php es_icon( 'close', 'es-icon', 22 ); ?>
			</button>
		</div>

		<div class="es-drawer__body">
			<nav class="es-drawer__nav" aria-label="<?php esc_attr_e( 'منوی موبایل', 'erfan-sanat' ); ?>">
				<?php
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'menu_class'     => 'es-drawer__list',
							'container'      => false,
							'depth'          => 3,
							'menu_id'        => 'mobile-menu',
						)
					);
				} else {
					es_nav_menu_fallback( array( 'menu_class' => 'es-drawer__list' ) );
				}
				?>
			</nav>

			<div class="es-drawer__contact">
				<?php if ( $es_tel ) : ?>
					<a class="es-drawer__phone" href="<?php echo esc_url( $es_tel ); ?>">
						<?php es_icon( 'phone', 'es-icon', 18 ); ?>
						<span dir="ltr"><?php echo esc_html( $es_phone ); ?></span>
					</a>
				<?php endif; ?>

				<?php if ( es_woocommerce_active() ) : ?>
					<div class="es-drawer__shop">
						<?php es_header_cart_link(); ?>
						<?php es_header_account_link(); ?>
					</div>
				<?php endif; ?>
			</div>

			<?php
			$es_cta_text = (string) es_opt( 'header_cta_text', '' );

			if ( $es_cta_text ) :
				$es_cta_url = (string) es_opt( 'header_cta_url', '' );
				?>
				<a class="es-btn es-btn--primary es-btn--block" href="<?php echo esc_url( $es_cta_url ? $es_cta_url : home_url( '/contact/' ) ); ?>">
					<?php echo esc_html( $es_cta_text ); ?>
				</a>
			<?php endif; ?>

			<?php if ( es_opt( 'social_show_header', true ) && $es_socials ) : ?>
				<ul class="es-socials">
					<?php foreach ( $es_socials as $es_social ) : ?>
						<li>
							<a href="<?php echo esc_url( $es_social['url'] ); ?>" target="_blank" rel="noopener nofollow" aria-label="<?php echo esc_attr( $es_social['label'] ); ?>">
								<?php echo es_get_social_icon( (string) $es_social['network'], 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php if ( $es_footer ) : ?>
			<p class="es-drawer__footer-text"><?php echo esc_html( $es_footer ); ?></p>
		<?php endif; ?>
	</div>

	<button type="button" class="es-drawer__backdrop" data-es-menu-close tabindex="-1" aria-hidden="true"></button>
</div>
