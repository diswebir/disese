<?php
/**
 * Theme admin panel: menu, tabbed settings screen, save handler, notices.
 *
 * Everything the screen renders comes from inc/options-schema.php through the
 * generic renderer in inc/admin/fields.php.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Capability required to manage the theme panel.
 *
 * @return string
 */
function es_admin_capability() {
	return (string) apply_filters( 'es_admin_capability', 'manage_options' );
}

/**
 * Registered admin page slugs.
 *
 * @return array<string,string>
 */
function es_admin_pages() {
	return array(
		'settings' => ES_THEME_SLUG . '-settings',
		'tools'    => ES_THEME_SLUG . '-tools',
		'io'       => ES_THEME_SLUG . '-import-export',
	);
}

/**
 * Register the top-level theme menu and its sub pages.
 *
 * @return void
 */
function es_register_admin_menu() {
	$cap   = es_admin_capability();
	$pages = es_admin_pages();

	add_menu_page(
		__( 'تنظیمات قالب عرفان صنعت', 'erfan-sanat' ),
		__( 'قالب عرفان صنعت', 'erfan-sanat' ),
		$cap,
		$pages['settings'],
		'es_render_settings_page',
		'dashicons-lightbulb',
		(int) es_opt( 'menu_priority', 59 )
	);

	add_submenu_page(
		$pages['settings'],
		__( 'تنظیمات قالب', 'erfan-sanat' ),
		__( 'تنظیمات قالب', 'erfan-sanat' ),
		$cap,
		$pages['settings'],
		'es_render_settings_page'
	);

	add_submenu_page(
		$pages['settings'],
		__( 'ابزارها و وضعیت', 'erfan-sanat' ),
		__( 'ابزارها و وضعیت', 'erfan-sanat' ),
		$cap,
		$pages['tools'],
		'es_render_tools_page'
	);

	add_submenu_page(
		$pages['settings'],
		__( 'پشتیبان‌گیری تنظیمات', 'erfan-sanat' ),
		__( 'پشتیبان‌گیری تنظیمات', 'erfan-sanat' ),
		$cap,
		$pages['io'],
		'es_render_import_export_page'
	);
}
add_action( 'admin_menu', 'es_register_admin_menu' );

/**
 * Current settings tab (validated against the schema).
 *
 * @return string
 */
function es_current_tab() {
	$tabs = es_schema_tabs();
	$tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only navigation.

	if ( ! $tab || ! isset( $tabs[ $tab ] ) ) {
		$tab = (string) key( $tabs );
	}

	return $tab;
}

/**
 * Settings page wrapper.
 *
 * @return void
 */
