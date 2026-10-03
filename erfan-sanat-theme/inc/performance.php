<?php
/**
 * Front-end performance tuning.
 *
 * Every optimisation is opt-in through the "کارایی" options tab and reversible;
 * nothing here touches stored content or WordPress core files.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Disable the WordPress emoji script and styles.
 *
 * @return void
 */
function es_disable_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'emoji_svg_url', '__return_false' );
}

/**
 * Disable the wp-embed script and oEmbed discovery on the front-end.
 *
 * @return void
 */
function es_disable_embeds() {
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	remove_action( 'rest_api_init', 'wp_oembed_register_route' );
	add_filter( 'embed_oembed_discover', '__return_false' );
}

/**
 * Remove jQuery Migrate from the front-end (jQuery itself is untouched).
 *
 * @param WP_Scripts $scripts Script registry.
 * @return void
 */
function es_remove_jquery_migrate( $scripts ) {
	if ( is_admin() || empty( $scripts->registered['jquery'] ) ) {
		return;
	}

	$jquery = $scripts->registered['jquery'];

	if ( ! empty( $jquery->deps ) ) {
		$jquery->deps = array_diff( $jquery->deps, array( 'jquery-migrate' ) );
	}
}

/**
 * Add decoding hints to theme rendered images.
 *
 * @param array $attr       Image attributes.
 * @param int   $attachment Attachment id.
 * @return array
 */
function es_image_attributes( $attr, $attachment = 0 ) {
	if ( ! es_opt( 'perf_lazy_load', true ) ) {
		unset( $attr['loading'] );
	}

	if ( empty( $attr['decoding'] ) ) {
		$attr['decoding'] = 'async';
	}

	if ( ! empty( $attr['class'] ) && false !== strpos( (string) $attr['class'], 'es-hero__image' ) ) {
		$attr['fetchpriority'] = 'high';
		$attr['loading']       = 'eager';
		$attr['decoding']      = 'sync';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'es_image_attributes', 10, 2 );

/**
 * DNS prefetch hints configured by the site owner.
 *
 * @return void
 */
function es_dns_prefetch() {
	$raw = (string) es_opt( 'perf_dns_prefetch', '' );

	if ( '' === trim( $raw ) ) {
		return;
	}

	$hosts = preg_split( '/\r\n|\r|\n/', $raw );
	$hosts = is_array( $hosts ) ? array_filter( array_map( 'trim', $hosts ) ) : array();

	foreach ( array_slice( $hosts, 0, 8 ) as $host ) {
		if ( ! preg_match( '#^https?://#i', $host ) ) {
			$host = 'https://' . $host;
		}

		$host = esc_url( $host );
		if ( ! $host ) {
			continue;
		}

		printf( '<link rel="dns-prefetch" href="%s">' . "\n", esc_url( $host ) );
	}
}
add_action( 'wp_head', 'es_dns_prefetch', 2 );

/**
 * Remove the unused `wlwmanifest` and `rsd` head links (micro-optimisation).
 *
 * @return void
 */
function es_remove_head_links() {
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}

/**
 * Keep the front-end free from theme admin assets (defensive guard).
 *
 * @return void
 */
function es_no_admin_assets_on_frontend() {
	if ( is_admin() ) {
		return;
	}

	wp_deregister_style( 'es-admin' );
	wp_deregister_script( 'es-admin' );
}
add_action( 'wp_enqueue_scripts', 'es_no_admin_assets_on_frontend', 99 );

/**
 * Expose the optimisations actually applied (used by the admin dashboard).
 *
 * @return array<string,bool>
 */
function es_performance_report() {
	return array(
		'defer_js'         => (bool) es_opt( 'perf_defer_js', true ),
		'preload_fonts'    => (bool) es_opt( 'perf_preload_fonts', true ),
		'lazy_load'        => (bool) es_opt( 'perf_lazy_load', true ),
		'disable_emoji'    => (bool) es_opt( 'perf_disable_emoji', true ),
		'disable_embeds'   => (bool) es_opt( 'perf_disable_embeds', true ),
		'no_jquery_migrate'=> (bool) es_opt( 'perf_disable_jquery_migrate', true ),
		'block_css_removed'=> (bool) es_opt( 'perf_remove_global_styles', false ),
		'inline_tokens'    => (bool) es_opt( 'perf_critical_css', true ),
	);
}

/**
 * Apply the configured optimisations.
 *
 * Hooked to `init` because reading options before init triggers translation
 * loading too early (WordPress 6.7+ notice) — options are only available from
 * a fully bootstrapped WordPress.
 *
 * @return void
 */
function es_apply_performance_options() {
	if ( es_opt( 'perf_disable_emoji', true ) ) {
		es_disable_emoji();
	}

	if ( es_opt( 'perf_disable_embeds', true ) ) {
		es_disable_embeds();
	}

	if ( es_opt( 'perf_disable_jquery_migrate', true ) ) {
		add_action( 'wp_default_scripts', 'es_remove_jquery_migrate' );
	}

	es_remove_head_links();
}
add_action( 'init', 'es_apply_performance_options', 1 );
