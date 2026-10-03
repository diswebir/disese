<?php
/**
 * Erfan Sanat Enterprise theme bootstrap.
 *
 * This file stays intentionally small: it defines the theme constants and loads
 * the modules inside inc/. All behaviour lives in those modules so that the
 * bootstrap never becomes a dumping ground.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Constants
 * ---------------------------------------------------------------------- */
define( 'ES_THEME_VERSION', '1.0.0' );
define( 'ES_THEME_DIR', trailingslashit( get_template_directory() ) );
define( 'ES_THEME_URI', trailingslashit( get_template_directory_uri() ) );
define( 'ES_THEME_SLUG', 'erfan-sanat-theme' );

/** Single serialized option row that stores every theme setting. */
define( 'ES_OPTIONS_KEY', 'erfan_sanat_options' );

/** Schema version row, used for option migrations. */
define( 'ES_OPTIONS_VERSION_KEY', 'erfan_sanat_options_version' );

/** Stored per-post meta prefix used by the content model. */
define( 'ES_META_PREFIX', '_es_' );

/** Local, self-hosted variable font (no Google Fonts, no CDN). */
define( 'ES_FONT_BODY_STACK', "'Vazirmatn', 'Segoe UI', Tahoma, system-ui, -apple-system, sans-serif" );
define( 'ES_FONT_HEADING_STACK', "'Vazirmatn', 'Segoe UI', Tahoma, system-ui, -apple-system, sans-serif" );

/* -------------------------------------------------------------------------
 * Modules (front-end + shared)
 * ---------------------------------------------------------------------- */
$es_modules = array(
	'theme-setup',
	'options-schema',
	'options',
	'template-helpers',
	'meta-fields',
	'post-types',
	'taxonomies',
	'dynamic-css',
	'enqueue',
	'security',
	'performance',
	'seo',
	'accessibility',
	'woocommerce',
	'contact',
	'demo-content',
);

foreach ( $es_modules as $es_module ) {
	$es_module_path = ES_THEME_DIR . 'inc/' . $es_module . '.php';

	if ( is_readable( $es_module_path ) ) {
		require_once $es_module_path;
	}
}

/* -------------------------------------------------------------------------
 * Admin-only modules
 * ---------------------------------------------------------------------- */
if ( is_admin() ) {
	$es_admin_modules = array( 'fields', 'dashboard', 'tools', 'import-export' );

	foreach ( $es_admin_modules as $es_module ) {
		$es_module_path = ES_THEME_DIR . 'inc/admin/' . $es_module . '.php';

		if ( is_readable( $es_module_path ) ) {
			require_once $es_module_path;
		}
	}

	unset( $es_admin_modules, $es_module, $es_module_path );
}

unset( $es_modules, $es_module, $es_module_path );
