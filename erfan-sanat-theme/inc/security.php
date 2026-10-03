<?php
/**
 * Security hardening and shared validation helpers.
 *
 * Every state-changing theme operation (options save, tools, import/export,
 * contact form, meta boxes) routes through the guards defined here so the
 * nonce + capability + sanitization contract is enforced in exactly one place.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Verify a nonce AND a capability for a state-changing request.
 *
 * @param string $action      Nonce action.
 * @param string $query_arg   Request key holding the nonce.
 * @param string $capability  Capability required.
 * @return true|WP_Error True when the request is trusted.
 */
/**
 * Verify a nonce for public (possibly logged-out) form submissions.
 *
 * Capability checks are deliberately not part of this helper: forms like the
 * contact form are open to visitors, so only the nonce is validated here.
 *
 * @param string $action Nonce action.
 * @param string $field  Request key holding the nonce.
 * @return true|WP_Error
 */
function es_verify_nonce( $action, $field ) {
	$nonce = isset( $_REQUEST[ $field ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $field ] ) ) : '';

	if ( ! $nonce || ! wp_verify_nonce( $nonce, $action ) ) {
		return new WP_Error( 'es_bad_nonce', __( 'اعتبار درخواست تأیید نشد؛ لطفاً صفحه را دوباره بارگذاری کنید.', 'erfan-sanat' ), array( 'status' => 403 ) );
	}

	return true;
}

/**
 * Verify a state-changing admin request: nonce + capability.
 *
 * @param string $action     Nonce action.
 * @param string $query_arg  Request key holding the nonce.
 * @param string $capability Required capability.
 * @return true|WP_Error
 */
function es_verify_request( $action, $query_arg, $capability = 'manage_options' ) {
	if ( ! is_user_logged_in() ) {
		return new WP_Error( 'es_not_logged_in', __( 'برای انجام این عملیات باید وارد شوید.', 'erfan-sanat' ), array( 'status' => 401 ) );
	}

	if ( ! current_user_can( $capability ) ) {
		return new WP_Error( 'es_forbidden', __( 'شما دسترسی لازم برای این عملیات را ندارید.', 'erfan-sanat' ), array( 'status' => 403 ) );
	}

	$nonce = isset( $_REQUEST[ $query_arg ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $query_arg ] ) ) : '';

	if ( ! $nonce || ! wp_verify_nonce( $nonce, $action ) ) {
		return new WP_Error( 'es_bad_nonce', __( 'اعتبار درخواست تأیید نشد؛ لطفاً صفحه را دوباره بارگذاری کنید.', 'erfan-sanat' ), array( 'status' => 403 ) );
	}

	return true;
}

/**
 * Redirect safely and stop execution (safe hosts only).
 *
 * @param string $url  Destination.
 * @param int    $code HTTP status, 301/302/303.
 * @return void
 */
function es_safe_redirect( $url, $code = 302 ) {
	$code = in_array( (int) $code, array( 301, 302, 303 ), true ) ? (int) $code : 302;
	wp_safe_redirect( $url, $code );
	exit;
}

/**
 * Simple per-IP rate limiter backed by transients.
 *
 * @param string $bucket  Logical bucket name (e.g. "contact").
 * @param int    $limit   Maximum attempts inside the window.
 * @param int    $window  Window length in seconds.
 * @return bool True when the caller is still allowed to proceed.
 */
function es_rate_limit_ok( $bucket, $limit = 3, $window = MINUTE_IN_SECONDS ) {
	$limit = max( 1, (int) $limit );
	$ip    = es_get_client_ip();

	$key   = 'es_rl_' . md5( $bucket . '|' . $ip );
	$hits  = (int) get_transient( $key );

	if ( $hits >= $limit ) {
		return false;
	}

	set_transient( $key, $hits + 1, max( 10, (int) $window ) );

	return true;
}

/**
 * Best-effort client IP for rate limiting (never trusted for authorization).
 *
 * @return string
 */
