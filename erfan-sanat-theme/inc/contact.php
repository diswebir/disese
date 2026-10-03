<?php
/**
 * Contact form: renderer, validator and mailer.
 *
 * The form posts to admin-post.php with a nonce, a honeypot and an optional
 * per-IP rate limit. Every field is validated server side; the result is
 * reported back through a status query argument so the page can render a
 * proper notice without JS.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Contact form departments (from options).
 *
 * @return array<string,string>
 */
function es_contact_departments() {
	$departments = es_opt( 'contact_departments', array() );
	$out         = array();

	if ( is_array( $departments ) ) {
		foreach ( $departments as $row ) {
			if ( ! empty( $row['key'] ) && ! empty( $row['label'] ) ) {
				$out[ sanitize_key( $row['key'] ) ] = (string) $row['label'];
			}
		}
	}

	if ( ! $out ) {
		$out = array(
			'sales'    => __( 'واحد فروش و استعلام قیمت', 'erfan-sanat' ),
			'projects' => __( 'واحد پروژه‌های شهری', 'erfan-sanat' ),
			'tech'     => __( 'واحد فنی و پشتیبانی', 'erfan-sanat' ),
		);
	}

	return $out;
}

/**
 * Contact form subjects (from options).
 *
 * @return array<string,string>
 */
function es_contact_subjects() {
	$subjects = es_opt( 'contact_subjects', array() );
	$out      = array();

	if ( is_array( $subjects ) ) {
		foreach ( $subjects as $row ) {
			if ( ! empty( $row['key'] ) && ! empty( $row['label'] ) ) {
				$out[ sanitize_key( $row['key'] ) ] = (string) $row['label'];
			}
		}
	}

	if ( ! $out ) {
		$out = array(
			'consult' => __( 'درخواست مشاوره و طراحی', 'erfan-sanat' ),
			'price'   => __( 'استعلام قیمت و خرید', 'erfan-sanat' ),
			'support' => __( 'پشتیبانی فنی و گارانتی', 'erfan-sanat' ),
			'other'   => __( 'سایر موضوعات', 'erfan-sanat' ),
		);
	}

	return $out;
}

/**
 * Render the contact form.
 *
 * @param array $args title, button, compact, department, subject.
 * @return void
 */
