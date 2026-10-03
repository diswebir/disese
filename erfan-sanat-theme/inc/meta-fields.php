<?php
/**
 * Content model meta boxes.
 *
 * Project details, technical article data and the WooCommerce purchasing model
 * of a product. Rendering goes through the shared field renderer (admin/fields)
 * and sanitization through es_sanitize_value(), so no markup or validation
 * logic is duplicated here.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta box definitions grouped by screen.
 *
 * @return array<string,array>
 */
function es_meta_box_defs() {
	$defs = array(
		'project' => array(
			'title'  => __( 'مشخصات پروژه', 'erfan-sanat' ),
			'fields' => array(
				array(
					'key'      => '_es_project_client',
					'type'     => 'text',
					'label'    => __( 'کارفرما / نهاد اجرایی', 'erfan-sanat' ),
					'default'  => '',
					'sanitize' => 'text',
				),
				array(
					'key'      => '_es_completion_date',
					'type'     => 'text',
					'label'    => __( 'تاریخ اجرا / بهره‌برداری', 'erfan-sanat' ),
					'default'  => '',
					'sanitize' => 'text',
					'desc'     => __( 'مثال: ۱۴۰۳/۰۲/۲۰ یا 2024-05-10', 'erfan-sanat' ),
				),
				array(
					'key'      => '_es_total_pixel_count',
					'type'     => 'number',
					'label'    => __( 'تعداد کل پیکسل', 'erfan-sanat' ),
					'default'  => 0,
					'min'      => 0,
					'sanitize' => 'int',
				),
				array(
					'key'      => '_es_total_power_kw',
					'type'     => 'number',
					'label'    => __( 'توان مصرفی کل (کیلووات)', 'erfan-sanat' ),
					'default'  => 0,
					'min'      => 0,
					'step'     => '0.1',
					'sanitize' => 'float',
				),
				array(
					'key'      => '_es_project_drone_video',
					'type'     => 'url',
					'label'    => __( 'ویدیوی هوایی پروژه (نشانی)', 'erfan-sanat' ),
					'default'  => '',
					'sanitize' => 'url',
					'desc'     => __( 'نشانی ویدیو در سرویس‌های داخلی مانند آپارات یا فایل محلی.', 'erfan-sanat' ),
				),
				array(
					'key'      => '_es_project_map_coords',
					'type'     => 'text',
					'label'    => __( 'مختصات جغرافیایی', 'erfan-sanat' ),
					'default'  => '',
					'sanitize' => 'text',
					'desc'     => __( 'قالب: latitude,longitude — مثال: 32.6539,51.6660', 'erfan-sanat' ),
				),
				array(
					'key'      => '_es_before_after_gallery',
					'type'     => 'gallery',
					'label'    => __( 'گالری تصاویر پروژه', 'erfan-sanat' ),
					'default'  => array(),
					'sanitize' => 'gallery',
					'desc'     => __( 'تصاویر قبل/بعد و جزئیات اجرا.', 'erfan-sanat' ),
				),
				array(
					'key'      => '_es_featured',
					'type'     => 'toggle',
					'label'    => __( 'نمایش در بخش پروژه‌های شاخص صفحهٔ اصلی', 'erfan-sanat' ),
					'default'  => false,
					'sanitize' => 'bool',
				),
			),
		),
		'post'    => array(
			'title'  => __( 'اطلاعات فنی مقاله', 'erfan-sanat' ),
			'fields' => array(
				array(
					'key'      => '_es_reading_time_min',
					'type'     => 'number',
					'label'    => __( 'زمان مطالعه (دقیقه)', 'erfan-sanat' ),
					'default'  => 0,
					'min'      => 0,
					'max'      => 120,
					'sanitize' => 'int',
					'desc'     => __( 'اگر خالی بماند، به‌صورت خودکار از حجم متن محاسبه می‌شود.', 'erfan-sanat' ),
				),
				array(
					'key'      => '_es_technical_reviewer',
					'type'     => 'text',
					'label'    => __( 'بازبین فنی', 'erfan-sanat' ),
					'default'  => '',
					'sanitize' => 'text',
				),
				array(
					'key'      => '_es_software_project_file',
					'type'     => 'image',
					'label'    => __( 'فایل پروژهٔ نرم‌افزاری (دانلود)', 'erfan-sanat' ),
					'default'  => 0,
					'sanitize' => 'int',
				),
				array(
					'key'      => '_es_faq_schema_repeater',
					'type'     => 'repeater',
					'label'    => __( 'پرسش‌های متداول (اسکیمای FAQ)', 'erfan-sanat' ),
					'default'  => array(),
					'sanitize' => 'repeater',
					'desc'     => __( 'این پرسش و پاسخ‌ها به‌صورت داده ساخت‌یافتهٔ FAQPage در صفحه منتشر می‌شوند.', 'erfan-sanat' ),
					'fields'   => array(
						array(
							'key'      => 'q',
							'type'     => 'text',
							'label'    => __( 'پرسش', 'erfan-sanat' ),
							'default'  => '',
							'sanitize' => 'text',
						),
						array(
							'key'      => 'a',
							'type'     => 'textarea',
							'label'    => __( 'پاسخ', 'erfan-sanat' ),
							'default'  => '',
							'sanitize' => 'textarea',
						),
					),
				),
			),
		),
		'product' => array(
			'title'  => __( 'مدل خرید و مشخصات فنی (عرفان صنعت)', 'erfan-sanat' ),
			'fields' => array(
				array(
					'key'      => '_es_is_purchasable_online',
					'type'     => 'toggle',
					'label'    => __( 'قابل خرید آنلاین است', 'erfan-sanat' ),
					'default'  => true,
					'sanitize' => 'bool',
					'desc'     => __( 'در صورت خاموش بودن، دکمهٔ افزودن به سبد حذف و دکمهٔ استعلام نمایش داده می‌شود.', 'erfan-sanat' ),
				),
				array(
					'key'      => '_es_order_type',
					'type'     => 'select',
					'label'    => __( 'نوع سفارش', 'erfan-sanat' ),
					'default'  => 'online_cart',
					'sanitize' => 'key',
					'choices'  => es_order_type_choices(),
				),
				array(
					'key'      => '_es_inquiry_phone',
					'type'     => 'text',
					'label'    => __( 'تلفن استعلام این محصول', 'erfan-sanat' ),
					'default'  => '',
					'sanitize' => 'text',
					'desc'     => __( 'در صورت خالی بودن، تلفن پیش‌فرض سایت استفاده می‌شود.', 'erfan-sanat' ),
				),
				array(
					'key'      => '_es_custom_price_badge',
					'type'     => 'text',
					'label'    => __( 'برچسب قیمت جایگزین', 'erfan-sanat' ),
					'default'  => '',
					'sanitize' => 'text',
					'desc'     => __( 'مثال: «تماس بگیرید» یا «قیمت بر اساس متراژ». جایگزین نمایش قیمت می‌شود.', 'erfan-sanat' ),
				),
				array(
					'key'      => '_es_min_order_qty',
					'type'     => 'number',
					'label'    => __( 'حداقل تعداد سفارش', 'erfan-sanat' ),
					'default'  => 1,
					'min'      => 1,
					'sanitize' => 'int',
				),
				array(
					'key'      => '_es_production_lead_time',
					'type'     => 'text',
					'label'    => __( 'زمان تولید و تحویل', 'erfan-sanat' ),
					'default'  => '',
					'sanitize' => 'text',
					'desc'     => __( 'مثال: ۱۵ روز کاری پس از تأیید سفارش.', 'erfan-sanat' ),
				),
				array(
					'key'      => '_es_wattage_rating',
					'type'     => 'number',
					'label'    => __( 'توان مصرفی (وات)', 'erfan-sanat' ),
					'default'  => 0,
					'min'      => 0,
					'step'     => '0.1',
					'sanitize' => 'float',
				),
				array(
					'key'      => '_es_chip_brand',
					'type'     => 'text',
					'label'    => __( 'برند چیپ / درایور', 'erfan-sanat' ),
					'default'  => '',
					'sanitize' => 'text',
				),
				array(
					'key'      => '_es_technical_datasheet_pdf',
					'type'     => 'image',
					'label'    => __( 'فایل دیتاشیت فنی (PDF)', 'erfan-sanat' ),
					'default'  => 0,
					'sanitize' => 'int',
					'desc'     => __( 'فایل را از کتابخانهٔ رسانه انتخاب کنید (PDF یا تصویر).', 'erfan-sanat' ),
				),
				array(
					'key'      => '_es_wiring_schematic_img',
					'type'     => 'image',
					'label'    => __( 'تصویر شماتیک سیم‌کشی', 'erfan-sanat' ),
					'default'  => 0,
					'sanitize' => 'int',
				),
				array(
					'key'      => '_es_demo_video_url',
					'type'     => 'url',
					'label'    => __( 'ویدیوی دموی محصول', 'erfan-sanat' ),
					'default'  => '',
					'sanitize' => 'url',
				),
			),
		),
	);

	/**
	 * Filters the meta box definitions.
	 *
	 * @param array $defs Meta box definitions by post type.
	 */
	return apply_filters( 'es_meta_box_defs', $defs );
}

