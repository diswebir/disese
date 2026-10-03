<?php
/**
 * Homepage products section (WooCommerce products, with a graceful fallback).
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! es_opt( 'home_products_enable', true ) ) {
	return;
}

$es_count    = max( 1, (int) es_opt( 'home_products_count', 4 ) );
$es_feature  = (string) es_opt( 'home_products_feature', '' );
$es_badge    = (string) es_opt( 'home_products_badge', '' );
$es_products = null;

if ( es_woocommerce_active() ) {
	$es_products = new WP_Query(
		array(
			'post_type'           => 'product',
			'posts_per_page'      => $es_count,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'tax_query'           => array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'exclude-from-catalog',
					'operator' => 'NOT IN',
				),
			),
		)
	);
}
?>
<section class="es-section es-section--products" id="products">
	<div class="es-container">

		<?php if ( $es_feature || $es_badge ) : ?>
			<div class="es-feature-strip">
				<span class="es-feature-strip__icon" aria-hidden="true"><?php es_icon( 'chip', 'es-icon', 26 ); ?></span>
				<p class="es-feature-strip__text">
					<?php echo esc_html( $es_badge ? $es_badge : wp_strip_all_tags( $es_feature ) ); ?>
				</p>
			</div>
		<?php endif; ?>

		<?php
		es_section_header(
			array(
				'eyebrow' => (string) es_opt( 'home_products_eyebrow', '' ),
				'title'   => (string) es_opt( 'home_products_title', '' ),
				'text'    => (string) es_opt( 'home_products_text', '' ),
				'align'   => 'center',
			)
		);
		?>

		<?php if ( $es_products && $es_products->have_posts() ) : ?>
			<div class="es-grid es-grid--products es-grid--cols-<?php echo esc_attr( (string) min( 4, $es_count ) ); ?>">
				<?php
				while ( $es_products->have_posts() ) :
					$es_products->the_post();
					get_template_part( 'template-parts/cards/product-card' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>

			<div class="es-section__footer">
				<a class="es-btn es-btn--outline es-btn--lg" href="<?php echo esc_url( es_shop_url() ); ?>">
					<span><?php echo esc_html( (string) es_opt( 'home_products_link_text', __( 'مشاهدهٔ همهٔ محصولات', 'erfan-sanat' ) ) ); ?></span>
					<?php es_icon( 'arrow', 'es-icon', 18 ); ?>
				</a>

				<?php if ( es_opt( 'home_products_custom_order_text', '' ) ) : ?>
					<a class="es-btn es-btn--ghost es-btn--lg" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
						<?php echo esc_html( (string) es_opt( 'home_products_custom_order_text', '' ) ); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<?php
			$es_empty = array(
				'title' => __( 'محصولی برای نمایش وجود ندارد.', 'erfan-sanat' ),
				'icon'  => 'cart',
				'text'  => __( 'برای فعال‌سازی فروشگاه، افزونهٔ ووکامرس را نصب و فعال کنید.', 'erfan-sanat' ),
			);

			if ( es_woocommerce_active() ) {
				$es_empty['text'] = __( 'با افزودن محصول از پیشخوان، این بخش به‌صورت خودکار تکمیل می‌شود.', 'erfan-sanat' );
			}

			if ( es_woocommerce_active() && current_user_can( 'edit_products' ) ) {
				$es_empty['button_text'] = __( 'افزودن محصول', 'erfan-sanat' );
				$es_empty['button_url']  = admin_url( 'post-new.php?post_type=product' );
			} else {
				// Visitors never see a dead end: route them to a real enquiry.
				$es_contact_page = get_page_by_path( 'contact' );

				$es_empty['button_text'] = __( 'درخواست مشاوره و استعلام قیمت', 'erfan-sanat' );
				$es_empty['button_url']  = $es_contact_page instanceof WP_Post
					? (string) get_permalink( $es_contact_page )
					: home_url( '/contact/' );
			}

			es_empty_state( $es_empty );
			?>
		<?php endif; ?>
	</div>
</section>
