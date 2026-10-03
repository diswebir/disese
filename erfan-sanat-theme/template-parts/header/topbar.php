<?php
/**
 * Topbar: phone, email, working hours and social profiles.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! es_opt( 'header_show_topbar', true ) ) {
	return;
}

$es_phone   = (string) es_opt( 'phone_primary', '' );
$es_tel     = es_phone_href( $es_phone );
$es_email   = (string) es_opt( 'email_primary', '' );
$es_hours   = (string) es_opt( 'working_hours', '' );
$es_text    = (string) es_opt( 'topbar_text', '' );
$es_socials = es_get_social_items();
$es_iso     = (string) es_opt( 'iso_badge_text', '' );
?>
<div class="es-topbar" id="es-topbar">
	<div class="es-container es-topbar__inner">

		<ul class="es-topbar__list">
			<?php if ( $es_phone && $es_tel ) : ?>
				<li>
					<?php es_icon( 'phone', 'es-icon', 15 ); ?>
					<a href="<?php echo esc_url( $es_tel ); ?>" dir="ltr"><?php echo esc_html( $es_phone ); ?></a>
				</li>
			<?php endif; ?>

			<?php if ( $es_email ) : ?>
				<li>
					<?php es_icon( 'mail', 'es-icon', 15 ); ?>
					<a href="<?php echo esc_url( 'mailto:' . antispambot( $es_email ) ); ?>" dir="ltr"><?php echo esc_html( antispambot( $es_email ) ); ?></a>
				</li>
			<?php endif; ?>

			<?php if ( $es_hours ) : ?>
				<li class="es-topbar__hours">
					<?php es_icon( 'clock', 'es-icon', 15 ); ?>
					<span><?php echo esc_html( $es_hours ); ?></span>
				</li>
			<?php endif; ?>
		</ul>

		<?php if ( $es_text ) : ?>
			<p class="es-topbar__notice"><?php echo esc_html( $es_text ); ?></p>
		<?php endif; ?>

		<div class="es-topbar__end">
			<?php if ( $es_iso ) : ?>
				<span class="es-topbar__badge">
					<?php es_icon( 'certified', 'es-icon', 15 ); ?>
					<?php echo esc_html( $es_iso ); ?>
				</span>
			<?php endif; ?>

			<?php if ( es_opt( 'social_show_header', true ) && $es_socials ) : ?>
				<ul class="es-socials es-socials--sm">
					<?php foreach ( $es_socials as $es_social ) : ?>
						<li>
							<a href="<?php echo esc_url( $es_social['url'] ); ?>" target="_blank" rel="noopener nofollow" aria-label="<?php echo esc_attr( $es_social['label'] ); ?>">
								<?php echo es_get_social_icon( (string) $es_social['network'], 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</div>
