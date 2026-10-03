<?php
/**
 * Theme options storage: one serialized row, cached per request.
 *
 * Storage contract
 *   option_name : erfan_sanat_options   (single row, autoloaded)
 *   shape       : array( option_key => value )
 *   cache       : static per-request array, invalidated on update
 *   versioning  : erfan_sanat_options_version (migration + import/export).
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Per-request static cache holder (kept in a reference-returning function so
 * the cache can be invalidated from es_flush_options_cache()).
 *
 * @return mixed Reference to the cached options (null when not primed).
 */
function &es_options_static_cache() {
	static $options = null;

	return $options;
}

/**
 * Read all theme options (single DB hit per request).
 *
 * @return array<string,mixed>
 */
function es_get_options() {
	$cache = &es_options_static_cache();

	if ( null !== $cache ) {
		return $cache;
	}

	$stored = get_option( ES_OPTIONS_KEY, array() );
	if ( ! is_array( $stored ) ) {
		$stored = array();
	}

	// Schema defaults fill any gap (fresh install, new fields, partial import).
	$cache = array_merge( es_schema_defaults(), $stored );

	return $cache;
}

/**
 * Read a single option with an optional runtime fallback.
 *
 * @param string $key     Option key as defined in the schema.
 * @param mixed  $default Fallback when the key is completely unknown.
 * @return mixed
 */
function es_opt( $key, $default = null ) {
	$options = es_get_options();

	if ( array_key_exists( $key, $options ) ) {
		return $options[ $key ];
	}

	return $default;
}

/**
 * Persist a set of option values (merged into the single row).
 *
 * @param array<string,mixed> $values Key => value pairs (already sanitized).
 * @return bool True when the row changed.
 */
function es_update_options( array $values ) {
	$current = es_get_options();
	$merged  = array_merge( $current, $values );

	if ( $merged === $current ) {
		return false;
	}

	$updated = update_option( ES_OPTIONS_KEY, $merged, true );

	// Invalidate the static cache of this request.
	es_flush_options_cache();

	return (bool) $updated;
}

/**
 * Reset the static options cache (used after writes, imports and in tests).
 *
 * @return void
 */
function es_flush_options_cache() {
	$cache = &es_options_static_cache();
	$cache = null;

	if ( function_exists( 'wp_cache_delete' ) ) {
		wp_cache_delete( ES_OPTIONS_KEY, 'options' );
	}
}

/**
 * Reset every option back to schema defaults.
 *
 * @return void
 */
function es_reset_options() {
	update_option( ES_OPTIONS_KEY, es_schema_defaults(), true );
	es_flush_options_cache();
}

/**
 * Reset a single tab (section) back to schema defaults.
 *
 * @param string $tab Tab slug.
 * @return array<string,mixed> The defaults that were restored.
 */
function es_reset_section( $tab ) {
	$fields   = es_schema_fields( $tab );
	$defaults = array();

	foreach ( $fields as $key => $field ) {
		$defaults[ $key ] = array_key_exists( 'default', $field ) ? $field['default'] : '';
	}

	if ( empty( $defaults ) ) {
		return array();
	}

	es_update_options( $defaults );

	return $defaults;
}

/**
 * Sanitize a single schema value.
 *
 * @param array $field Field definition from the schema.
 * @param mixed $value Raw value.
 * @return mixed Sanitized value.
 */
