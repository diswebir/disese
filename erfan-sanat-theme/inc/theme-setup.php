<?php
/**
 * Theme setup: supports, menus, image sizes, translation.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme supports and navigation locations.
 *
 * @return void
 */
function es_theme_setup() {
	load_theme_textdomain( 'erfan-sanat', ES_THEME_DIR . 'languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-line-height' );
	add_theme_support( 'custom-spacing' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );

	add_theme_support(
		'custom-logo',
		array(
			'height'               => 64,
			'width'                => 220,
			'flex-height'          => true,
			'flex-width'           => true,
			'unlink-homepage-logo' => false,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'منوی اصلی (هدر)', 'erfan-sanat' ),
			'topbar'  => __( 'منوی نوار بالایی', 'erfan-sanat' ),
			'footer'  => __( 'منوی فوتر — ستون اول', 'erfan-sanat' ),
			'footer2' => __( 'منوی فوتر — ستون دوم', 'erfan-sanat' ),
		)
	);

	// Image sizes tuned for the card/hero layouts used across the theme.
	add_image_size( 'es-card', 640, 480, true );
	add_image_size( 'es-card-wide', 960, 560, true );
	add_image_size( 'es-hero', 1920, 1080, true );
	add_image_size( 'es-thumb', 320, 320, true );
	add_image_size( 'es-gallery', 1200, 900, false );
}
add_action( 'after_setup_theme', 'es_theme_setup' );

/**
 * Content width for embeds and wide blocks.
 *
 * @return void
 */
function es_content_width() {
	$GLOBALS['content_width'] = 1240;
}
add_action( 'after_setup_theme', 'es_content_width', 0 );

/**
 * Register widget areas used by the sidebar/blog templates.
 *
 * @return void
 */
function es_register_sidebars() {
	register_sidebar(
		array(
			'name'          => __( 'ستون کناری مقالات', 'erfan-sanat' ),
			'id'            => 'es-blog-sidebar',
			'description'   => __( 'ابزارک‌های کنار مقالات و آرشیو بلاگ.', 'erfan-sanat' ),
			'before_widget' => '<section id="%1$s" class="es-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="es-widget__title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'ستون کناری فروشگاه', 'erfan-sanat' ),
			'id'            => 'es-shop-sidebar',
			'description'   => __( 'ابزارک‌های کنار صفحات فروشگاه (آرشیو محصولات).', 'erfan-sanat' ),
			'before_widget' => '<section id="%1$s" class="es-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="es-widget__title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'es_register_sidebars' );

/**
 * Body classes used by the design system (direction, sticky header, layout).
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function es_body_classes( $classes ) {
	$classes[] = 'es-body';
	$classes[] = 'rtl' === es_theme_direction() ? 'es-rtl' : 'es-ltr';

	if ( es_opt( 'header_sticky', true ) ) {
		$classes[] = 'es-sticky-header';
	}

	if ( ! is_active_sidebar( 'es-blog-sidebar' ) && ! is_active_sidebar( 'es-shop-sidebar' ) ) {
		$classes[] = 'es-no-sidebar';
	}

	if ( es_opt( 'header_style', 'dark' ) === 'transparent' && ( is_front_page() || is_page_template( 'templates/template-fullwidth.php' ) ) ) {
		$classes[] = 'es-transparent-header';
	}

	return $classes;
}
add_filter( 'body_class', 'es_body_classes' );

/**
 * Pingback header for single posts.
 *
 * @return void
 */
function es_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'es_pingback_header' );