function es_render_settings_page() {
	if ( ! current_user_can( es_admin_capability() ) ) {
		wp_die( esc_html__( 'شما دسترسی لازم برای مشاهدهٔ این صفحه را ندارید.', 'erfan-sanat' ), 403 );
	}

	$tab   = es_current_tab();
	$tabs  = es_options_schema()['tabs'];
	$data  = isset( $tabs[ $tab ] ) ? $tabs[ $tab ] : array();
	$opts  = es_get_options();

	?>
	<div class="wrap es-admin" dir="rtl">
		<div class="es-admin__header">
			<div class="es-admin__brand">
				<span class="es-admin__logo" aria-hidden="true"><?php echo es_get_icon( 'spark', 'es-icon', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<div>
					<h1><?php esc_html_e( 'قالب عرفان صنعت', 'erfan-sanat' ); ?></h1>
					<p class="es-admin__subtitle"><?php esc_html_e( 'پنل مدیریت قالب سازمانی نورپردازی شهری', 'erfan-sanat' ); ?></p>
				</div>
			</div>
			<div class="es-admin__meta">
				<span class="es-badge"><?php echo esc_html( sprintf( /* translators: %s: theme version */ __( 'نسخهٔ %s', 'erfan-sanat' ), ES_THEME_VERSION ) ); ?></span>
				<a class="button button-secondary" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'مشاهدهٔ سایت', 'erfan-sanat' ); ?></a>
			</div>
		</div>

		<?php es_render_admin_notices(); ?>

		<nav class="es-admin__tabs" aria-label="<?php esc_attr_e( 'بخش‌های تنظیمات', 'erfan-sanat' ); ?>">
			<?php foreach ( es_schema_tabs() as $slug => $label ) : ?>
				<?php
				$url = add_query_arg(
					array(
						'page' => es_admin_pages()['settings'],
						'tab'  => $slug,
					),
					admin_url( 'admin.php' )
				);
				?>
				<a
					class="es-admin__tab<?php echo $slug === $tab ? ' is-active' : ''; ?>"
					href="<?php echo esc_url( $url ); ?>"
					<?php echo $slug === $tab ? 'aria-current="page"' : ''; ?>
				>
					<?php if ( ! empty( $tabs[ $slug ]['icon'] ) ) : ?>
						<span class="dashicons <?php echo esc_attr( $tabs[ $slug ]['icon'] ); ?>" aria-hidden="true"></span>
					<?php endif; ?>
					<span><?php echo esc_html( $label ); ?></span>
				</a>
			<?php endforeach; ?>
		</nav>

		<form class="es-admin__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="es_save_options">
			<input type="hidden" name="es_tab" value="<?php echo esc_attr( $tab ); ?>">
			<?php wp_nonce_field( 'es_save_options_' . $tab, 'es_options_nonce' ); ?>

			<div class="es-panel">
				<div class="es-panel__head">
					<h2><?php echo esc_html( isset( $data['label'] ) ? $data['label'] : '' ); ?></h2>
					<?php if ( ! empty( $data['description'] ) ) : ?>
						<p><?php echo esc_html( $data['description'] ); ?></p>
					<?php endif; ?>
				</div>

				<div class="es-panel__body">
					<?php
					if ( ! empty( $data['fields'] ) ) {
						foreach ( $data['fields'] as $field ) {
							$value = array_key_exists( $field['key'], $opts ) ? $opts[ $field['key'] ] : ( isset( $field['default'] ) ? $field['default'] : '' );

							if ( ! es_current_user_can_edit_field( $field ) ) {
								continue;
							}

							es_field_row(
								$field,
								$value,
								array(
									'name'    => ES_OPTIONS_KEY . '[' . $field['key'] . ']',
									'id'      => 'es-option-' . $field['key'],
									'context' => 'options',
								)
							);
						}
					}
					?>
				</div>
			</div>

			<div class="es-admin__actions">
				<?php submit_button( __( 'ذخیرهٔ تنظیمات', 'erfan-sanat' ), 'primary', 'submit', false ); ?>

				<button
					type="submit"
					class="button button-link-delete es-reset-section"
					name="es_reset_section"
					value="1"
					data-confirm="<?php esc_attr_e( 'تمام فیلدهای این بخش به مقادیر پیش‌فرض بازمی‌گردند. ادامه می‌دهید؟', 'erfan-sanat' ); ?>"
				><?php esc_html_e( 'بازنشانی این بخش', 'erfan-sanat' ); ?></button>

				<span class="es-admin__hint"><?php esc_html_e( 'تنظیمات در یک رکورد واحد ذخیره می‌شوند و بین درخواست‌ها کش می‌شوند.', 'erfan-sanat' ); ?></span>
			</div>
		</form>
	</div>
	<?php
}

/**
 * Admin notices (success / error / validation feedback).
 *
 * @return void
 */
function es_render_admin_notices() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only flash messages.
	$status = isset( $_GET['es_status'] ) ? sanitize_key( wp_unslash( $_GET['es_status'] ) ) : '';
	$detail = isset( $_GET['es_detail'] ) ? sanitize_text_field( rawurldecode( wp_unslash( $_GET['es_detail'] ) ) ) : '';
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	if ( ! $status ) {
		return;
	}

	$messages = array(
		'saved'         => __( 'تنظیمات با موفقیت ذخیره شد.', 'erfan-sanat' ),
		'reset_section' => __( 'بخش انتخابی به مقادیر پیش‌فرض بازگشت.', 'erfan-sanat' ),
		'reset_all'     => __( 'تمام تنظیمات قالب به حالت پیش‌فرض بازگشت.', 'erfan-sanat' ),
		'imported'      => __( 'تنظیمات با موفقیت درون‌ریزی شد.', 'erfan-sanat' ),
		'exported'      => __( 'فایل پشتیبان آمادهٔ دانلود است.', 'erfan-sanat' ),
		'denied'        => __( 'دسترسی لازم برای این عملیات وجود ندارد.', 'erfan-sanat' ),
		'bad_nonce'     => __( 'اعتبار درخواست تأیید نشد. لطفاً دوباره تلاش کنید.', 'erfan-sanat' ),
		'invalid'       => __( 'دادهٔ ورودی نامعتبر بود؛ تنها فیلدهای معتبر ذخیره شدند.', 'erfan-sanat' ),
		'rewrites'      => __( 'قواعد بازنویسی نشانی‌ها بازسازی شد.', 'erfan-sanat' ),
		'demo'          => __( 'داده‌های نمونه ایجاد/به‌روزرسانی شد.', 'erfan-sanat' ),
		'attributes'    => __( 'ویژگی‌های سراسری ووکامرس ساخته شد.', 'erfan-sanat' ),
	);

	$type = in_array( $status, array( 'denied', 'bad_nonce', 'invalid' ), true ) ? 'error' : 'success';
	$text = isset( $messages[ $status ] ) ? $messages[ $status ] : __( 'عملیات انجام شد.', 'erfan-sanat' );

	printf(
		'<div class="notice notice-%1$s is-dismissible es-notice"><p>%2$s</p></div>',
		esc_attr( $type ),
		esc_html( $text )
	);

	if ( $detail ) {
		printf( '<div class="notice notice-info es-notice"><p>%s</p></div>', esc_html( $detail ) );
	}
}

