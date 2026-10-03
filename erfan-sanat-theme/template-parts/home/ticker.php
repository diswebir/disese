<?php
/**
 * Cities marquee ("we have lit up these cities").
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! es_opt( 'home_ticker_enable', true ) ) {
	return;
}

$es_label = (string) es_opt( 'home_ticker_label', '' );
$es_title = (string) es_opt( 'home_ticker_title', '' );
$es_items = es_opt( 'home_ticker_items', array() );

if ( ! is_array( $es_items ) || ! $es_items ) {
	return;
}
?>
<section class="es-ticker" aria-label="<?php esc_attr_e( 'شهرهایی که در آن‌ها پروژه اجرا کرده‌ایم', 'erfan-sanat' ); ?>">
	<div class="es-container es-ticker__head">
		<?php if ( $es_label ) : ?>
			<p class="es-ticker__label"><?php echo esc_html( $es_label ); ?></p>
		<?php endif; ?>

		<?php if ( $es_title ) : ?>
			<p class="es-ticker__title"><?php echo esc_html( $es_title ); ?></p>
		<?php endif; ?>
	</div>

	<div class="es-ticker__viewport">
		<div class="es-ticker__track">
			<?php
			// Duplicated once: the CSS animation loops seamlessly and the copy is hidden from AT.
			for ( $es_pass = 0; $es_pass < 2; $es_pass++ ) :
				?>
				<ul class="es-ticker__list" <?php echo 1 === $es_pass ? 'aria-hidden="true"' : ''; ?>>
					<?php foreach ( $es_items as $es_item ) : ?>
						<?php if ( empty( $es_item['name'] ) ) { continue; } ?>
						<li class="es-ticker__item">
							<span class="es-ticker__name"><?php echo esc_html( $es_item['name'] ); ?></span>
							<span class="es-ticker__dot" aria-hidden="true"></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endfor; ?>
		</div>
	</div>
</section>
