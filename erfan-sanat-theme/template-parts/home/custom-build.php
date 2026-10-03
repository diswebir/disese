<?php
/**
 * Homepage custom build call to action.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! es_opt( 'home_custom_enable', true ) ) {
	return;
}

$es_title    = (string) es_opt( 'home_custom_title', '' );
$es_text     = (string) es_opt( 'home_custom_text', '' );
$es_btn_text = (string) es_opt( 'home_custom_btn_text', '' );
$es_btn_url  = (string) es_opt( 'home_custom_btn_url', '' );
$es_image_id = (int) es_opt( 'home_custom_image', 0 );

if ( ! $es_btn_url ) {
	$es_btn_url = home_url( '/contact/' );
}
?>
<section class="es-band es-band--custom" id="custom-build">
	<div class="es-container es-band__inner">

		<div class="es-band__content">
			<span class="es-band__icon" aria-hidden="true"><?php es_icon( 'tools', 'es-icon', 28 ); ?></span>

			<?php if ( $es_title ) : ?>
				<h2 class="es-band__title"><?php echo esc_html( $es_title ); ?></h2>
			<?php endif; ?>

			<?php if ( $es_text ) : ?>
				<p class="es-band__text"><?php echo esc_html( $es_text ); ?></p>
			<?php endif; ?>

			<?php if ( $es_btn_text ) : ?>
				<a class="es-btn es-btn--primary es-btn--lg" href="<?php echo esc_url( $es_btn_url ); ?>">
					<span><?php echo esc_html( $es_btn_text ); ?></span>
					<?php es_icon( 'arrow', 'es-icon', 18 ); ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="es-band__media">
			<?php if ( $es_image_id ) : ?>
				<?php
				echo wp_get_attachment_image(
					$es_image_id,
					'es-card-wide',
					false,
					array( 'class' => 'es-band__image', 'loading' => 'lazy' )
				);
				?>
			<?php else : ?>
				<img class="es-band__image" src="<?php echo esc_url( ES_THEME_URI . 'assets/images/custom-build-element.svg' ); ?>" alt="<?php esc_attr_e( 'طراحی و ساخت المان نوری سفارشی', 'erfan-sanat' ); ?>" width="800" height="600" loading="lazy">
			<?php endif; ?>
		</div>
	</div>
</section>
