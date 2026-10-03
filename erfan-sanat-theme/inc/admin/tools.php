<?php
/**
 * Tools screen: maintenance actions, status report and system information.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registered maintenance actions handled by admin_post_es_theme_tool.
 *
 * @return array<string,string> action => label
 */
function es_tool_actions() {
	return array(
		'flush_rewrites'    => __( 'بازسازی قواعد بازنویسی نشانی‌ها', 'erfan-sanat' ),
		'reset_all'         => __( 'بازنشانی همهٔ تنظیمات قالب', 'erfan-sanat' ),
		'install_demo'      => __( 'ایجاد/به‌روزرسانی داده‌های نمونه (پروژه‌ها، برگه‌ها، مقالات)', 'erfan-sanat' ),
		'install_attributes'=> __( 'ساخت ویژگی‌های سراسری ووکامرس', 'erfan-sanat' ),
		'install_terms'     => __( 'ساخت دسته‌بندی‌های محصول، پروژه و بلاگ', 'erfan-sanat' ),
		'setup_woocommerce' => __( 'ساخت برگه‌های ووکامرس (سبد، تسویه‌حساب، حساب کاربری)', 'erfan-sanat' ),
		'clear_transients'  => __( 'پاک‌سازی داده‌های موقت قالب', 'erfan-sanat' ),
	);
}

/**
 * Tools page markup.
 *
 * @return void
 */
