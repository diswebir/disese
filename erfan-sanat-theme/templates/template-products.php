<?php
/**
 * Template Name: نمای محصولات (کاتالوگ)
 * Template Post Type: page
 *
 * A catalogue view of the shop: product categories, a grid of products and
 * the inquiry call to action. Falls back to a clear notice when WooCommerce
 * is not active.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="es-main es-main--catalog" role="main">

	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<header class="es-archive-hero">
			<div class="es-container">
				<?php
				es_section_header(
					array(
						'eyebrow' => __( 'کاتالوگ محصولات', 'erfan-sanat' ),
						'title'   => get_the_title(),
						'tag'     => 'h1',
						'align'   => 'start',
					)
				);
				?>
				<?php if ( get_the_content() ) : ?>
					<div class="es-archive-hero__desc es-entry es-entry--compact"><?php the_content(); ?></div>
				<?php endif; ?>
			</div>
		</header>
		<?php
	endwhile;
	?>

	<div class="es-container">
		<?php if ( ! es_woocommerce_active() ) : ?>
			<?php
			es_empty_state(
				array(
					'title' => __( 'فروشگاه ووکامرس فعال نیست.', 'erfan-sanat' ),
					'text'  => __( 'برای نمایش کاتالوگ محصولات، افزونهٔ ووکامرس را نصب و فعال کنید. قالب بدون آن هم به‌درستی کار می‌کند.', 'erfan-sanat' ),
					'icon'  => 'cart',
				)
			);
			?>
		<?php else : ?>

			<?php
			$es_cats = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => false,
					'parent'     => 0,
				)
			);
			?>

			<?php if ( ! is_wp_error( $es_cats ) && $es_cats ) : ?>
				<nav class="es-cat-nav" aria-label="<?php esc_attr_e( 'دسته‌بندی محصولات', 'erfan-sanat' ); ?>">
					<ul class="es-cat-nav__list">
						<?php foreach ( $es_cats as $es_cat ) : ?>
							<li>
								<a class="es-cat-nav__item" href="<?php echo esc_url( (string) get_term_link( $es_cat ) ); ?>">
									<span class="es-cat-nav__name"><?php echo esc_html( $es_cat->name ); ?></span>
									<span class="es-cat-nav__count"><?php echo esc_html( es_num( number_format_i18n( $es_cat->count ) ) ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>

			<?php
			$es_products = new WP_Query(
				array(
					'post_type'      => 'product',
					'posts_per_page' => max( 1, (int) es_opt( 'product_per_page', 12 ) ),
					'paged'          => max( 1, (int) get_query_var( 'paged' ) ),
					'tax_query'      => array(
						array(
							'taxonomy' => 'product_visibility',
							'field'    => 'name',
							'terms'    => 'exclude-from-catalog',
							'operator' => 'NOT IN',
						),
					),
				)
			);

			$GLOBALS['wp_query'] = $es_products; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

			get_template_part(
				'template-parts/global/loop',
				null,
				array(
					'post_type'   => 'product',
					'layout'      => 'grid',
					'columns'     => max( 1, min( 4, (int) es_opt( 'product_archive_columns', 3 ) ) ),
					'empty_title' => __( 'هنوز محصولی منتشر نشده است.', 'erfan-sanat' ),
				)
			);

			wp_reset_postdata();
			?>

			<div class="es-section__footer">
				<a class="es-btn es-btn--outline es-btn--lg" href="<?php echo esc_url( es_shop_url() ); ?>">
					<span><?php esc_html_e( 'مشاهدهٔ فروشگاه کامل', 'erfan-sanat' ); ?></span>
					<?php es_icon( 'arrow', 'es-icon', 18 ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