/**
 * Handle the settings form submission.
 *
 * Guards: logged-in, capability, nonce, sanitization, error handling, safe redirect.
 *
 * @return void
 */
function es_handle_save_options() {
	$cap  = es_admin_capability();
	$tab  = isset( $_POST['es_tab'] ) ? sanitize_key( wp_unslash( $_POST['es_tab'] ) ) : '';
	$tabs = es_schema_tabs();
	$tab  = isset( $tabs[ $tab ] ) ? $tab : (string) key( $tabs );

	$verify = es_verify_request( 'es_save_options_' . $tab, 'es_options_nonce', $cap );

	if ( is_wp_error( $verify ) ) {
		es_redirect_to_settings(
			$tab,
			array(
				'es_status' => 'bad_nonce',
				'es_detail' => $verify->get_error_message(),
			)
		);
	}

	// Section reset.
	if ( ! empty( $_POST['es_reset_section'] ) ) {
		es_reset_section( $tab );
		es_redirect_to_settings( $tab, array( 'es_status' => 'reset_section' ) );
	}

	$raw   = isset( $_POST[ ES_OPTIONS_KEY ] ) ? (array) wp_unslash( $_POST[ ES_OPTIONS_KEY ] ) : array();
	$clean = es_sanitize_options( $raw, $tab, array( 'form' => true ) );

	es_update_options( $clean );
	es_store_options_version();

	/**
	 * Fires after a tab of options was saved.
	 *
	 * @param string $tab   Saved tab slug.
	 * @param array  $clean Sanitized values.
	 */
	do_action( 'es_options_saved', $tab, $clean );

	es_redirect_to_settings( $tab, array( 'es_status' => 'saved' ) );
}
add_action( 'admin_post_es_save_options', 'es_handle_save_options' );

/**
 * Redirect back to the settings screen with flash arguments.
 *
 * @param string $tab   Tab slug.
 * @param array  $args  Extra query args.
 * @return void
 */
function es_redirect_to_settings( $tab, array $args = array() ) {
	$url = add_query_arg(
		array_merge(
			array(
				'page' => es_admin_pages()['settings'],
				'tab'  => $tab,
			),
			$args
		),
		admin_url( 'admin.php' )
	);

	es_safe_redirect( $url );
}

/**
 * Dashboard widget: quick status of the theme configuration.
 *
 * @return void
 */
function es_register_dashboard_widget() {
	if ( ! current_user_can( es_admin_capability() ) ) {
		return;
	}

	wp_add_dashboard_widget(
		'es_theme_status',
		__( 'وضعیت قالب عرفان صنعت', 'erfan-sanat' ),
		'es_render_dashboard_widget'
	);
}
add_action( 'wp_dashboard_setup', 'es_register_dashboard_widget' );

/**
 * Dashboard widget markup.
 *
 * @return void
 */
function es_render_dashboard_widget() {
	$status = es_theme_status_report();

	echo '<ul class="es-status-list">';

	foreach ( $status as $row ) {
		printf(
			'<li class="es-status-list__item es-status-list__item--%1$s"><span class="es-status-list__dot" aria-hidden="true"></span><strong>%2$s</strong><span>%3$s</span></li>',
			esc_attr( $row['state'] ),
			esc_html( $row['label'] ),
			esc_html( $row['value'] )
		);
	}

	echo '</ul>';

	printf(
		'<p><a class="button button-primary" href="%1$s">%2$s</a> <a class="button" href="%3$s">%4$s</a></p>',
		esc_url( add_query_arg( 'page', es_admin_pages()['settings'], admin_url( 'admin.php' ) ) ),
		esc_html__( 'تنظیمات قالب', 'erfan-sanat' ),
		esc_url( add_query_arg( 'page', es_admin_pages()['tools'], admin_url( 'admin.php' ) ) ),
		esc_html__( 'ابزارها', 'erfan-sanat' )
	);
}

