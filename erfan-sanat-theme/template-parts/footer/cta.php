<?php
/**
 * Pre-footer call to action band.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

// The front page already ends with its own closing call to action.
if ( is_front_page() || ! es_opt( 'footer_cta_enable', true ) ) {
	return;
}

$es_title    = (string) es_opt( 'footer_cta_title', __( 'پروژه‌ای در ذهن دارید؟', 'erfan-sanat' ) );
$es_text     = (string) es_opt( 'footer_cta_text', __( 'کارشناسان عرفان صنعت برای انتخاب، قیمت‌گذاری و اجرای المان‌های نوری در کنار شما هستند.', 'erfan-sanat' ) );
$es_btn_text = (string) es_opt( 'footer_cta_btn_text', __( 'درخواست مشاوره', 'erfan-sanat' ) );
$es_btn_url  = (string) es_opt( 'footer_cta_btn_url', '' );
$es_phone    = (string) es_opt( 'phone_primary', '' );
$es_tel      = es_phone_href( $es_phone );

if ( ! $es_btn_url ) {
	$es_contact = get_page_by_path( 'contact' );
	$es_btn_url = $es_contact ? get_permalink( $es_contact ) : home_url( '/contact/' );
}
?>
<section class="es-prefooter-cta" aria-labelledby="es-prefooter-title">
	<div class="es-container es-prefooter-cta__inner">
		<div class="es-prefooter-cta__content">
			<h2 class="es-prefooter-cta__title" id="es-prefooter-title"><?php echo esc_html( $es_title ); ?></h2>
			<p class="es-prefooter-cta__text"><?php echo esc_html( $es_text ); ?></p>
		</div>

		<div class="es-prefooter-cta__actions">
			<a class="es-btn es-btn--primary es-btn--lg" href="<?php echo esc_url( $es_btn_url ); ?>">
				<span><?php echo esc_html( $es_btn_text ); ?></span>
				<?php es_icon( 'arrow', 'es-icon', 18 ); ?>
			</a>

			<?php if ( $es_tel ) : ?>
				<a class="es-btn es-btn--ghost es-btn--lg" href="<?php echo esc_url( $es_tel ); ?>">
					<?php es_icon( 'phone', 'es-icon', 18 ); ?>
					<span dir="ltr"><?php echo esc_html( $es_phone ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
