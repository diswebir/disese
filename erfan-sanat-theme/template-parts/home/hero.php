<?php
/**
 * Homepage hero.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! es_opt( 'home_hero_enable', true ) ) {
	return;
}

$es_eyebrow  = (string) es_opt( 'home_hero_eyebrow', '' );
$es_title    = (string) es_opt( 'home_hero_title', '' );
$es_accent   = (string) es_opt( 'home_hero_title_accent', '' );
$es_text     = (string) es_opt( 'home_hero_text', '' );
$es_btn1     = (string) es_opt( 'home_hero_btn1_text', '' );
$es_btn1_url = (string) es_opt( 'home_hero_btn1_url', '' );
$es_btn2     = (string) es_opt( 'home_hero_btn2_text', '' );
$es_btn2_url = (string) es_opt( 'home_hero_btn2_url', '' );
$es_image_id = (int) es_opt( 'home_hero_image', 0 );
$es_stats    = es_opt( 'home_hero_stats', array() );
$es_iso      = (string) es_opt( 'iso_badge_text', '' );
?>
<section class="es-hero" id="hero">
	<div class="es-hero__media">
		<?php if ( $es_image_id ) : ?>
			<?php
			echo wp_get_attachment_image(
				$es_image_id,
				'es-hero',
				false,
				array(
					'class'         => 'es-hero__image',
					'fetchpriority' => 'high',
					'loading'       => 'eager',
					'decoding'      => 'async',
					'alt'           => '',
				)
			);
			?>
		<?php else : ?>
			<img class="es-hero__image" src="<?php echo esc_url( ES_THEME_URI . 'assets/images/hero-light-tunnel.svg' ); ?>" alt="" width="1920" height="1080" fetchpriority="high">
		<?php endif; ?>

		<span class="es-hero__overlay" aria-hidden="true"></span>
		<span class="es-hero__beam" aria-hidden="true"></span>
	</div>

	<div class="es-container es-hero__inner">
		<div class="es-hero__content">
			<?php if ( $es_eyebrow ) : ?>
				<p class="es-hero__eyebrow">
					<span class="es-hero__dot" aria-hidden="true"></span>
					<span><?php echo esc_html( $es_eyebrow ); ?></span>
				</p>
			<?php endif; ?>

			<h1 class="es-hero__title">
				<?php if ( $es_title ) : ?>
					<span class="es-hero__title-line"><?php echo esc_html( $es_title ); ?></span>
				<?php endif; ?>

				<?php if ( $es_accent ) : ?>
					<span class="es-hero__title-accent"><?php echo esc_html( $es_accent ); ?></span>
				<?php endif; ?>
			</h1>

			<?php if ( $es_text ) : ?>
				<p class="es-hero__text"><?php echo esc_html( $es_text ); ?></p>
			<?php endif; ?>

			<div class="es-hero__actions">
				<?php if ( $es_btn1 ) : ?>
					<a class="es-btn es-btn--primary es-btn--lg" href="<?php echo esc_url( $es_btn1_url ? $es_btn1_url : '#projects' ); ?>">
						<span><?php echo esc_html( $es_btn1 ); ?></span>
						<?php es_icon( 'arrow', 'es-icon', 18 ); ?>
					</a>
				<?php endif; ?>

				<?php if ( $es_btn2 ) : ?>
					<a class="es-btn es-btn--ghost es-btn--lg" href="<?php echo esc_url( $es_btn2_url ? $es_btn2_url : '#contact' ); ?>">
						<?php es_icon( 'phone', 'es-icon', 18 ); ?>
						<span><?php echo esc_html( $es_btn2 ); ?></span>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( $es_iso ) : ?>
				<p class="es-hero__iso">
					<?php es_icon( 'certified', 'es-icon', 16 ); ?>
					<span><?php echo esc_html( $es_iso ); ?></span>
				</p>
			<?php endif; ?>
		</div>

		<?php if ( is_array( $es_stats ) && $es_stats ) : ?>
			<ul class="es-hero__stats">
				<?php foreach ( $es_stats as $es_stat ) : ?>
					<?php if ( empty( $es_stat['value'] ) && empty( $es_stat['label'] ) ) { continue; } ?>
					<li class="es-hero__stat">
						<span class="es-hero__stat-value"><?php echo esc_html( isset( $es_stat['value'] ) ? $es_stat['value'] : '' ); ?></span>
						<span class="es-hero__stat-label"><?php echo esc_html( isset( $es_stat['label'] ) ? $es_stat['label'] : '' ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>

	<a class="es-hero__scroll" href="#projects" aria-label="<?php esc_attr_e( 'رفتن به بخش پروژه‌ها', 'erfan-sanat' ); ?>">
		<span class="es-hero__scroll-text"><?php esc_html_e( 'اسکرول', 'erfan-sanat' ); ?></span>
		<span class="es-hero__scroll-line" aria-hidden="true"></span>
	</a>
</section>
