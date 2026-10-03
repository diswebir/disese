<?php
/**
 * Footer bottom: trust badges, copyright and legal menu.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_badges    = es_opt( 'footer_badges', array() );
$es_copyright = (string) es_opt( 'footer_copyright', '' );

if ( ! $es_copyright ) {
	$es_copyright = sprintf(
		/* translators: 1: year, 2: site name */
		__( '© %1$s %2$s — همهٔ حقوق محفوظ است.', 'erfan-sanat' ),
		esc_html( es_num( wp_date( 'Y' ) ) ),
		esc_html( (string) es_opt( 'brand_name_fa', get_bloginfo( 'name' ) ) )
	);
}
?>
<div class="es-footer__bottom">
	<div class="es-container es-footer__bottom-inner">

		<?php if ( is_array( $es_badges ) && $es_badges ) : ?>
			<ul class="es-badges">
				<?php foreach ( $es_badges as $es_badge ) : ?>
					<?php if ( empty( $es_badge['title'] ) ) { continue; } ?>
					<li class="es-badges__item">
						<span class="es-badges__icon" aria-hidden="true">
							<?php echo es_get_icon( (string) $es_badge['icon'], 'es-icon', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<span class="es-badges__text">
							<strong><?php echo esc_html( $es_badge['title'] ); ?></strong>
							<?php if ( ! empty( $es_badge['desc'] ) ) : ?>
								<span><?php echo esc_html( $es_badge['desc'] ); ?></span>
							<?php endif; ?>
						</span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<p class="es-footer__copyright"><?php echo esc_html( wp_strip_all_tags( $es_copyright ) ); ?></p>
	</div>
</div>