function es_sanitize_value( array $field, $value ) {
	$strategy = isset( $field['sanitize'] ) ? $field['sanitize'] : 'text';
	$type     = isset( $field['type'] ) ? $field['type'] : 'text';

	switch ( $strategy ) {
		case 'text':
			$value = is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
			break;

		case 'textarea':
			$value = is_scalar( $value ) ? sanitize_textarea_field( (string) $value ) : '';
			break;

		case 'email':
			$value = sanitize_email( is_scalar( $value ) ? (string) $value : '' );
			break;

		case 'url':
			$value = is_scalar( $value ) ? esc_url_raw( trim( (string) $value ) ) : '';
			// Allow in-page anchors (#contact) that esc_url_raw keeps, but strip protocols.
			if ( $value && ! preg_match( '#^(https?:|mailto:|tel:|/|\#)#i', $value ) ) {
				$value = '#' . ltrim( $value, '#' );
			}
			break;

		case 'color':
			$value = is_scalar( $value ) ? sanitize_hex_color( (string) $value ) : '';
			if ( ! $value ) {
				$value = isset( $field['default'] ) ? $field['default'] : '';
			}
			break;

		case 'bool':
			$value = (bool) filter_var( $value, FILTER_VALIDATE_BOOLEAN );
			break;

		case 'int':
			$value = is_numeric( $value ) ? (int) $value : 0;
			if ( isset( $field['min'] ) ) {
				$value = max( (int) $field['min'], $value );
			}
			if ( isset( $field['max'] ) ) {
				$value = min( (int) $field['max'], $value );
			}
			break;

		case 'float':
			$value = is_numeric( $value ) ? (float) $value : 0.0;
			if ( isset( $field['min'] ) ) {
				$value = max( (float) $field['min'], $value );
			}
			if ( isset( $field['max'] ) ) {
				$value = min( (float) $field['max'], $value );
			}
			break;

		case 'key':
			$value   = is_scalar( $value ) ? sanitize_key( (string) $value ) : '';
			$choices = isset( $field['choices'] ) && is_array( $field['choices'] ) ? $field['choices'] : array();
			if ( $choices && ! array_key_exists( $value, $choices ) ) {
				$value = isset( $field['default'] ) ? $field['default'] : key( $choices );
			}
			break;

		case 'keys':
			$value   = array_map( 'sanitize_key', (array) $value );
			$choices = isset( $field['choices'] ) && is_array( $field['choices'] ) ? $field['choices'] : array();
			if ( $choices ) {
				$value = array_values( array_intersect( $value, array_keys( $choices ) ) );
			}
			break;

		case 'html':
			$value = wp_kses_post( (string) $value );
			break;

		case 'html_head':
			// Only users who may post unfiltered HTML can store raw head/footer code.
			if ( ! current_user_can( 'unfiltered_html' ) ) {
				$value = '';
			} else {
				$value = is_string( $value ) ? $value : '';
			}
			break;

		case 'css':
			// CSS is stored verbatim but stripped from any HTML-breaking sequence.
			$value = is_string( $value ) ? $value : '';
			$value = wp_strip_all_tags( $value, true );
			$value = str_replace( array( '</style>', '<style', '<?', '?>' ), '', $value );
			break;

		case 'gallery':
		case 'ids':
			$value = array_filter( array_map( 'absint', (array) $value ) );
			$value = array_values( array_unique( $value ) );
			break;

		case 'csv':
			$value = array_filter( array_map( 'sanitize_text_field', array_map( 'trim', explode( ',', (string) $value ) ) ) );
			$value = array_values( $value );
			break;

		case 'repeater':
			$rows    = is_array( $value ) ? $value : array();
			$sub     = isset( $field['fields'] ) ? (array) $field['fields'] : array();
			$clean   = array();
			foreach ( $rows as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$clean_row = array();
				foreach ( $sub as $sub_field ) {
					$sub_key             = $sub_field['key'];
					$raw                 = array_key_exists( $sub_key, $row ) ? $row[ $sub_key ] : null;
					$clean_row[ $sub_key ] = es_sanitize_value( $sub_field, $raw );
				}
				// Drop rows where every value is empty (removed repeater rows).
				$has_value = false;
				foreach ( $clean_row as $cell ) {
					if ( '' !== $cell && 0 !== $cell && null !== $cell && array() !== $cell && '0' !== $cell ) {
						$has_value = true;
						break;
					}
				}
				if ( $has_value ) {
					$clean[] = $clean_row;
				}
			}
			$value = $clean;
			break;

		case 'raw':
		default:
			$value = is_scalar( $value ) ? (string) $value : '';
			break;
	}

	// Numeric/media fallbacks for empty media ids.
	if ( in_array( $type, array( 'image', 'gallery' ), true ) && '' === $value ) {
		$value = 0;
	}

	return $value;
}

