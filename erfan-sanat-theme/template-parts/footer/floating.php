<?php
/**
 * Floating actions: WhatsApp / phone quick contact.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_phone    = (string) es_opt( 'phone_sales', '' );
$es_whatsapp = (string) es_opt( 'mobile_primary', '' );
$es_wa_href  = es_whatsapp_href( $es_whatsapp );
$es_tel      = es_phone_href( $es_phone ? $es_phone : (string) es_opt( 'phone_primary', '' ) );
?>
<div class="es-float" data-es-float>
	<?php if ( $es_wa_href && es_opt( 'enable_whatsapp_float', true ) ) : ?>
		<a class="es-float__btn es-float__btn--whatsapp" href="<?php echo esc_url( $es_wa_href ); ?>" target="_blank" rel="noopener nofollow" aria-label="<?php esc_attr_e( 'گفت‌وگو در واتس‌اپ', 'erfan-sanat' ); ?>">
			<?php es_icon( 'whatsapp', 'es-icon', 24 ); ?>
			<span class="es-float__tooltip"><?php esc_html_e( 'گفت‌وگو در واتس‌اپ', 'erfan-sanat' ); ?></span>
		</a>
	<?php endif; ?>

	<?php if ( $es_tel ) : ?>
		<a class="es-float__btn es-float__btn--phone" href="<?php echo esc_url( $es_tel ); ?>" aria-label="<?php esc_attr_e( 'تماس تلفنی', 'erfan-sanat' ); ?>">
			<?php es_icon( 'phone', 'es-icon', 24 ); ?>
			<span class="es-float__tooltip"><?php esc_html_e( 'تماس تلفنی با فروش', 'erfan-sanat' ); ?></span>
		</a>
	<?php endif; ?>
</div>
