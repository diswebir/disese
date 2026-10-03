<?php
/**
 * Asset loading: front-end, editor and admin.
 *
 * Assets are only loaded where they are used: theme.css/theme.js on the
 * front-end, admin.css/admin.js on the theme screens (and post edit screens
 * that render the theme meta boxes), WooCommerce overrides only on store
 * routes.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Front-end assets.
 *
 * @return void
 */
function es_enqueue_assets() {
	// Self-hosted variable font (no Google Fonts, no CDN).
	wp_enqueue_style(
		'es-fonts',
		ES_THEME_URI . 'assets/css/fonts.css',
		array(),
		ES_THEME_VERSION
	);

	wp_enqueue_style(
		'es-style',
		get_stylesheet_uri(),
		array( 'es-fonts' ),
		ES_THEME_VERSION
	);

	wp_enqueue_style(
		'es-theme',
		ES_THEME_URI . 'assets/css/theme.css',
		array( 'es-style' ),
		ES_THEME_VERSION
	);

	if ( es_woocommerce_active() && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
		wp_enqueue_style(
			'es-woocommerce',
			ES_THEME_URI . 'assets/css/woocommerce.css',
			array( 'es-theme' ),
			ES_THEME_VERSION
		);
	}

	wp_enqueue_script(
		'es-theme',
		ES_THEME_URI . 'assets/js/theme.js',
		array(),
		ES_THEME_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => apply_filters( 'es_script_strategy', es_opt( 'perf_defer_js', true ) ? 'defer' : '' ),
		)
	);

	wp_localize_script(
		'es-theme',
		'esTheme',
		array(
			'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
			'restUrl'      => esc_url_raw( rest_url( 'wp/v2/' ) ),
			'nonce'        => wp_create_nonce( 'es_frontend' ),
			'isRtl'        => is_rtl() || 'rtl' === es_theme_direction(),
			'breakpoint'   => (int) es_opt( 'nav_breakpoint', 992 ),
			'i18n'         => array(
				'loading'  => __( 'در حال بارگذاری…', 'erfan-sanat' ),
				'error'    => __( 'خطایی رخ داد. دوباره تلاش کنید.', 'erfan-sanat' ),
				'copied'   => __( 'کپی شد', 'erfan-sanat' ),
				'close'    => __( 'بستن', 'erfan-sanat' ),
				'openMenu' => __( 'باز کردن منو', 'erfan-sanat' ),
				'closeMenu' => __( 'بستن منو', 'erfan-sanat' ),
				'searchPlaceholder' => __( 'جست‌وجو در سایت…', 'erfan-sanat' ),
			),
		)
	);

	// Comment reply script, only where it is needed.
	if ( is_singular() && comments_open() && (int) get_option( 'thread_comments' ) === 1 ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular( array( 'post', 'project' ) ) && es_opt( 'seo_enable_schema', true ) && file_exists( ES_THEME_DIR . 'assets/js/schema.js' ) ) {
		wp_enqueue_script( 'es-schema', ES_THEME_URI . 'assets/js/schema.js', array(), ES_THEME_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'es_enqueue_assets' );

/**
 * Resource hints for the self-hosted font files.
 *
 * @param array  $urls          URLs to print.
 * @param string $relation_type Relation.
 * @return array
 */
function es_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		return $urls; // Everything is served from this origin: no third parties.
	}

	if ( 'preload' !== $relation_type || ! es_opt( 'perf_preload_fonts', true ) ) {
		return $urls;
	}

	$fonts = array(
		'assets/fonts/vazirmatn-arabic-wght-normal.woff2',
		'assets/fonts/vazirmatn-latin-wght-normal.woff2',
	);

	foreach ( $fonts as $font ) {
		if ( file_exists( ES_THEME_DIR . $font ) ) {
			$urls[] = array(
				'href'        => ES_THEME_URI . $font,
				'as'          => 'font',
				'type'        => 'font/woff2',
				'crossorigin' => 'anonymous',
			);
		}
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'es_resource_hints', 10, 2 );

/**
 * Block editor assets (shared design tokens + editor stylesheet).
 *
 * @return void
 */
function es_editor_assets() {
	wp_enqueue_style(
		'es-editor',
		ES_THEME_URI . 'assets/css/editor.css',
		array(),
		ES_THEME_VERSION
	);
}
add_action( 'enqueue_block_editor_assets', 'es_editor_assets' );

/**
 * Admin assets, loaded only on the screens that need them.
 *
 * @param string $hook Current admin page hook.
 * @return void
 */
function es_admin_assets( $hook ) {
	$screen          = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$is_theme_screen = false !== strpos( (string) $hook, 'erfan-sanat' );
	$is_edit_screen  = $screen && in_array( $screen->base, array( 'post', 'post-new' ), true )
		&& ( 'project' === $screen->post_type || 'post' === $screen->post_type || 'product' === $screen->post_type );
	$is_theme_page   = 'appearance_page_' . ES_THEME_SLUG === $hook;
	$is_widgets      = in_array( $hook, array( 'widgets.php', 'nav-menus.php' ), true );

	if ( ! $is_theme_screen && ! $is_edit_screen && ! $is_theme_page && ! $is_widgets ) {
		return;
	}

	wp_enqueue_style(
		'es-admin',
		ES_THEME_URI . 'assets/css/admin.css',
		array( 'wp-color-picker' ),
		ES_THEME_VERSION
	);

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_media();
	wp_enqueue_editor();

	wp_enqueue_script(
		'es-admin',
		ES_THEME_URI . 'assets/js/admin.js',
		array( 'jquery', 'wp-color-picker', 'jquery-ui-sortable', 'media-editor' ),
		ES_THEME_VERSION,
		true
	);

	wp_localize_script(
		'es-admin',
		'esAdmin',
		array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'es_admin' ),
			'i18n'     => array(
				'selectImage'  => __( 'انتخاب تصویر', 'erfan-sanat' ),
				'useImage'     => __( 'استفاده از این تصویر', 'erfan-sanat' ),
				'removeImage'  => __( 'حذف تصویر', 'erfan-sanat' ),
				'addRow'       => __( 'افزودن ردیف', 'erfan-sanat' ),
				'removeRow'    => __( 'حذف ردیف', 'erfan-sanat' ),
				'confirmReset' => __( 'همهٔ تنظیمات قالب به مقادیر پیش‌فرض بازگردد؟ این کار قابل بازگشت نیست.', 'erfan-sanat' ),
				'confirmRow'   => __( 'این ردیف حذف شود؟', 'erfan-sanat' ),
				'copied'       => __( 'کپی شد', 'erfan-sanat' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'es_admin_assets' );

/**
 * Inline critical CSS for the admin settings screen (progress bar, notice).
 *
 * @return void
 */
function es_admin_inline_css() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || false === strpos( (string) $screen->id, 'erfan-sanat' ) ) {
		return;
	}

	$css = '.es-settings__badge{background:' . esc_attr( (string) es_opt( 'color_primary', '#f2b32c' ) ) . ';}';

	wp_register_style( 'es-admin-inline', false, array( 'es-admin' ), ES_THEME_VERSION );
	wp_enqueue_style( 'es-admin-inline' );
	wp_add_inline_style( 'es-admin-inline', $css );
}
add_action( 'admin_enqueue_scripts', 'es_admin_inline_css', 30 );
