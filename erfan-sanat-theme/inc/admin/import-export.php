<?php
/**
 * Import / export of theme settings as JSON.
 *
 * The importer is schema driven: unknown keys are ignored and every accepted
 * value goes through the same sanitizer the settings screen uses.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Maximum accepted import size (512 KB), enforced on top of PHP limits.
 */
define( 'ES_IMPORT_MAX_BYTES', 524288 );

/**
 * Import/export screen.
 *
 * @return void
 */
function es_render_import_export_page() {
	if ( ! current_user_can( es_admin_capability() ) ) {
		wp_die( esc_html__( 'دسترسی غیرمجاز.', 'erfan-sanat' ), 403 );
	}

	$export_url = wp_nonce_url(
		add_query_arg(
			array(
				'action' => 'es_export_options',
			),
			admin_url( 'admin-post.php' )
		),
		'es_export_options',
		'es_io_nonce'
	);

	?>
	<div class="wrap es-admin" dir="rtl">
		<div class="es-admin__header">
			<div class="es-admin__brand">
				<span class="es-admin__logo" aria-hidden="true"><?php echo es_get_icon( 'download', 'es-icon', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<div>
					<h1><?php esc_html_e( 'پشتیبان‌گیری و انتقال تنظیمات', 'erfan-sanat' ); ?></h1>
					<p class="es-admin__subtitle"><?php esc_html_e( 'خروجی JSON از تنظیمات قالب و درون‌ریزی آن روی نصب دیگر.', 'erfan-sanat' ); ?></p>
				</div>
			</div>
		</div>

		<?php es_render_admin_notices(); ?>

		<div class="es-admin__grid">
			<section class="es-panel">
				<div class="es-panel__head">
					<h2><?php esc_html_e( 'خروجی گرفتن', 'erfan-sanat' ); ?></h2>
					<p><?php esc_html_e( 'فایل JSON شامل تمام مقادیر تنظیمات، نسخهٔ اسکیما و تاریخ خروجی است.', 'erfan-sanat' ); ?></p>
				</div>
				<div class="es-panel__body">
					<p>
						<a class="button button-primary" href="<?php echo esc_url( $export_url ); ?>">
							<?php esc_html_e( 'دانلود فایل پشتیبان (JSON)', 'erfan-sanat' ); ?>
						</a>
					</p>
					<p class="es-field__desc">
						<?php
						printf(
							/* translators: %s: file size */
							esc_html__( 'حجم تقریبی فایل: %s', 'erfan-sanat' ),
							esc_html( size_format( strlen( (string) wp_json_encode( es_export_options() ) ) ) )
						);
						?>
					</p>
				</div>
			</section>

			<section class="es-panel">
				<div class="es-panel__head">
					<h2><?php esc_html_e( 'درون‌ریزی', 'erfan-sanat' ); ?></h2>
					<p><?php esc_html_e( 'فایل JSON را بارگذاری کنید یا متن آن را در کادر زیر بچسبانید.', 'erfan-sanat' ); ?></p>
				</div>
				<div class="es-panel__body">
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
						<input type="hidden" name="action" value="es_import_options">
						<?php wp_nonce_field( 'es_import_options', 'es_io_nonce' ); ?>

						<p class="es-field">
							<label class="es-field__label" for="es-import-file"><?php esc_html_e( 'فایل JSON', 'erfan-sanat' ); ?></label>
							<input type="file" id="es-import-file" name="es_import_file" accept="application/json,.json">
						</p>

						<p class="es-field">
							<label class="es-field__label" for="es-import-json"><?php esc_html_e( 'یا متن JSON', 'erfan-sanat' ); ?></label>
							<textarea id="es-import-json" name="es_import_json" class="es-input es-input--area" rows="8" dir="ltr" placeholder='{"format":"erfan-sanat-theme-options","options":{"color_primary":"#f2b32c"}}'></textarea>
						</p>

						<p class="es-field es-field--toggle">
							<label class="es-switch" for="es-import-partial">
								<input type="checkbox" id="es-import-partial" name="es_import_partial" value="1" checked>
								<span class="es-switch__track" aria-hidden="true"><span class="es-switch__thumb"></span></span>
								<span class="es-switch__text"><?php esc_html_e( 'درون‌ریزی جزئی (فقط کلیدهای موجود، بقیه دست‌نخورده)', 'erfan-sanat' ); ?></span>
							</label>
						</p>

						<p>
							<button type="submit" class="button button-primary"><?php esc_html_e( 'درون‌ریزی تنظیمات', 'erfan-sanat' ); ?></button>
						</p>

						<p class="es-field__desc">
							<?php esc_html_e( 'تنها کلیدهایی که در اسکیمای قالب تعریف شده‌اند پذیرفته می‌شوند؛ کلیدهای ناشناخته نادیده گرفته می‌شوند.', 'erfan-sanat' ); ?>
						</p>
					</form>
				</div>
			</section>
		</div>
	</div>
	<?php
}

/**
 * Stream the options export as a JSON download.
 *
 * @return void
 */
function es_handle_export_options() {
	$verify = es_verify_request( 'es_export_options', 'es_io_nonce', es_admin_capability() );

	if ( is_wp_error( $verify ) ) {
		es_redirect_to_tools( array( 'es_status' => 'bad_nonce' ) );
	}

	$payload = es_export_options();
	$json    = wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
	$name    = sprintf( 'erfan-sanat-options-%s.json', gmdate( 'Ymd-His' ) );

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=' . $name );
	header( 'Content-Length: ' . strlen( (string) $json ) );

	echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON download body.
	exit;
}
add_action( 'admin_post_es_export_options', 'es_handle_export_options' );

/**
 * Handle an options import (uploaded file or pasted JSON).
 *
 * @return void
 */
function es_handle_import_options() {
	$verify = es_verify_request( 'es_import_options', 'es_io_nonce', es_admin_capability() );

	if ( is_wp_error( $verify ) ) {
		es_redirect_to_io( array( 'es_status' => 'bad_nonce' ) );
	}

	$json = '';

	// 1) Uploaded file (validated: size, upload errors, JSON content type).
	if ( ! empty( $_FILES['es_import_file']['tmp_name'] ) && is_uploaded_file( $_FILES['es_import_file']['tmp_name'] ) ) {
		$file = $_FILES['es_import_file']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- validated below.

		if ( ! empty( $file['error'] ) && UPLOAD_ERR_OK !== (int) $file['error'] ) {
			es_redirect_to_io( array( 'es_status' => 'invalid', 'es_detail' => __( 'بارگذاری فایل ناموفق بود.', 'erfan-sanat' ) ) );
		}

		if ( (int) $file['size'] > ES_IMPORT_MAX_BYTES ) {
			es_redirect_to_io( array( 'es_status' => 'invalid', 'es_detail' => __( 'حجم فایل بیش از حد مجاز (۵۱۲ کیلوبایت) است.', 'erfan-sanat' ) ) );
		}

		$contents = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local uploaded temp file.

		if ( false !== $contents ) {
			$json = (string) $contents;
		}
	}

	// 2) Pasted JSON.
	if ( '' === $json && isset( $_POST['es_import_json'] ) ) {
		$raw = wp_unslash( $_POST['es_import_json'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON decoded and sanitized below.
		$raw = is_string( $raw ) ? trim( $raw ) : '';

		if ( strlen( $raw ) > ES_IMPORT_MAX_BYTES ) {
			es_redirect_to_io( array( 'es_status' => 'invalid', 'es_detail' => __( 'حجم متن ورودی بیش از حد مجاز است.', 'erfan-sanat' ) ) );
		}

		$json = $raw;
	}

	if ( '' === $json ) {
		es_redirect_to_io( array( 'es_status' => 'invalid', 'es_detail' => __( 'فایل یا متنی برای درون‌ریزی ارسال نشد.', 'erfan-sanat' ) ) );
	}

	$decoded = json_decode( $json, true );

	if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $decoded ) ) {
		es_redirect_to_io( array( 'es_status' => 'invalid', 'es_detail' => __( 'ساختار JSON معتبر نیست.', 'erfan-sanat' ) ) );
	}

	$partial = ! empty( $_POST['es_import_partial'] );
	$report  = es_import_options( $decoded, $partial );

	/**
	 * Fires after theme options were imported.
	 *
	 * @param array $report  Import report.
	 * @param array $decoded Decoded payload.
	 */
	do_action( 'es_options_imported', $report, $decoded );

	flush_rewrite_rules();

	es_redirect_to_io(
		array(
			'es_status' => 'imported',
			'es_detail' => sprintf(
				/* translators: 1: imported count, 2: ignored count */
				__( 'تعداد کلید درون‌ریزی‌شده: %1$d — نادیده گرفته‌شده: %2$d', 'erfan-sanat' ),
				(int) $report['imported'],
				(int) $report['ignored']
			),
		)
	);
}
add_action( 'admin_post_es_import_options', 'es_handle_import_options' );

/**
 * Redirect back to the import/export screen.
 *
 * @param array $args Query args.
 * @return void
 */
function es_redirect_to_io( array $args = array() ) {
	$url = add_query_arg(
		array_merge(
			array( 'page' => es_admin_pages()['io'] ),
			$args
		),
		admin_url( 'admin.php' )
	);

	es_safe_redirect( $url );
}
