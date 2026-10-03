<?php
/**
 * Homepage about section with counters.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! es_opt( 'home_about_enable', true ) ) {
	return;
}

$es_image_id = (int) es_opt( 'home_about_image', 0 );
$es_features = es_opt( 'home_about_features', array() );
$es_btn_text = (string) es_opt( 'home_about_btn_text', '' );
$es_btn_url  = (string) es_opt( 'home_about_btn_url', '' );
$es_iso      = (string) es_opt( 'iso_badge_text', '' );
$es_stats    = es_opt( 'home_hero_stats', array() );

if ( ! $es_btn_url ) {
	$es_about_page = get_page_by_path( 'about' );
	$es_btn_url    = $es_about_page ? get_permalink( $es_about_page ) : home_url( '/about/' );
}
?>
<section class="es-section es-section--about" id="about">
	<div class="es-container es-about">

		<div class="es-about__media">
			<?php if ( $es_image_id ) : ?>
				<?php
				echo wp_get_attachment_image(
					$es_image_id,
					'es-card-wide',
					false,
					array(
						'class'   => 'es-about__image',
						'loading' => 'lazy',
						'alt'     => (string) es_opt( 'brand_name_fa', '' ),
					)
				);
				?>
			<?php else : ?>
				<img class="es-about__image" src="<?php echo esc_url( ES_THEME_URI . 'assets/images/about-light-element.svg' ); ?>" alt="<?php echo esc_attr( (string) es_opt( 'brand_name_fa', '' ) ); ?>" width="960" height="640" loading="lazy">
			<?php endif; ?>

			<?php if ( $es_iso ) : ?>
				<div class="es-about__badge">
					<?php es_icon( 'certified', 'es-icon', 26 ); ?>
					<div>
						<strong><?php echo esc_html( $es_iso ); ?></strong>
						<span><?php esc_html_e( 'مدیریت کیفیت بین‌المللی', 'erfan-sanat' ); ?></span>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<div class="es-about__content">
			<?php
			es_section_header(
				array(
					'eyebrow' => (string) es_opt( 'home_about_eyebrow', '' ),
					'title'   => (string) es_opt( 'home_about_title', '' ),
					'align'   => 'start',
				)
			);
			?>

			<div class="es-about__text"><?php echo wp_kses_post( wpautop( (string) es_opt( 'home_about_content', '' ) ) ); ?></div>

			<?php if ( is_array( $es_features ) && $es_features ) : ?>
				<ul class="es-check-list">
					<?php foreach ( $es_features as $es_feature ) : ?>
						<?php if ( empty( $es_feature['title'] ) ) { continue; } ?>
						<li class="es-check-list__item">
							<span class="es-check-list__icon" aria-hidden="true"><?php es_icon( 'check', 'es-icon', 14 ); ?></span>
							<span><?php echo esc_html( $es_feature['title'] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $es_btn_text ) : ?>
				<a class="es-btn es-btn--primary" href="<?php echo esc_url( $es_btn_url ); ?>">
					<span><?php echo esc_html( $es_btn_text ); ?></span>
					<?php es_icon( 'arrow', 'es-icon', 18 ); ?>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( is_array( $es_stats ) && $es_stats ) : ?>
			<ul class="es-about__stats">
				<?php foreach ( $es_stats as $es_stat ) : ?>
					<li>
						<span class="es-about__stat-value"><?php echo esc_html( isset( $es_stat['value'] ) ? $es_stat['value'] : '' ); ?></span>
						<span class="es-about__stat-label"><?php echo esc_html( isset( $es_stat['label'] ) ? $es_stat['label'] : '' ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
