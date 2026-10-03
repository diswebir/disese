<?php
/**
 * Template Name: تماس با ما
 * Template Post Type: page
 *
 * Contact page: form, contact cards, opening hours, departments and the map
 * coordinates from the theme options (no external map script is loaded; the
 * coordinates are linked out to a map provider instead).
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_phone   = (string) es_opt( 'phone_primary', '' );
$es_sales   = (string) es_opt( 'phone_sales', '' );
$es_mobile  = (string) es_opt( 'mobile_primary', '' );
$es_email   = (string) es_opt( 'email_primary', '' );
$es_address = (string) es_opt( 'address_line', '' );
$es_postal  = (string) es_opt( 'postal_code', '' );
$es_hours   = (string) es_opt( 'working_hours', '' );
$es_coords  = (string) es_opt( 'map_coords', '' );
?>
<main id="main" class="es-main es-main--contact" role="main">

	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<header class="es-archive-hero">
			<div class="es-container">
				<?php
				es_section_header(
					array(
						'eyebrow' => __( 'ارتباط با عرفان صنعت', 'erfan-sanat' ),
						'title'   => get_the_title(),
						'tag'     => 'h1',
						'align'   => 'start',
					)
				);
				?>
			</div>
		</header>

		<div class="es-container es-contact">
			<div class="es-contact__form">
				<?php if ( get_the_content() ) : ?>
					<div class="es-entry es-entry--compact"><?php the_content(); ?></div>
				<?php endif; ?>

				<h2 class="es-section__title es-section__title--sm"><?php esc_html_e( 'فرم درخواست مشاوره', 'erfan-sanat' ); ?></h2>
				<?php es_render_contact_form(); ?>
			</div>

			<aside class="es-contact__aside">
				<section class="es-spec-card">
					<h2 class="es-spec-card__title"><?php esc_html_e( 'راه‌های ارتباطی', 'erfan-sanat' ); ?></h2>

					<ul class="es-spec-card__list es-spec-card__list--contact">
						<?php if ( $es_phone ) : ?>
							<li>
								<span class="es-spec-card__label"><?php esc_html_e( 'تلفن دفتر مرکزی', 'erfan-sanat' ); ?></span>
								<a class="es-spec-card__value" href="<?php echo esc_url( (string) es_phone_href( $es_phone ) ); ?>" dir="ltr"><?php echo esc_html( $es_phone ); ?></a>
							</li>
						<?php endif; ?>

						<?php if ( $es_sales ) : ?>
							<li>
								<span class="es-spec-card__label"><?php esc_html_e( 'تلفن واحد فروش', 'erfan-sanat' ); ?></span>
								<a class="es-spec-card__value" href="<?php echo esc_url( (string) es_phone_href( $es_sales ) ); ?>" dir="ltr"><?php echo esc_html( $es_sales ); ?></a>
							</li>
						<?php endif; ?>

						<?php if ( $es_mobile ) : ?>
							<li>
								<span class="es-spec-card__label"><?php esc_html_e( 'همراه / واتس‌اپ', 'erfan-sanat' ); ?></span>
								<a class="es-spec-card__value" href="<?php echo esc_url( (string) es_whatsapp_href( $es_mobile ) ); ?>" dir="ltr"><?php echo esc_html( $es_mobile ); ?></a>
							</li>
						<?php endif; ?>

						<?php if ( $es_email ) : ?>
							<li>
								<span class="es-spec-card__label"><?php esc_html_e( 'ایمیل', 'erfan-sanat' ); ?></span>
								<a class="es-spec-card__value" href="<?php echo esc_url( 'mailto:' . antispambot( $es_email ) ); ?>" dir="ltr"><?php echo esc_html( antispambot( $es_email ) ); ?></a>
							</li>
						<?php endif; ?>

						<?php if ( $es_hours ) : ?>
							<li>
								<span class="es-spec-card__label"><?php esc_html_e( 'ساعات کاری', 'erfan-sanat' ); ?></span>
								<span class="es-spec-card__value"><?php echo esc_html( $es_hours ); ?></span>
							</li>
						<?php endif; ?>
					</ul>
				</section>

				<?php if ( $es_address ) : ?>
					<section class="es-spec-card">
						<h2 class="es-spec-card__title"><?php esc_html_e( 'نشانی کارخانه', 'erfan-sanat' ); ?></h2>
						<p class="es-spec-card__address"><?php echo esc_html( $es_address ); ?></p>

						<?php if ( $es_postal ) : ?>
							<p class="es-spec-card__postal">
								<?php printf( /* translators: %s: postal code */ esc_html__( 'کدپستی: %s', 'erfan-sanat' ), esc_html( es_num( $es_postal ) ) ); ?>
							</p>
						<?php endif; ?>

						<?php if ( $es_coords ) : ?>
							<a class="es-btn es-btn--outline es-btn--block" href="<?php echo esc_url( 'https://neshan.org/maps/@' . rawurlencode( $es_coords ) . ',17z' ); ?>" target="_blank" rel="noopener nofollow">
								<?php es_icon( 'pin', 'es-icon', 16 ); ?>
								<?php esc_html_e( 'مشاهده روی نقشه', 'erfan-sanat' ); ?>
							</a>
						<?php endif; ?>
					</section>
				<?php endif; ?>

				<section class="es-spec-card">
					<h2 class="es-spec-card__title"><?php esc_html_e( 'استعلام سریع تلفنی', 'erfan-sanat' ); ?></h2>
					<p class="es-spec-card__note"><?php esc_html_e( 'برای دریافت قیمت روز و مشاورهٔ فنی، شنبه تا پنجشنبه با کارشناسان ما تماس بگیرید.', 'erfan-sanat' ); ?></p>
					<?php if ( $es_phone && es_phone_href( $es_phone ) ) : ?>
						<a class="es-btn es-btn--primary es-btn--block" href="<?php echo esc_url( (string) es_phone_href( $es_phone ) ); ?>">
							<?php es_icon( 'phone', 'es-icon', 16 ); ?>
							<span dir="ltr"><?php echo esc_html( $es_phone ); ?></span>
						</a>
					<?php endif; ?>
				</section>
			</aside>
		</div>
		<?php
	endwhile;
	?>
</main>

<?php
get_footer();