/**
 * Register the meta boxes.
 *
 * @return void
 */
function es_register_meta_boxes() {
	$defs = es_meta_box_defs();

	foreach ( $defs as $post_type => $def ) {
		if ( 'product' === $post_type && ! es_woocommerce_active() ) {
			continue;
		}

		add_meta_box(
			'es-meta-' . $post_type,
			$def['title'],
			'es_render_meta_boxes',
			$post_type,
			'normal',
			'high'
		);
	}

	// Purchasing summary in the sidebar for quick decisions.
	if ( es_woocommerce_active() ) {
		add_meta_box(
			'es-purchase-summary',
			__( 'خلاصهٔ مدل خرید', 'erfan-sanat' ),
			'es_render_purchase_summary',
			'product',
			'side',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'es_register_meta_boxes' );

/**
 * Render a meta box.
 *
 * @param WP_Post $post Current post.
 * @param array   $box  Meta box args.
 * @return void
 */
function es_render_meta_boxes( $post, $box = array() ) {
	$defs = es_meta_box_defs();
	$type = $post->post_type;

	if ( ! isset( $defs[ $type ] ) ) {
		return;
	}

	wp_nonce_field( 'es_save_meta_' . $type, 'es_meta_nonce' );

	echo '<div class="es-meta">';

	foreach ( $defs[ $type ]['fields'] as $field ) {
		$stored  = get_post_meta( $post->ID, $field['key'], true );
		$value   = ( '' === $stored || null === $stored ) ? $field['default'] : $stored;
		$is_full = in_array( $field['type'], array( 'repeater', 'editor' ), true );

		echo '<div class="es-meta__field' . ( $is_full ? ' es-meta__field--full' : '' ) . '">';
		es_field_row(
			$field,
			$value,
			array(
				'name'    => $field['key'],
				'id'      => 'es-meta-' . $type . '-' . ltrim( $field['key'], '_' ),
				'context' => 'meta',
			)
		);
		echo '</div>';
	}

	echo '</div>';
}

/**
 * Sidebar summary for products (purchasing model at a glance).
 *
 * @param WP_Post $post Product post.
 * @return void
 */
function es_render_purchase_summary( $post ) {
	$model   = es_product_purchase_model( $post->ID );
	$labels  = es_order_type_choices();
	$phone   = (string) get_post_meta( $post->ID, '_es_inquiry_phone', true );
	$badge   = (string) get_post_meta( $post->ID, '_es_custom_price_badge', true );
	$lead    = (string) get_post_meta( $post->ID, '_es_production_lead_time', true );
	$min_qty = (int) get_post_meta( $post->ID, '_es_min_order_qty', true );

	printf(
		'<p class="es-summary__model es-summary__model--%1$s"><strong>%2$s</strong></p>',
		esc_attr( $model ),
		esc_html( isset( $labels[ $model ] ) ? $labels[ $model ] : $model )
	);

	echo '<ul class="es-summary__list">';
	printf(
		'<li>%1$s <strong>%2$s</strong></li>',
		esc_html__( 'خرید آنلاین:', 'erfan-sanat' ),
		esc_html( es_product_is_purchasable_online( $post->ID ) ? __( 'فعال', 'erfan-sanat' ) : __( 'غیرفعال', 'erfan-sanat' ) )
	);
	printf(
		'<li>%1$s <strong dir="ltr">%2$s</strong></li>',
		esc_html__( 'حداقل سفارش:', 'erfan-sanat' ),
		esc_html( es_num( number_format_i18n( max( 1, $min_qty ) ) ) )
	);

	if ( $phone ) {
		printf( '<li>%1$s <strong dir="ltr">%2$s</strong></li>', esc_html__( 'تلفن استعلام:', 'erfan-sanat' ), esc_html( $phone ) );
	}

	if ( $lead ) {
		printf( '<li>%1$s <strong>%2$s</strong></li>', esc_html__( 'زمان تحویل:', 'erfan-sanat' ), esc_html( $lead ) );
	}

	if ( $badge ) {
		printf( '<li>%1$s <strong>%2$s</strong></li>', esc_html__( 'برچسب قیمت:', 'erfan-sanat' ), esc_html( $badge ) );
	}

	echo '</ul>';

	printf(
		'<p class="es-summary__hint">%s</p>',
		esc_html__( 'این مقادیر در آرشیو فروشگاه، صفحهٔ محصول و دکمه‌های خرید استفاده می‌شوند.', 'erfan-sanat' )
	);
}

/**
 * Persist the meta boxes.
 *
 * @param int $post_id Post id.
 * @return void
 */
function es_save_meta_boxes( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	$type = get_post_type( $post_id );
	$defs = es_meta_box_defs();

	if ( ! isset( $defs[ $type ] ) ) {
		return;
	}

	// Meta boxes are only rendered for the screens we registered them on.
	$nonce = isset( $_POST['es_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['es_meta_nonce'] ) ) : '';

	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'es_save_meta_' . $type ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( $defs[ $type ]['fields'] as $field ) {
		$key = $field['key'];

		if ( ! es_current_user_can_edit_field( $field ) ) {
			continue;
		}

		if ( ! isset( $_POST[ $key ] ) ) {
			// Unchecked toggles disappear from POST: store the explicit state.
			if ( 'toggle' === $field['type'] ) {
				delete_post_meta( $post_id, $key );
			}
			continue;
		}

		$raw     = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized right below through the schema sanitizer.
		$clean   = es_sanitize_value( $field, $raw );
		$is_void = ( '' === $clean || 0 === $clean || array() === $clean || false === $clean || '0' === $clean );

		if ( $is_void && 'toggle' !== $field['type'] && 0 !== (int) $field['default'] && false !== $field['default'] && '' !== $field['default'] ) {
			// Empty value with a meaningful default: fall back to it.
			update_post_meta( $post_id, $key, $field['default'] );
			continue;
		}

		if ( $is_void ) {
			delete_post_meta( $post_id, $key );
			continue;
		}

		update_post_meta( $post_id, $key, $clean );
	}

	/**
	 * Fires after the theme meta boxes were saved.
	 *
	 * @param int    $post_id Post id.
	 * @param string $type    Post type.
	 */
	do_action( 'es_meta_saved', $post_id, $type );
}
add_action( 'save_post', 'es_save_meta_boxes' );

/**
 * Read a theme meta value with a default.
 *
 * @param string $key     Meta key (with the _es_ prefix).
 * @param int    $post_id Post id (0 = current).
 * @param mixed  $default Default.
 * @return mixed
 */
function es_meta( $key, $post_id = 0, $default = '' ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();

	if ( ! $post_id ) {
		return $default;
	}

	$value = get_post_meta( $post_id, $key, true );

	return ( '' === $value || null === $value ) ? $default : $value;
}

/* -------------------------------------------------------------------------
 * Admin list columns
 * ---------------------------------------------------------------------- */

/**
 * Register the extra list columns.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function es_project_columns( $columns ) {
	return $columns + array(
		'es_project_cat'  => __( 'دسته‌بندی پروژه', 'erfan-sanat' ),
		'es_project_meta' => __( 'مشخصات', 'erfan-sanat' ),
	);
}
add_filter( 'manage_project_posts_columns', 'es_project_columns' );

/**
 * Render project list columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post id.
 * @return void
 */
function es_project_column_content( $column, $post_id ) {
	if ( 'es_project_cat' === $column ) {
		es_term_pills( $post_id, 'project_cat', 2 );
		echo '<br>';
		es_term_pills( $post_id, 'project_location', 2 );
	}

	if ( 'es_project_meta' === $column ) {
		$client = es_meta( '_es_project_client', $post_id, '' );
		echo esc_html( $client ? $client : '—' );
	}
}
add_action( 'manage_project_posts_custom_column', 'es_project_column_content', 10, 2 );

/**
 * Render post list columns (reading time).
 *
 * @param array $columns Existing columns.
 * @return array
 */
function es_post_columns( $columns ) {
	$columns['es_reading_time'] = __( 'زمان مطالعه', 'erfan-sanat' );
	$columns['es_reviewer']     = __( 'بازبین فنی', 'erfan-sanat' );

	return $columns;
}
add_filter( 'manage_post_posts_columns', 'es_post_columns' );

/**
 * Render post list columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post id.
 * @return void
 */
function es_post_column_content( $column, $post_id ) {
	if ( 'es_reading_time' === $column ) {
		printf(
			/* translators: %s: minutes */
			esc_html__( '%s دقیقه', 'erfan-sanat' ),
			esc_html( es_num( (string) es_reading_time( $post_id ) ) )
		);
	}

	if ( 'es_reviewer' === $column ) {
		$reviewer = es_meta( '_es_technical_reviewer', $post_id, '' );
		echo esc_html( $reviewer ? $reviewer : '—' );
	}
}
add_action( 'manage_post_posts_custom_column', 'es_post_column_content', 10, 2 );

/**
 * Product list columns: purchasing model + price badge.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function es_product_columns( $columns ) {
	$new = array();

	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;

		if ( 'name' === $key ) {
			$new['es_purchase_model'] = __( 'مدل خرید', 'erfan-sanat' );
		}
	}

	$new['es_product_specs'] = __( 'مشخصات فنی', 'erfan-sanat' );

	return $new;
}
add_filter( 'manage_product_posts_columns', 'es_product_columns' );

/**
 * Render product list columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post id.
 * @return void
 */
function es_product_column_content( $column, $post_id ) {
	if ( 'es_purchase_model' === $column ) {
		$model  = es_product_purchase_model( $post_id );
		$labels = es_order_type_choices();

		printf(
			'<span class="es-badge es-badge--%1$s">%2$s</span>',
			esc_attr( $model ),
			esc_html( isset( $labels[ $model ] ) ? $labels[ $model ] : $model )
		);

		$badge = es_meta( '_es_custom_price_badge', $post_id, '' );

		if ( $badge ) {
			printf( '<br><small>%s</small>', esc_html( $badge ) );
		}
	}

	if ( 'es_product_specs' === $column ) {
		$watts = es_meta( '_es_wattage_rating', $post_id, 0 );
		$chip  = es_meta( '_es_chip_brand', $post_id, '' );

		if ( $watts ) {
			printf( '<span dir="ltr">%s W</span>', esc_html( es_num( (string) $watts ) ) );
		}

		if ( $chip ) {
			printf( '<br><small>%s</small>', esc_html( $chip ) );
		}
	}
}
add_action( 'manage_product_posts_custom_column', 'es_product_column_content', 10, 2 );
