<?php
/**
 * Accessibility layer.
 *
 * The theme targets WCAG 2.1 AA: semantic landmarks, skip link, visible focus,
 * keyboard operable menus, ARIA used only where HTML cannot express state,
 * reduced-motion support and screen-reader friendly dynamic updates.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Skip-to-content link rendered as the first focusable element.
 *
 * @return void
 */
function es_skip_link() {
	printf(
		'<a class="es-skip-link screen-reader-text" href="#es-main">%s</a>',
		esc_html__( 'پرش به محتوای اصلی', 'erfan-sanat' )
	);
}
add_action( 'wp_body_open', 'es_skip_link', 1 );

/**
 * Landmark helper: flag the current nav item for assistive tech.
 *
 * @param array  $classes CSS classes.
 * @param object $item    Menu item.
 * @return array
 */
function es_nav_menu_item_classes( $classes, $item ) {
	if ( in_array( 'current-menu-item', $classes, true ) ) {
		$classes[] = 'es-current';
	}

	if ( ! empty( $item->menu_item_parent ) ) {
		$classes[] = 'es-has-parent';
	}

	return $classes;
}
add_filter( 'nav_menu_css_class', 'es_nav_menu_item_classes', 10, 2 );

/**
 * Add aria attributes required for the dropdown menus.
 *
 * @param string   $output Menu item markup.
 * @param WP_Post  $item   Menu item.
 * @param int      $depth  Depth.
 * @param stdClass $args   Arguments.
 * @return string
 */
function es_nav_menu_link_attributes( $output, $item, $depth, $args ) {
	if ( ! isset( $args->theme_location ) ) {
		return $output;
	}

	if ( in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
		$output .= ' aria-haspopup="true" aria-expanded="false"';
	}

	if ( 0 === $depth ) {
		$output .= ' class="es-nav__link"';
	} else {
		$output .= ' class="es-nav__sublink"';
	}

	return $output;
}
add_filter( 'nav_menu_link_attributes', 'es_nav_menu_link_attributes', 10, 4 );

/**
 * Accessible read-more link for excerpts.
 *
 * @return string
 */
function es_excerpt_more() {
	if ( is_admin() ) {
		return '';
	}

	return '…';
}
add_filter( 'excerpt_more', 'es_excerpt_more' );

/**
 * Search form: keep the semantic label and the theme's class names.
 *
 * @return string
 */
function es_search_form() {
	$unique = wp_unique_id( 'es-search-' );

	return sprintf(
		'<form role="search" method="get" class="es-search-form" action="%1$s">
			<label class="screen-reader-text" for="%2$s">%3$s</label>
			<input id="%2$s" class="es-search-form__input" type="search" name="s" value="%4$s" placeholder="%5$s" required>
			<button class="es-search-form__submit" type="submit">%6$s<span class="screen-reader-text">%7$s</span></button>
		</form>',
		esc_url( home_url( '/' ) ),
		esc_attr( $unique ),
		esc_html__( 'جست‌وجو در سایت', 'erfan-sanat' ),
		esc_attr( get_search_query() ),
		esc_attr__( 'جست‌وجو…', 'erfan-sanat' ),
		es_get_icon( 'search', 'es-icon', 20 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html__( 'جست‌وجو', 'erfan-sanat' )
	);
}
add_filter( 'get_search_form', 'es_search_form' );

/**
 * Comment form: accessible labels and theme styled fields.
 *
 * @param array $args Form arguments.
 * @return array
 */
function es_comment_form_args( $args ) {
	$args['class_submit']          = 'es-btn es-btn--primary';
	$args['title_reply_before']    = '<h3 id="reply-title" class="es-comments__title">';
	$args['title_reply_after']     = '</h3>';
	$args['comment_notes_before']  = '<p class="es-comments__notes">' . esc_html__( 'نشانی ایمیل شما منتشر نخواهد شد. بخش‌های موردنیاز علامت‌گذاری شده‌اند.', 'erfan-sanat' ) . '</p>';
	$args['comment_field']         = '<p class="es-field"><label for="comment" class="es-field__label">' . esc_html__( 'متن دیدگاه', 'erfan-sanat' ) . ' <span aria-hidden="true">*</span></label><textarea id="comment" name="comment" class="es-field__input es-field__input--area" cols="45" rows="6" required aria-required="true"></textarea></p>';

	return $args;
}
add_filter( 'comment_form_defaults', 'es_comment_form_args' );

/**
 * Add a screen-reader description to the comment form for context.
 *
 * @return void
 */
function es_comment_form_accessibility() {
	if ( ! comments_open() ) {
		return;
	}

	echo '<span class="screen-reader-text" id="es-comment-context">' . esc_html__( 'فرم ثبت دیدگاه؛ فیلدهای ستاره‌دار الزامی هستند.', 'erfan-sanat' ) . '</span>';
}
add_action( 'comment_form_top', 'es_comment_form_accessibility' );

/**
 * Declare the theme's reduced-motion friendly default in the html tag.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function es_accessibility_body_classes( $classes ) {
	$classes[] = 'es-a11y';

	return $classes;
}
add_filter( 'body_class', 'es_accessibility_body_classes' );

/**
 * Provide an accessible label for the header cart button.
 *
 * @return string
 */
function es_cart_aria_label() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return __( 'سبد خرید', 'erfan-sanat' );
	}

	$count = (int) WC()->cart->get_cart_contents_count();

	if ( 0 === $count ) {
		return __( 'سبد خرید؛ خالی', 'erfan-sanat' );
	}

	return sprintf(
		/* translators: %s: number of items. */
		_n( 'سبد خرید؛ %s کالا', 'سبد خرید؛ %s کالا', $count, 'erfan-sanat' ),
		esc_html( (string) $count )
	);
}

/**
 * Announce dynamic states (menu open/close, cart updates) to screen readers.
 *
 * @return void
 */
function es_live_region() {
	echo '<div class="es-live-region screen-reader-text" role="status" aria-live="polite" aria-atomic="true"></div>';
}
add_action( 'wp_footer', 'es_live_region', 5 );
