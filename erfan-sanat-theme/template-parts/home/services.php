<?php
/**
 * Homepage services: interactive list with a shared preview image.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! es_opt( 'home_services_enable', true ) ) {
	return;
}

$es_items = es_opt( 'home_services_items', array() );

if ( ! is_array( $es_items ) || ! $es_items ) {
	return;
}

$es_preview = '';
$es_count   = 0;

foreach ( $es_items as $es_probe ) {
	if ( empty( $es_probe['title'] ) ) {
		continue;
	}

	$es_count++;

	if ( ! $es_preview && ! empty( $es_probe['image'] ) ) {
		$es_preview = es_image_url( (int) $es_probe['image'], 'es-card-wide' );
	}
}

if ( ! $es_count ) {
	return;
}

if ( ! $es_preview ) {
	$es_preview = ES_THEME_URI . 'assets/images/services-preview.svg';
}

$es_first = true;
?>
<section class="es-section es-section--services" id="services">
	<div class="es-container">

		<?php
		es_section_header(
			array(
				'eyebrow' => (string) es_opt( 'home_services_eyebrow', '' ),
				'title'   => (string) es_opt( 'home_services_title', '' ),
				'text'    => (string) es_opt( 'home_services_text', '' ),
				'align'   => 'start',
			)
		);
		?>

		<div class="es-services" data-es-services>
			<div class="es-services__list">
				<?php
				$es_index = 0;

				foreach ( $es_items as $es_item ) :
					if ( empty( $es_item['title'] ) ) {
						continue;
					}

					$es_index++;
					$es_image = ! empty( $es_item['image'] ) ? es_image_url( (int) $es_item['image'], 'es-card-wide' ) : '';
					?>
					<article class="es-service<?php echo $es_first ? ' is-active' : ''; ?>" data-es-service>
						<button
							type="button"
							class="es-service__head"
							data-es-service-toggle
							aria-expanded="<?php echo $es_first ? 'true' : 'false'; ?>"
						>
							<span class="es-service__index" aria-hidden="true"><?php echo esc_html( es_index_label( $es_index ) ); ?></span>
							<span class="es-service__icon" aria-hidden="true"><?php echo es_get_icon( isset( $es_item['icon'] ) ? $es_item['icon'] : 'spark', 'es-icon', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="es-service__title"><?php echo esc_html( $es_item['title'] ); ?></span>
							<span class="es-service__chevron" aria-hidden="true"><?php es_icon( 'chevron', 'es-icon', 18 ); ?></span>
						</button>

						<div class="es-service__body" <?php echo $es_first ? '' : 'hidden'; ?> data-image="<?php echo esc_url( $es_image ); ?>">
							<?php if ( ! empty( $es_item['desc'] ) ) : ?>
								<p class="es-service__text"><?php echo esc_html( $es_item['desc'] ); ?></p>
							<?php endif; ?>

							<?php if ( $es_image ) : ?>
								<img class="es-service__image es-service__image--inline" src="<?php echo esc_url( $es_image ); ?>" alt="" width="720" height="420" loading="lazy">
							<?php endif; ?>
						</div>
					</article>
					<?php
					$es_first = false;
				endforeach;
				?>
			</div>

			<div class="es-services__preview">
				<img
					class="es-services__preview-image"
					src="<?php echo esc_url( $es_preview ); ?>"
					alt="<?php esc_attr_e( 'نمونهٔ اجرای خدمات نورپردازی', 'erfan-sanat' ); ?>"
					width="880"
					height="1100"
					loading="lazy"
					data-es-service-preview
				>
				<span class="es-services__counter" aria-hidden="true">
					<span data-es-service-current><?php echo esc_html( es_index_label( 1 ) ); ?></span>
					<span class="es-services__counter-sep">/</span>
					<span><?php echo esc_html( es_index_label( $es_count ) ); ?></span>
				</span>
			</div>
		</div>
	</div>
</section>