/**
 * Compact status report shared by the dashboard widget and the tools page.
 *
 * @return array<int,array{label:string,value:string,state:string}>
 */
function es_theme_status_report() {
	$report = array(
		array(
			'label' => __( 'نسخهٔ قالب', 'erfan-sanat' ),
			'value' => ES_THEME_VERSION,
			'state' => 'ok',
		),
		array(
			'label' => __( 'نسخهٔ وردپرس', 'erfan-sanat' ),
			'value' => get_bloginfo( 'version' ),
			'state' => version_compare( get_bloginfo( 'version' ), '6.4', '>=' ) ? 'ok' : 'warn',
		),
		array(
			'label' => __( 'نسخهٔ PHP', 'erfan-sanat' ),
			'value' => PHP_VERSION,
			'state' => version_compare( PHP_VERSION, '7.4', '>=' ) ? 'ok' : 'warn',
		),
		array(
			'label' => __( 'ووکامرس', 'erfan-sanat' ),
			'value' => es_woocommerce_active() ? __( 'فعال', 'erfan-sanat' ) : __( 'غیرفعال', 'erfan-sanat' ),
			'state' => es_woocommerce_active() ? 'ok' : 'warn',
		),
		array(
			'label' => __( 'نوع دادهٔ پروژه', 'erfan-sanat' ),
			'value' => post_type_exists( 'project' ) ? __( 'ثبت‌شده', 'erfan-sanat' ) : __( 'ثبت نشده', 'erfan-sanat' ),
			'state' => post_type_exists( 'project' ) ? 'ok' : 'warn',
		),
		array(
			'label' => __( 'تعداد فیلدهای تنظیمات', 'erfan-sanat' ),
			'value' => (string) count( es_schema_fields() ),
			'state' => 'ok',
		),
		array(
			'label' => __( 'نسخهٔ اسکیما', 'erfan-sanat' ),
			'value' => esc_html( es_options_version() ? es_options_version() : '—' ),
			'state' => version_compare( es_options_version(), es_options_schema()['version'], '>=' ) ? 'ok' : 'warn',
		),
	);

	if ( function_exists( 'wp_doing_cron' ) && ! es_opt( 'enable_breadcrumbs', true ) ) {
		$report[] = array(
			'label' => __( 'مسیر راهنما', 'erfan-sanat' ),
			'value' => __( 'غیرفعال', 'erfan-sanat' ),
			'state' => 'warn',
		);
	}

	return $report;
}

/**
 * Add a settings shortcut to the admin bar.
 *
 * @param WP_Admin_Bar $bar Admin bar instance.
 * @return void
 */
function es_admin_bar_settings_link( $bar ) {
	if ( ! current_user_can( es_admin_capability() ) ) {
		return;
	}

	$bar->add_node(
		array(
			'id'    => 'es-theme-settings',
			'title' => __( 'تنظیمات قالب عرفان صنعت', 'erfan-sanat' ),
			'href'  => add_query_arg( 'page', es_admin_pages()['settings'], admin_url( 'admin.php' ) ),
			'meta'  => array( 'title' => __( 'تنظیمات قالب', 'erfan-sanat' ) ),
		)
	);
}
add_action( 'admin_bar_menu', 'es_admin_bar_settings_link', 100 );

/**
 * "Settings" row action on the theme card in Appearance → Themes.
 *
 * @param array    $actions Existing actions.
 * @param WP_Theme $theme   Theme object.
 * @return array
 */
function es_theme_action_links( $actions, $theme ) {
	if ( ES_THEME_SLUG === $theme->get_stylesheet() && current_user_can( es_admin_capability() ) ) {
		$actions['es_settings'] = sprintf(
			'<a href="%s">%s</a>',
			esc_url( add_query_arg( 'page', es_admin_pages()['settings'], admin_url( 'admin.php' ) ) ),
			esc_html__( 'تنظیمات قالب', 'erfan-sanat' )
		);
	}

	return $actions;
}
add_filter( 'theme_action_links', 'es_theme_action_links', 10, 2 );
