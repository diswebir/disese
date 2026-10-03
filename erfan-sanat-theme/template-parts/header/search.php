<?php
/**
 * Header search overlay: instant (REST-powered) search with a no-JS fallback.
 *
 * The form itself is a plain GET form pointing at the WordPress search route:
 * if JavaScript is unavailable the overlay still submits and lands on the
 * regular search template. With JavaScript the panel fetches suggestions from
 * the core `/wp/v2/search` endpoint (local REST API — no third party).
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! es_opt( 'enable_search_in_header', true ) ) {
	return;
}

$es_quick_links = array();

$es_projects_archive = get_post_type_archive_link( 'project' );

if ( $es_projects_archive ) {
	$es_quick_links[] = array(
		'label' => __( 'پروژه‌های نورپردازی', 'erfan-sanat' ),
		'url'   => $es_projects_archive,
	);
}

if ( es_woocommerce_active() ) {
	$es_shop = wc_get_page_permalink( 'shop' );

	if ( $es_shop ) {
		$es_quick_links[] = array(
			'label' => __( 'فروشگاه محصولات', 'erfan-sanat' ),
			'url'   => $es_shop,
		);
	}
}

$es_blog_archive = get_permalink( (int) get_option( 'page_for_posts' ) );

if ( ! $es_blog_archive ) {
	$es_blog_archive = home_url( '/blog/' );
}

$es_quick_links[] = array(
	'label' => __( 'مقالات و راهنماها', 'erfan-sanat' ),
	'url'   => $es_blog_archive,
);

$es_contact = get_page_by_path( 'contact' );

if ( $es_contact instanceof WP_Post ) {
	$es_quick_links[] = array(
		'label' => __( 'تماس با ما', 'erfan-sanat' ),
		'url'   => get_permalink( $es_contact ),
	);
}
?>
<div class="es-search" id="es-search-panel" data-es-search-panel hidden>
	<button type="button" class="es-search__backdrop" data-es-search-close tabindex="-1" aria-hidden="true"></button>

	<div class="es-search__dialog" role="dialog" aria-modal="true" aria-labelledby="es-search-title">
		<h2 class="screen-reader-text" id="es-search-title"><?php esc_html_e( 'جست‌وجو در سایت', 'erfan-sanat' ); ?></h2>

		<form role="search" method="get" class="es-search__form" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-es-search-form>
			<span class="es-search__icon" aria-hidden="true"><?php es_icon( 'search', 'es-icon', 22 ); ?></span>
			<label class="screen-reader-text" for="es-search-input"><?php esc_html_e( 'عبارت مورد جست‌وجو', 'erfan-sanat' ); ?></label>
			<input
				id="es-search-input"
				class="es-search__input"
				type="search"
				name="s"
				value=""
				placeholder="<?php esc_attr_e( 'نام محصول، پروژه یا مقاله را بنویسید…', 'erfan-sanat' ); ?>"
				autocomplete="off"
				required
				data-es-search-input
			>
			<button type="submit" class="es-btn es-btn--primary es-btn--sm es-search__submit">
				<?php esc_html_e( 'جست‌وجو', 'erfan-sanat' ); ?>
			</button>
		</form>

		<?php if ( $es_quick_links ) : ?>
			<div class="es-search__quick">
				<span class="es-search__quick-label"><?php esc_html_e( 'دسترسی سریع:', 'erfan-sanat' ); ?></span>
				<?php foreach ( $es_quick_links as $es_link ) : ?>
					<a class="es-pill es-pill--sm" href="<?php echo esc_url( $es_link['url'] ); ?>"><?php echo esc_html( $es_link['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="es-search__results" data-es-search-results aria-live="polite" aria-label="<?php esc_attr_e( 'نتایج پیشنهادی', 'erfan-sanat' ); ?>"></div>

		<button type="button" class="es-search__close" data-es-search-close aria-label="<?php esc_attr_e( 'بستن جست‌وجو', 'erfan-sanat' ); ?>">
			<?php es_icon( 'close', 'es-icon', 20 ); ?>
		</button>
	</div>
</div>
