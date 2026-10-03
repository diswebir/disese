<?php
/**
 * Footer main columns: about, quick links, product categories, contact.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_about   = (string) es_opt( 'footer_about', '' );
$es_phone   = (string) es_opt( 'phone_primary', '' );
$es_tel     = es_phone_href( $es_phone );
$es_email   = (string) es_opt( 'email_primary', '' );
$es_address = (string) es_opt( 'address_line', '' );
$es_hours   = (string) es_opt( 'working_hours', '' );
$es_socials = es_get_social_items();
?>
<div class="es-footer__main">
	<div class="es-container es-footer__grid">

		<div class="es-footer__col es-footer__col--brand">
			<?php get_template_part( 'template-parts/header/brand' ); ?>

			<?php if ( $es_about ) : ?>
				<p class="es-footer__about"><?php echo esc_html( $es_about ); ?></p>
			<?php endif; ?>

			<?php if ( es_opt( 'footer_show_socials', true ) && $es_socials ) : ?>
				<ul class="es-socials">
					<?php foreach ( $es_socials as $es_social ) : ?>
						<li>
							<a href="<?php echo esc_url( $es_social['url'] ); ?>" target="_blank" rel="noopener nofollow" aria-label="<?php echo esc_attr( $es_social['label'] ); ?>">
								<?php echo es_get_social_icon( (string) $es_social['network'], 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="es-footer__col">
			<h2 class="es-footer__title"><?php esc_html_e( 'دسترسی سریع', 'erfan-sanat' ); ?></h2>

			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'menu_class'     => 'es-footer__list',
						'container'      => false,
						'depth'          => 1,
					)
				);
			} else {
				?>
				<ul class="es-footer__list">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحهٔ اصلی', 'erfan-sanat' ); ?></a></li>
					<li><a href="<?php echo esc_url( es_projects_url() ); ?>"><?php esc_html_e( 'پروژه‌های نورپردازی', 'erfan-sanat' ); ?></a></li>
					<li><a href="<?php echo esc_url( es_shop_url() ); ?>"><?php esc_html_e( 'فروشگاه تجهیزات', 'erfan-sanat' ); ?></a></li>
					<li><a href="<?php echo esc_url( es_blog_url() ); ?>"><?php esc_html_e( 'مقالات فنی', 'erfan-sanat' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'تماس با ما', 'erfan-sanat' ); ?></a></li>
				</ul>
				<?php
			}
			?>
		</div>

		<div class="es-footer__col">
			<h2 class="es-footer__title"><?php esc_html_e( 'دسته‌بندی محصولات', 'erfan-sanat' ); ?></h2>

			<?php
			if ( has_nav_menu( 'footer2' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer2',
						'menu_class'     => 'es-footer__list',
						'container'      => false,
						'depth'          => 1,
					)
				);
			} elseif ( taxonomy_exists( 'product_cat' ) ) {
				$es_terms = get_terms(
					array(
						'taxonomy'   => 'product_cat',
						'hide_empty' => false,
						'parent'     => 0,
						'number'     => 6,
						'orderby'    => 'count',
						'order'      => 'DESC',
					)
				);

				if ( ! is_wp_error( $es_terms ) && $es_terms ) {
					echo '<ul class="es-footer__list">';
					foreach ( $es_terms as $es_term ) {
						printf(
							'<li><a href="%1$s">%2$s <span class="es-count">(%3$s)</span></a></li>',
							esc_url( (string) get_term_link( $es_term ) ),
							esc_html( $es_term->name ),
							esc_html( es_num( number_format_i18n( $es_term->count ) ) )
						);
					}
					echo '</ul>';
				} else {
					echo '<p class="es-footer__muted">' . esc_html__( 'هنوز دسته‌بندی محصولی ساخته نشده است.', 'erfan-sanat' ) . '</p>';
				}
			} else {
				echo '<p class="es-footer__muted">' . esc_html__( 'فروشگاه فعال نیست.', 'erfan-sanat' ) . '</p>';
			}
			?>
		</div>

		<div class="es-footer__col es-footer__col--contact">
			<h2 class="es-footer__title"><?php esc_html_e( 'ارتباط با ما', 'erfan-sanat' ); ?></h2>

			<ul class="es-contact-list">
				<?php if ( $es_tel ) : ?>
					<li class="es-contact-list__item">
						<span class="es-contact-list__icon"><?php es_icon( 'phone', 'es-icon', 18 ); ?></span>
						<a href="<?php echo esc_url( $es_tel ); ?>" dir="ltr"><?php echo esc_html( $es_phone ); ?></a>
					</li>
				<?php endif; ?>

				<?php if ( $es_email ) : ?>
					<li class="es-contact-list__item">
						<span class="es-contact-list__icon"><?php es_icon( 'mail', 'es-icon', 18 ); ?></span>
						<a href="<?php echo esc_url( 'mailto:' . antispambot( $es_email ) ); ?>" dir="ltr"><?php echo esc_html( antispambot( $es_email ) ); ?></a>
					</li>
				<?php endif; ?>

				<?php if ( $es_address ) : ?>
					<li class="es-contact-list__item">
						<span class="es-contact-list__icon"><?php es_icon( 'pin', 'es-icon', 18 ); ?></span>
						<span><?php echo esc_html( $es_address ); ?></span>
					</li>
				<?php endif; ?>

				<?php if ( $es_hours ) : ?>
					<li class="es-contact-list__item">
						<span class="es-contact-list__icon"><?php es_icon( 'clock', 'es-icon', 18 ); ?></span>
						<span><?php echo esc_html( $es_hours ); ?></span>
					</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>
