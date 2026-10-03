<?php
/**
 * Homepage closing call to action with the contact information cards.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! es_opt( 'home_cta_enable', true ) ) {
	return;
}

$es_title    = (string) es_opt( 'home_cta_title', '' );
$es_text     = (string) es_opt( 'home_cta_text', '' );
$es_btn      = (string) es_opt( 'home_cta_btn_text', '' );
$es_btn_url  = (string) es_opt( 'home_cta_btn_url', '' );
$es_eyebrow  = (string) es_opt( 'home_cta_eyebrow', '' );
$es_phone    = (string) es_opt( 'phone_primary', '' );
$es_email    = (string) es_opt( 'email_primary', '' );
$es_address  = (string) es_opt( 'address_line', '' );
$es_hours    = (string) es_opt( 'working_hours', '' );
$es_tel      = es_phone_href( $es_phone );
$es_whatsapp = es_whatsapp_href( (string) es_opt( 'mobile_primary', '' ) );

if ( ! $es_btn_url ) {
	$es_btn_url = home_url( '/contact/' );
}
?>
<section class="es-section es-section--cta" id="contact">
	<div class="es-container">

		<div class="es-final-cta">
			<span class="es-final-cta__glow" aria-hidden="true"></span>

			<?php if ( $es_eyebrow ) : ?>
				<p class="es-eyebrow es-eyebrow--light"><span class="es-eyebrow__dot" aria-hidden="true"></span><?php echo esc_html( $es_eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $es_title ) : ?>
				<h2 class="es-final-cta__title"><?php echo esc_html( $es_title ); ?></h2>
			<?php endif; ?>

			<?php if ( $es_text ) : ?>
				<p class="es-final-cta__text"><?php echo esc_html( $es_text ); ?></p>
			<?php endif; ?>

			<div class="es-final-cta__actions">
				<?php if ( $es_btn ) : ?>
					<a class="es-btn es-btn--primary es-btn--lg" href="<?php echo esc_url( $es_btn_url ); ?>">
						<span><?php echo esc_html( $es_btn ); ?></span>
						<?php es_icon( 'arrow', 'es-icon', 18 ); ?>
					</a>
				<?php endif; ?>

				<?php if ( $es_whatsapp ) : ?>
					<a class="es-btn es-btn--whatsapp es-btn--lg" href="<?php echo esc_url( $es_whatsapp ); ?>" target="_blank" rel="noopener nofollow">
						<?php es_icon( 'whatsapp', 'es-icon', 18 ); ?>
						<span><?php esc_html_e( 'گفت‌وگو در واتس‌اپ', 'erfan-sanat' ); ?></span>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<ul class="es-info-cards">
			<?php if ( $es_tel ) : ?>
				<li class="es-info-card">
					<span class="es-info-card__icon" aria-hidden="true"><?php es_icon( 'phone', 'es-icon', 24 ); ?></span>
					<span class="es-info-card__label"><?php esc_html_e( 'تلفن فروش و مشاوره', 'erfan-sanat' ); ?></span>
					<a class="es-info-card__value" href="<?php echo esc_url( $es_tel ); ?>" dir="ltr"><?php echo esc_html( $es_phone ); ?></a>
				</li>
			<?php endif; ?>

			<?php if ( $es_email ) : ?>
				<li class="es-info-card">
					<span class="es-info-card__icon" aria-hidden="true"><?php es_icon( 'mail', 'es-icon', 24 ); ?></span>
					<span class="es-info-card__label"><?php esc_html_e( 'ایمیل', 'erfan-sanat' ); ?></span>
					<a class="es-info-card__value" href="<?php echo esc_url( 'mailto:' . antispambot( $es_email ) ); ?>" dir="ltr"><?php echo esc_html( antispambot( $es_email ) ); ?></a>
				</li>
			<?php endif; ?>

			<?php if ( $es_hours ) : ?>
				<li class="es-info-card">
					<span class="es-info-card__icon" aria-hidden="true"><?php es_icon( 'clock', 'es-icon', 24 ); ?></span>
					<span class="es-info-card__label"><?php esc_html_e( 'ساعات کاری', 'erfan-sanat' ); ?></span>
					<span class="es-info-card__value"><?php echo esc_html( $es_hours ); ?></span>
				</li>
			<?php endif; ?>

			<?php if ( $es_address ) : ?>
				<li class="es-info-card">
					<span class="es-info-card__icon" aria-hidden="true"><?php es_icon( 'pin', 'es-icon', 24 ); ?></span>
					<span class="es-info-card__label"><?php esc_html_e( 'نشانی کارخانه', 'erfan-sanat' ); ?></span>
					<span class="es-info-card__value"><?php echo esc_html( $es_address ); ?></span>
				</li>
			<?php endif; ?>
		</ul>
	</div>
</section>
