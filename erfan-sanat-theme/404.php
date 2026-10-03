<?php
/**
 * 404 page with helpful navigation and a search form.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

es_section_header(
	array(
		'eyebrow' => __( 'خطای ۴۰۴', 'erfan-sanat' ),
		'title'   => __( 'این صفحه پیدا نشد.', 'erfan-sanat' ),
		'text'    => __( 'ممکن است نشانی تغییر کرده باشد یا صفحه حذف شده باشد. از مسیرهای زیر ادامه دهید.', 'erfan-sanat' ),
		'tag'     => 'h1',
		'align'   => 'center',
	)
);

$es_404_links = array(
	array( 'label' => __( 'صفحهٔ اصلی', 'erfan-sanat' ), 'url' => home_url( '/' ), 'icon' => 'spark' ),
	array( 'label' => __( 'پروژه‌های نورپردازی', 'erfan-sanat' ), 'url' => es_projects_url(), 'icon' => 'tunnel' ),
	array( 'label' => __( 'فروشگاه تجهیزات', 'erfan-sanat' ), 'url' => es_shop_url(), 'icon' => 'cart' ),
	array( 'label' => __( 'مقالات فنی', 'erfan-sanat' ), 'url' => es_blog_url(), 'icon' => 'bulb' ),
);
?>
<main id="main" class="es-main es-main--404" role="main">
	<div class="es-container es-404">
		<div class="es-404__search"><?php get_search_form(); ?></div>

		<ul class="es-404__links">
			<?php foreach ( $es_404_links as $es_link ) : ?>
				<li>
					<a class="es-404__link" href="<?php echo esc_url( $es_link['url'] ); ?>">
						<?php echo es_get_icon( $es_link['icon'], 'es-icon', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<span><?php echo esc_html( $es_link['label'] ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( es_woocommerce_active() ) : ?>
			<section class="es-section es-section--404-products">
				<?php
				$es_products = new WP_Query(
					array(
						'post_type'      => 'product',
						'posts_per_page' => 4,
						'no_found_rows'  => true,
						'meta_query'     => array( array( 'key' => '_thumbnail_id', 'compare' => 'EXISTS' ) ),
					)
				);

				if ( $es_products->have_posts() ) :
					?>
					<h2 class="es-section__title es-section__title--sm"><?php esc_html_e( 'محصولات پیشنهادی', 'erfan-sanat' ); ?></h2>
					<div class="es-grid es-grid--products es-grid--cols-4">
						<?php
						while ( $es_products->have_posts() ) :
							$es_products->the_post();
							get_template_part( 'template-parts/cards/product-card' );
						endwhile;
						wp_reset_postdata();
						?>
					</div>
					<?php
				endif;
				?>
			</section>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