function es_render_contact_form( array $args = array() ) {
	$status = isset( $_GET['es_contact'] ) ? sanitize_key( wp_unslash( $_GET['es_contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only status flag.
	$uid    = 'es-contact-' . wp_unique_id();

	$departments = es_contact_departments();
	$subjects    = es_contact_subjects();
	?>
	<form class="es-form es-contact-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
		<input type="hidden" name="action" value="es_contact_submit">
		<input type="hidden" name="es_redirect" value="<?php echo esc_url( es_current_url() ); ?>">
		<?php wp_nonce_field( 'es_contact_submit', 'es_contact_nonce' ); ?>

		<?php
		if ( $status ) {
			$is_ok    = 'success' === $status;
			$message  = $is_ok
				? (string) es_opt( 'contact_success_message', __( 'پیام شما با موفقیت ارسال شد. کارشناسان ما به‌زودی تماس می‌گیرند.', 'erfan-sanat' ) )
				: (string) es_opt( 'contact_error_message', __( 'ارسال پیام ناموفق بود. لطفاً دوباره تلاش کنید یا با شمارهٔ تلفن تماس بگیرید.', 'erfan-sanat' ) );

			printf(
				'<p class="es-form__notice es-form__notice--%1$s" role="status">%2$s</p>',
				esc_attr( $is_ok ? 'success' : 'error' ),
				esc_html( $message )
			);
		}
		?>

		<div class="es-form__grid">
			<p class="es-form__field">
				<label for="<?php echo esc_attr( $uid ); ?>-name"><?php esc_html_e( 'نام و نام خانوادگی', 'erfan-sanat' ); ?> <span class="es-required" aria-hidden="true">*</span></label>
				<input type="text" id="<?php echo esc_attr( $uid ); ?>-name" name="es_name" required autocomplete="name">
			</p>

			<p class="es-form__field">
				<label for="<?php echo esc_attr( $uid ); ?>-phone"><?php esc_html_e( 'شمارهٔ تماس', 'erfan-sanat' ); ?> <span class="es-required" aria-hidden="true">*</span></label>
				<input type="tel" id="<?php echo esc_attr( $uid ); ?>-phone" name="es_phone" required dir="ltr" inputmode="tel" autocomplete="tel" placeholder="031-91091011">
			</p>

			<p class="es-form__field">
				<label for="<?php echo esc_attr( $uid ); ?>-email"><?php esc_html_e( 'ایمیل (اختیاری)', 'erfan-sanat' ); ?></label>
				<input type="email" id="<?php echo esc_attr( $uid ); ?>-email" name="es_email" dir="ltr" autocomplete="email">
			</p>

			<p class="es-form__field">
				<label for="<?php echo esc_attr( $uid ); ?>-department"><?php esc_html_e( 'واحد مربوطه', 'erfan-sanat' ); ?></label>
				<select id="<?php echo esc_attr( $uid ); ?>-department" name="es_department">
					<?php foreach ( $departments as $key => $label ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p class="es-form__field es-form__field--full">
				<label for="<?php echo esc_attr( $uid ); ?>-subject"><?php esc_html_e( 'موضوع درخواست', 'erfan-sanat' ); ?></label>
				<select id="<?php echo esc_attr( $uid ); ?>-subject" name="es_subject">
					<?php foreach ( $subjects as $key => $label ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p class="es-form__field es-form__field--full">
				<label for="<?php echo esc_attr( $uid ); ?>-message"><?php esc_html_e( 'شرح درخواست', 'erfan-sanat' ); ?> <span class="es-required" aria-hidden="true">*</span></label>
				<textarea id="<?php echo esc_attr( $uid ); ?>-message" name="es_message" rows="6" required placeholder="<?php esc_attr_e( 'مثلاً: برای میدان اصلی شهر به ۲۰ عدد المان نوری با کنترل هوشمند نیاز داریم.', 'erfan-sanat' ); ?>"></textarea>
			</p>
		</div>

		<?php if ( es_opt( 'contact_enable_honeypot', true ) ) : ?>
			<p class="es-form__honeypot" aria-hidden="true">
				<label for="<?php echo esc_attr( $uid ); ?>-company"><?php esc_html_e( 'نام شرکت', 'erfan-sanat' ); ?></label>
				<input type="text" id="<?php echo esc_attr( $uid ); ?>-company" name="es_company" tabindex="-1" autocomplete="off">
			</p>
		<?php endif; ?>

		<div class="es-form__actions">
			<button type="submit" class="es-btn es-btn--primary es-btn--lg">
				<span><?php echo esc_html( isset( $args['button'] ) ? $args['button'] : __( 'ارسال درخواست', 'erfan-sanat' ) ); ?></span>
				<?php es_icon( 'arrow', 'es-icon', 18 ); ?>
			</button>

			<p class="es-form__privacy">
				<?php esc_html_e( 'اطلاعات شما محرمانه است و فقط برای پاسخ‌گویی به همین درخواست استفاده می‌شود.', 'erfan-sanat' ); ?>
			</p>
		</div>
	</form>
	<?php
}

/**
 * Current front-end URL (used as the post-submit redirect target).
 *
 * @return string
 */
function es_current_url() {
	$path = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';

	return home_url( $path );
}

/**
 * Handle the contact form submission.
 *
 * @return void
 */
function es_handle_contact_submit() {
	$redirect = isset( $_POST['es_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['es_redirect'] ) ) : home_url( '/' );

	$verify = es_verify_nonce( 'es_contact_submit', 'es_contact_nonce' );

	if ( is_wp_error( $verify ) ) {
		es_safe_redirect( add_query_arg( 'es_contact', 'failed', $redirect ) );
	}

	// Honeypot: silently accept and drop bots.
	if ( es_opt( 'contact_enable_honeypot', true ) && ! empty( $_POST['es_company'] ) ) {
		es_safe_redirect( add_query_arg( 'es_contact', 'success', $redirect ) );
	}

	$limit = (int) es_opt( 'contact_rate_limit', 5 );

	if ( $limit > 0 && ! es_rate_limit_ok( 'es_contact', $limit, 600 ) ) {
		es_safe_redirect( add_query_arg( 'es_contact', 'failed', $redirect ) );
	}

	$name    = isset( $_POST['es_name'] ) ? sanitize_text_field( wp_unslash( $_POST['es_name'] ) ) : '';
	$phone   = isset( $_POST['es_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['es_phone'] ) ) : '';
	$email   = isset( $_POST['es_email'] ) ? sanitize_email( wp_unslash( $_POST['es_email'] ) ) : '';
	$message = isset( $_POST['es_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['es_message'] ) ) : '';
	$dept    = isset( $_POST['es_department'] ) ? sanitize_key( wp_unslash( $_POST['es_department'] ) ) : '';
	$subject = isset( $_POST['es_subject'] ) ? sanitize_key( wp_unslash( $_POST['es_subject'] ) ) : '';

	$departments = es_contact_departments();
	$subjects    = es_contact_subjects();

	if ( '' === $name || '' === $phone || '' === $message ) {
		es_safe_redirect( add_query_arg( 'es_contact', 'failed', $redirect ) );
	}

	if ( $email && ! is_email( $email ) ) {
		es_safe_redirect( add_query_arg( 'es_contact', 'failed', $redirect ) );
	}

	$recipient = (string) es_opt( 'contact_recipient', get_option( 'admin_email' ) );
	$recipient = sanitize_email( $recipient );

	if ( ! is_email( $recipient ) ) {
		$recipient = sanitize_email( (string) get_option( 'admin_email' ) );
	}

	$prefix  = (string) es_opt( 'contact_subject_prefix', __( 'درخواست وب‌سایت', 'erfan-sanat' ) );
	$subject_line = sprintf(
		'%1$s — %2$s',
		$prefix,
		isset( $subjects[ $subject ] ) ? $subjects[ $subject ] : __( 'بدون موضوع', 'erfan-sanat' )
	);

	$lines = array(
		sprintf( /* translators: %s: name */ __( 'نام: %s', 'erfan-sanat' ), $name ),
		sprintf( /* translators: %s: phone */ __( 'تلفن: %s', 'erfan-sanat' ), $phone ),
		sprintf( /* translators: %s: email */ __( 'ایمیل: %s', 'erfan-sanat' ), $email ? $email : '—' ),
		sprintf( /* translators: %s: department */ __( 'واحد: %s', 'erfan-sanat' ), isset( $departments[ $dept ] ) ? $departments[ $dept ] : '—' ),
		'',
		__( 'متن پیام:', 'erfan-sanat' ),
		$message,
		'',
		sprintf( /* translators: %s: url */ __( 'صفحهٔ ارسال: %s', 'erfan-sanat' ), $redirect ),
	);

	$sent = wp_mail(
		$recipient,
		$subject_line,
		implode( "\n", $lines ),
		array(
			'Content-Type: text/plain; charset=UTF-8',
			$email ? 'Reply-To: ' . $name . ' <' . $email . '>' : '',
		)
	);

	/**
	 * Fires after a contact form submission was processed.
	 *
	 * @param array $payload  Submitted, sanitized payload.
	 * @param bool  $sent     Whether wp_mail() reported success.
	 */
	do_action(
		'es_contact_submitted',
		array(
			'name'       => $name,
			'phone'      => $phone,
			'email'      => $email,
			'subject'    => $subject,
			'department' => $dept,
			'message'    => $message,
		),
		(bool) $sent
	);

	es_safe_redirect( add_query_arg( 'es_contact', $sent ? 'success' : 'failed', $redirect ) );
}
add_action( 'admin_post_es_contact_submit', 'es_handle_contact_submit' );
add_action( 'admin_post_nopriv_es_contact_submit', 'es_handle_contact_submit' );
