<?php
/**
 * Runtime design tokens and dynamic CSS.
 *
 * The whole design system is expressed through CSS custom properties. Values
 * are generated at runtime from the theme options and added as an inline
 * stylesheet through wp_add_inline_style(); nothing is ever written to a
 * physical file, so a cached theme.css can never go stale.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sanitized font family stack for a given option value.
 *
 * @param string $key      Option key.
 * @param string $fallback Fallback stack.
 * @return string
 */
function es_font_stack( $key, $fallback ) {
	$families = es_font_choices();
	$value    = (string) es_opt( $key, '' );

	if ( isset( $families[ $value ]['stack'] ) ) {
		return $families[ $value ]['stack'];
	}

	return $fallback;
}

/**
 * Build the :root token block.
 *
 * @return string
 */
function es_design_tokens_css() {
	$radius     = (float) es_opt( 'radius_base', 18 );
	$container  = (int) es_opt( 'container_width', 1240 );
	$glow       = (float) es_opt( 'glow_strength', 45 ) / 100;
	$overlay    = (float) es_opt( 'hero_overlay_opacity', 72 ) / 100;
	$enable_glow = (bool) es_opt( 'enable_glow', true );

	$tokens = array(
		'--es-primary'       => (string) es_opt( 'color_primary', '#f2b32c' ),
		'--es-secondary'     => (string) es_opt( 'color_secondary', '#1d2536' ),
		'--es-accent'        => (string) es_opt( 'color_accent', '#ffd980' ),
		'--es-background'    => (string) es_opt( 'color_background', '#0a0b0e' ),
		'--es-surface'       => (string) es_opt( 'color_surface', '#14171d' ),
		'--es-surface-alt'   => (string) es_opt( 'color_surface_alt', '#1b202a' ),
		'--es-text'          => (string) es_opt( 'color_text', '#f4f6fa' ),
		'--es-muted'         => (string) es_opt( 'color_muted', '#9aa6b8' ),
		'--es-border'        => (string) es_opt( 'color_border', '#262c38' ),
		'--es-radius'        => $radius . 'px',
		'--es-radius-sm'     => max( 4, round( $radius * 0.5 ) ) . 'px',
		'--es-radius-lg'     => round( $radius * 1.5 ) . 'px',
		'--es-container'     => $container . 'px',
		'--es-font-body'     => es_font_stack( 'font_body', ES_FONT_BODY_STACK ),
		'--es-font-heading'  => es_font_stack( 'font_heading', ES_FONT_HEADING_STACK ),
		'--es-font-size'     => (int) es_opt( 'font_size_base', 16 ) . 'px',
		'--es-font-size-h1'  => (int) es_opt( 'font_size_h1', 52 ) . 'px',
		'--es-heading-weight' => (int) es_opt( 'heading_weight', 800 ),
		'--es-line-height'   => (float) es_opt( 'line_height', 1.85 ),
		'--es-glow'          => $enable_glow ? (string) $glow : '0',
		'--es-hero-overlay'  => (string) $overlay,
		'--es-shadow'        => $enable_glow ? '0 24px 60px -30px rgba(0,0,0,.85)' : 'none',
	);

	/**
	 * Filters the runtime design tokens before they are printed.
	 *
	 * @param array<string,string> $tokens CSS custom property => value.
	 */
	$tokens = apply_filters( 'es_design_tokens', $tokens );

	$css = ':root{';

	foreach ( $tokens as $name => $value ) {
		$css .= $name . ':' . $value . ';';
	}

	$css .= '}';

	return $css;
}

/**
 * Extra runtime rules that depend on options (editor-visible, ratio based).
 *
 * @return string
 */
function es_dynamic_rules_css() {
	$css = '';

	$custom = (string) es_opt( 'custom_css', '' );

	if ( $custom ) {
		// Custom CSS is stored from a textarea and printed as-is (developer field).
		$css .= "\n/* custom css (theme options) */\n" . $custom;
	}

	return $css;
}

/**
 * Inline style handle callback: tokens + dynamic rules.
 *
 * @return string
 */
function es_inline_css() {
	return es_design_tokens_css() . es_dynamic_rules_css();
}

/**
 * Add the generated CSS to the front-end stylesheet.
 *
 * @return void
 */
function es_enqueue_inline_css() {
	$css = es_inline_css();

	if ( ! $css ) {
		return;
	}

	if ( wp_style_is( 'es-theme', 'enqueued' ) ) {
		wp_add_inline_style( 'es-theme', $css );
		return;
	}

	wp_register_style( 'es-inline-tokens', false, array(), ES_THEME_VERSION );
	wp_enqueue_style( 'es-inline-tokens' );
	wp_add_inline_style( 'es-inline-tokens', $css );
}
add_action( 'wp_enqueue_scripts', 'es_enqueue_inline_css', 20 );

/**
 * Editor styles: mirror the same tokens inside the block editor iframe.
 *
 * @return void
 */
function es_enqueue_editor_tokens() {
	$css = es_inline_css();

	if ( ! $css ) {
		return;
	}

	wp_register_style( 'es-editor-tokens', false, array(), ES_THEME_VERSION );
	wp_enqueue_style( 'es-editor-tokens' );
	wp_add_inline_style( 'es-editor-tokens', $css );
}
add_action( 'enqueue_block_editor_assets', 'es_enqueue_editor_tokens' );