/**
 * Sanitize a raw request/import payload against the schema.
 *
 * @param array       $input Raw input (key => value).
 * @param string|null $tab   Optional tab to restrict the sanitization scope.
 * @param array       $args  {
 *     Optional behaviour flags.
 *
 *     @type bool $form       True when the payload comes from the settings
 *                            form: keys that are missing there (unchecked
 *                            toggles, unchecked checkboxes) are stored as
 *                            false. Imports and partial payloads must never
 *                            reset untouched values, so the default is false.
 *     @type bool $check_caps Whether to honor per-field capability rules.
 * }
 * @return array<string,mixed> Sanitized values (only keys present in $input).
 */
function es_sanitize_options( array $input, $tab = null, array $args = array() ) {
	$form = ! empty( $args['form'] );

	if ( array_key_exists( 'check_caps', $args ) ) {
		$check_caps = (bool) $args['check_caps'];
	} else {
		$check_caps = true;
	}

	$fields = es_schema_fields( $tab );
	$clean  = array();

	foreach ( $fields as $key => $field ) {
		if ( ! array_key_exists( $key, $input ) ) {
			// On a real form submit an absent toggle means "unchecked".
			if ( $form && 'toggle' === $field['type'] ) {
				$clean[ $key ] = false;
			}
			continue;
		}

		if ( $check_caps && ! es_current_user_can_edit_field( $field ) ) {
			continue;
		}

		$clean[ $key ] = es_sanitize_value( $field, $input[ $key ] );
	}

	return $clean;
}

/**
 * Capability check for a single field.
 *
 * @param array $field Field definition.
 * @return bool
 */
function es_current_user_can_edit_field( array $field ) {
	$cap = isset( $field['cap'] ) && $field['cap'] ? $field['cap'] : 'manage_options';

	return current_user_can( $cap );
}

/**
 * Stored schema version.
 *
 * @return string
 */
function es_options_version() {
	return (string) get_option( ES_OPTIONS_VERSION_KEY, '0' );
}

/**
 * Persist the current schema version.
 *
 * @return void
 */
function es_store_options_version() {
	update_option( ES_OPTIONS_VERSION_KEY, es_options_schema()['version'], false );
}

/**
 * Migrate stored options when the schema version advances.
 *
 * Additive migrations only: new fields automatically receive their schema
 * defaults through es_get_options(), so migrations are needed solely for
 * renames/removals.
 *
 * @return void
 */
function es_maybe_migrate_options() {
	if ( version_compare( es_options_version(), es_options_schema()['version'], '>=' ) ) {
		return;
	}

	$stored = get_option( ES_OPTIONS_KEY, array() );
	if ( ! is_array( $stored ) ) {
		$stored = array();
	}

	/**
	 * Filters the raw stored options right before a schema migration.
	 *
	 * @param array $stored Raw stored options.
	 */
	$stored = apply_filters( 'es_migrate_options', $stored );

	update_option( ES_OPTIONS_KEY, array_merge( es_schema_defaults(), $stored ), true );
	es_store_options_version();
	es_flush_options_cache();
}
add_action( 'admin_init', 'es_maybe_migrate_options' );

/**
 * Export the current options as a portable payload.
 *
 * @return array
 */
function es_export_options() {
	return array(
		'format'   => 'erfan-sanat-theme-options',
		'version'  => es_options_schema()['version'],
		'exported' => gmdate( 'c' ),
		'site'     => home_url( '/' ),
		'options'  => es_get_options(),
	);
}

/**
 * Import an options payload, sanitized against the schema.
 *
 * @param array $payload Decoded JSON payload.
 * @param bool  $partial When true, only keys present in the payload are replaced.
 * @return array{imported:int,ignored:int} Report of the import.
 */
function es_import_options( array $payload, $partial = true ) {
	$options = isset( $payload['options'] ) && is_array( $payload['options'] ) ? $payload['options'] : $payload;

	$clean = es_sanitize_options( $options );

	if ( ! $partial ) {
		es_reset_options();
	}

	es_update_options( $clean );
	es_store_options_version();

	return array(
		'imported' => count( $clean ),
		'ignored'  => max( 0, count( $options ) - count( $clean ) ),
	);
}