function es_get_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

	return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '0.0.0.0';
}

/**
 * Strip a value down to a plain, single-line string.
 *
 * @param mixed $value Raw value.
 * @return string
 */
function es_clean_text( $value ) {
	return is_scalar( $value ) ? trim( sanitize_text_field( (string) $value ) ) : '';
}

/**
 * Normalize user supplied URLs (anchors, relative paths and full URLs).
 *
 * @param mixed $value Raw value.
 * @return string
 */
function es_clean_url( $value ) {
	if ( ! is_scalar( $value ) ) {
		return '';
	}

	$value = trim( (string) $value );

	if ( '' === $value ) {
		return '';
	}

	if ( preg_match( '#^(https?:|mailto:|tel:)#i', $value ) ) {
		return esc_url_raw( $value );
	}

	if ( 0 === strpos( $value, '#' ) ) {
		$fragment = preg_replace( '/[^A-Za-z0-9_\-]/', '', ltrim( $value, '#' ) );

		return $fragment ? '#' . $fragment : '';
	}

	return esc_url_raw( $value );
}

/**
 * Only allow inline SVG markup that cannot execute script.
 *
 * @param string $svg Raw SVG markup.
 * @return string Safe SVG markup or an empty string.
 */
function es_kses_svg( $svg ) {
	$allowed = array(
		'svg'      => array(
			'xmlns'       => true,
			'viewbox'     => true,
			'width'       => true,
			'height'      => true,
			'fill'        => true,
			'stroke'      => true,
			'stroke-width'=> true,
			'class'       => true,
			'aria-hidden' => true,
			'role'        => true,
			'focusable'   => true,
		),
		'path'     => array( 'd' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'fill-rule' => true, 'clip-rule' => true ),
		'circle'   => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true ),
		'rect'     => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'fill' => true ),
		'g'        => array( 'fill' => true, 'stroke' => true, 'transform' => true ),
		'line'     => array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true ),
		'polyline' => array( 'points' => true, 'fill' => true, 'stroke' => true ),
		'polygon'  => array( 'points' => true, 'fill' => true, 'stroke' => true ),
		'defs'     => array(),
		'linearGradient' => array( 'id' => true, 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true ),
		'stop'     => array( 'offset' => true, 'stop-color' => true, 'stop-opacity' => true ),
	);

	return wp_kses( (string) $svg, $allowed );
}

/**
 * Send conservative security headers on theme-rendered front-end responses.
 *
 * @return void
 */
function es_send_security_headers() {
	if ( is_admin() || wp_doing_ajax() || headers_sent() ) {
		return;
	}

	header( 'X-Content-Type-Options: nosniff' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );
}
add_action( 'template_redirect', 'es_send_security_headers' );

/**
 * Remove the WordPress version fingerprint from the head and feeds.
 *
 * @return string
 */
function es_hide_generator_version() {
	return '';
}
add_filter( 'the_generator', 'es_hide_generator_version' );

/**
 * Generic login error message (do not reveal whether a username exists).
 *
 * @return string
 */
function es_login_error_message() {
	return __( 'نام کاربری یا گذرواژه نادرست است.', 'erfan-sanat' );
}
add_filter( 'login_errors', 'es_login_error_message' );

/**
 * Only allow SVG uploads when the site owner explicitly enabled them.
 *
 * @param array $mimes Allowed mime types.
 * @return array
 */
function es_filter_upload_mimes( $mimes ) {
	if ( es_opt( 'allow_svg_upload', false ) && current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	} else {
		unset( $mimes['svg'], $mimes['svgz'] );
	}

	return $mimes;
}
add_filter( 'upload_mimes', 'es_filter_upload_mimes' );

/**
 * Escape a value for safe use inside an HTML attribute of a link.
 *
 * @param string $value Raw attribute value.
 * @return string
 */
function es_esc_url_attr( $value ) {
	return esc_url( es_clean_url( $value ) );
}