function es_render_tools_page() {
	if ( ! current_user_can( es_admin_capability() ) ) {
		wp_die( esc_html__( 'دسترسی غیرمجاز.', 'erfan-sanat' ), 403 );
	}

	$status = es_theme_status_report();

	?>
	<div class="wrap es-admin" dir="rtl">
		<div class="es-admin__header">
			<div class="es-admin__brand">
				<span class="es-admin__logo" aria-hidden="true"><?php echo es_get_icon( 'tools', 'es-icon', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<div>
					<h1><?php esc_html_e( 'ابزارها و وضعیت قالب', 'erfan-sanat' ); ?></h1>
					<p class="es-admin__subtitle"><?php esc_html_e( 'نگهداری، راه‌اندازی محتوای اولیه و بررسی سلامت نصب.', 'erfan-sanat' ); ?></p>
				</div>
			</div>
		</div>

		<?php es_render_admin_notices(); ?>

		<div class="es-admin__grid">
			<section class="es-panel">
				<div class="es-panel__head">
					<h2><?php esc_html_e( 'وضعیت نصب', 'erfan-sanat' ); ?></h2>
				</div>
				<div class="es-panel__body">
					<ul class="es-status-list">
						<?php foreach ( $status as $row ) : ?>
							<li class="es-status-list__item es-status-list__item--<?php echo esc_attr( $row['state'] ); ?>">
								<span class="es-status-list__dot" aria-hidden="true"></span>
								<strong><?php echo esc_html( $row['label'] ); ?></strong>
								<span><?php echo esc_html( $row['value'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</section>

			<section class="es-panel">
				<div class="es-panel__head">
					<h2><?php esc_html_e( 'عملیات نگهداری', 'erfan-sanat' ); ?></h2>
					<p><?php esc_html_e( 'هر عملیات با nonce و بررسی دسترسی اجرا می‌شود و تکرار آن بی‌خطر (idempotent) است.', 'erfan-sanat' ); ?></p>
				</div>
				<div class="es-panel__body">
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="es-tools-form">
						<input type="hidden" name="action" value="es_theme_tool">
						<?php wp_nonce_field( 'es_theme_tool', 'es_tool_nonce' ); ?>

						<ul class="es-tools-list">
							<?php foreach ( es_tool_actions() as $tool => $label ) : ?>
								<li class="es-tools-list__item">
									<span><?php echo esc_html( $label ); ?></span>
									<button
										type="submit"
										class="button<?php echo 'reset_all' === $tool ? ' button-link-delete' : ' button-secondary'; ?>"
										name="es_tool"
										value="<?php echo esc_attr( $tool ); ?>"
										<?php echo 'reset_all' === $tool ? 'data-confirm="' . esc_attr__( 'همهٔ تنظیمات قالب پاک و به پیش‌فرض بازمی‌گردد. مطمئن هستید؟', 'erfan-sanat' ) . '"' : ''; ?>
									><?php esc_html_e( 'اجرا', 'erfan-sanat' ); ?></button>
								</li>
							<?php endforeach; ?>
						</ul>
					</form>
				</div>
			</section>

			<section class="es-panel">
				<div class="es-panel__head">
					<h2><?php esc_html_e( 'اطلاعات سیستم', 'erfan-sanat' ); ?></h2>
				</div>
				<div class="es-panel__body">
					<table class="widefat striped es-table">
						<tbody>
							<?php foreach ( es_system_info() as $label => $value ) : ?>
								<tr>
									<th scope="row"><?php echo esc_html( $label ); ?></th>
									<td><?php echo esc_html( $value ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</section>
		</div>
	</div>
	<?php
}

/**
 * Human readable system information rows.
 *
 * @return array<string,string>
 */
function es_system_info() {
	$theme  = wp_get_theme();
	$memory = function_exists( 'wp_convert_hr_to_bytes' ) ? wp_convert_hr_to_bytes( WP_MEMORY_LIMIT ) : 0;

	$rows = array(
		__( 'قالب فعال', 'erfan-sanat' )            => $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' ),
		__( 'وردپرس', 'erfan-sanat' )               => get_bloginfo( 'version' ),
		__( 'PHP', 'erfan-sanat' )                  => PHP_VERSION,
		__( 'حد حافظهٔ وردپرس', 'erfan-sanat' )      => size_format( $memory ? $memory : 0 ),
		__( 'محدودیت اجرا', 'erfan-sanat' )          => (string) ini_get( 'max_execution_time' ) . ' ' . __( 'ثانیه', 'erfan-sanat' ),
		__( 'زبان سایت', 'erfan-sanat' )             => get_locale(),
		__( 'جهت قالب', 'erfan-sanat' )              => 'rtl' === es_theme_direction() ? __( 'راست‌به‌چپ (RTL)', 'erfan-sanat' ) : 'LTR',
		__( 'ساختار پیوندها', 'erfan-sanat' )        => get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : __( 'ساده (بدون ساختار)', 'erfan-sanat' ),
		__( 'حالت چندسایتی', 'erfan-sanat' )         => is_multisite() ? __( 'فعال', 'erfan-sanat' ) : __( 'غیرفعال', 'erfan-sanat' ),
		__( 'افزونه‌های فعال', 'erfan-sanat' )       => (string) count( (array) get_option( 'active_plugins', array() ) ),
		__( 'ووکامرس', 'erfan-sanat' )               => es_woocommerce_active() && defined( 'WC_VERSION' ) ? WC_VERSION : __( 'نصب/فعال نیست', 'erfan-sanat' ),
		__( 'تعداد پروژه‌ها', 'erfan-sanat' )        => (string) (int) wp_count_posts( 'project' )->publish,
		__( 'تعداد محصولات', 'erfan-sanat' )         => post_type_exists( 'product' ) ? (string) (int) wp_count_posts( 'product' )->publish : '0',
		__( 'تعداد مقالات', 'erfan-sanat' )          => (string) (int) wp_count_posts( 'post' )->publish,
		__( 'حد اندازهٔ فایل ورودی تنظیمات', 'erfan-sanat' ) => size_format( 512 * KB_IN_BYTES ),
		__( 'تنظیمات قالب (حجم)', 'erfan-sanat' )    => size_format( strlen( (string) maybe_serialize( es_get_options() ) ) ),
	);

	return $rows;
}

/**
 * Handle the maintenance tool requests.
 *
 * @return void
 */
function es_handle_theme_tool() {
	$verify = es_verify_request( 'es_theme_tool', 'es_tool_nonce', es_admin_capability() );

	if ( is_wp_error( $verify ) ) {
		es_redirect_to_tools( array( 'es_status' => 'bad_nonce' ) );
	}

	$tool = isset( $_POST['es_tool'] ) ? sanitize_key( wp_unslash( $_POST['es_tool'] ) ) : '';

	if ( ! array_key_exists( $tool, es_tool_actions() ) ) {
		es_redirect_to_tools( array( 'es_status' => 'invalid' ) );
	}

	$result = es_run_tool( $tool );

	es_redirect_to_tools(
		array(
			'es_status' => $result['status'],
			'es_detail' => isset( $result['detail'] ) ? $result['detail'] : '',
		)
	);
}
add_action( 'admin_post_es_theme_tool', 'es_handle_theme_tool' );

/**
 * Execute a maintenance tool.
 *
 * @param string $tool Tool key.
 * @return array{status:string,detail?:string}
 */
function es_run_tool( $tool ) {
	switch ( $tool ) {
		case 'flush_rewrites':
			flush_rewrite_rules();
			return array( 'status' => 'rewrites' );

		case 'reset_all':
			es_reset_options();
			return array( 'status' => 'reset_all' );

		case 'install_demo':
			$report = es_install_demo_content();
			return array(
				'status' => 'demo',
				'detail' => sprintf(
					/* translators: 1: projects, 2: pages, 3: posts, 4: products */
					__( 'پروژه‌ها: %1$d — برگه‌ها: %2$d — مقالات: %3$d — محصولات: %4$d', 'erfan-sanat' ),
					(int) $report['projects'],
					(int) $report['pages'],
					(int) $report['posts'],
					(int) $report['products']
				),
			);

		case 'install_attributes':
			$created = es_install_product_attributes();
			return array(
				'status' => 'attributes',
				'detail' => sprintf(
					/* translators: %d: number of attributes */
					__( 'تعداد ویژگی‌های ایجادشده/به‌روزشده: %d', 'erfan-sanat' ),
					(int) $created
				),
			);

		case 'install_terms':
			$report = es_install_default_terms();
			return array(
				'status' => 'demo',
				'detail' => sprintf(
					/* translators: 1: product cats, 2: project cats, 3: locations, 4: blog cats */
					__( 'دستهٔ محصول: %1$d — دستهٔ پروژه: %2$d — موقعیت: %3$d — دستهٔ بلاگ: %4$d', 'erfan-sanat' ),
					(int) $report['product_cat'],
					(int) $report['project_cat'],
					(int) $report['project_location'],
					(int) $report['blog_cat']
				),
			);

		case 'setup_woocommerce':
			$created = es_install_woocommerce_pages();
			return array(
				'status' => 'demo',
				'detail' => sprintf(
					/* translators: %d: number of pages */
					__( 'برگه‌های ووکامرس ساخته/به‌روزشده: %d', 'erfan-sanat' ),
					(int) $created
				),
			);

		case 'clear_transients':
			$cleared = es_clear_theme_transients();
			return array(
				'status' => 'saved',
				'detail' => sprintf(
					/* translators: %d: number of transients */
					__( 'تعداد رکورد موقت پاک‌شده: %d', 'erfan-sanat' ),
					(int) $cleared
				),
			);
	}

	return array( 'status' => 'invalid' );
}

/**
 * Remove theme transients (rate limiter buckets and cached reports).
 *
 * @return int Number of deleted rows.
 */
function es_clear_theme_transients() {
	global $wpdb;

	$count = (int) $wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->prepare(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
			$wpdb->esc_like( '_transient_es_' ) . '%',
			$wpdb->esc_like( '_transient_timeout_es_' ) . '%'
		)
	);

	return $count;
}

/**
 * Redirect back to the tools screen.
 *
 * @param array $args Query args.
 * @return void
 */
function es_redirect_to_tools( array $args = array() ) {
	$url = add_query_arg(
		array_merge(
			array( 'page' => es_admin_pages()['tools'] ),
			$args
		),
		admin_url( 'admin.php' )
	);

	es_safe_redirect( $url );
}

/**
 * Print the theme debug log tail on the tools screen when debugging is on.
 *
 * @return void
 */
function es_maybe_render_debug_log() {
	if ( ! es_opt( 'enable_debug', false ) ) {
		return;
	}

	$log = WP_CONTENT_DIR . '/debug.log';

	if ( ! is_readable( $log ) ) {
		return;
	}

	$size  = (int) filesize( $log );
	$lines = array_slice( (array) file( $log ), -25 );

	printf(
		'<section class="es-panel"><div class="es-panel__head"><h2>%1$s</h2><p>%2$s</p></div><div class="es-panel__body"><pre class="es-log">%3$s</pre></div></section>',
		esc_html__( 'آخرین رخدادهای اشکال‌زدایی', 'erfan-sanat' ),
		esc_html( size_format( $size ) ),
		esc_html( implode( '', $lines ) )
	);
}
