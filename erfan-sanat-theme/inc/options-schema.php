<?php
/**
 * Declarative theme options schema.
 *
 * Everything the admin panel renders, sanitizes, imports and exports is
 * described here as pure data. No markup is duplicated anywhere else:
 * inc/admin/fields.php renders these definitions generically.
 *
 * Field contract (all keys are required unless noted):
 *   key      (string) Unique machine key inside the theme options row.
 *   type     (string) Renderer type, see es_field_types().
 *   label    (string) Translated UI label.
 *   default  (mixed)  Default value, also used by regression/reset tooling.
 *   sanitize (string) Sanitization strategy, see es_sanitize_value().
 *   desc     (string) Optional help text shown under the control.
 *   choices  (array)  Optional value => label map for select/multiselect/icon/font.
 *   min/max/step (int|float) Optional bounds for number/range fields.
 *   cap      (string) Optional capability override (defaults to manage_options).
 *   fields   (array)  Sub-schema for repeater field types.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Font choices available for typography fields (self-hosted only).
 *
 * @return array<string,string>
 */
function es_font_choices() {
	return array(
		'vazirmatn' => __( 'وزیرمتن (Vazirmatn) — پیش‌فرض', 'erfan-sanat' ),
		'system'    => __( 'فونت سیستم (بدون بارگذاری فایل)', 'erfan-sanat' ),
	);
}

/**
 * Available icon keys for icon fields (inline SVG, self-hosted).
 *
 * @return array<string,string>
 */
function es_icon_choices() {
	return array(
		'spark'      => __( 'درخشش', 'erfan-sanat' ),
		'bulb'       => __( 'لامپ', 'erfan-sanat' ),
		'bolt'       => __( 'صاعقه / برق', 'erfan-sanat' ),
		'shield'     => __( 'سپر / گارانتی', 'erfan-sanat' ),
		'drop'       => __( 'قطره / ضدآب', 'erfan-sanat' ),
		'chip'       => __( 'چیپ / کنترل هوشمند', 'erfan-sanat' ),
		'certified'  => __( 'گواهی‌نامه', 'erfan-sanat' ),
		'city'       => __( 'شهر', 'erfan-sanat' ),
		'tree'       => __( 'درخت نوری', 'erfan-sanat' ),
		'tunnel'     => __( 'تونل نوری', 'erfan-sanat' ),
		'phone'      => __( 'تلفن', 'erfan-sanat' ),
		'mail'       => __( 'ایمیل', 'erfan-sanat' ),
		'clock'      => __( 'ساعت کاری', 'erfan-sanat' ),
		'pin'        => __( 'نشانی', 'erfan-sanat' ),
		'cart'       => __( 'سبد خرید', 'erfan-sanat' ),
		'tools'      => __( 'ابزار / مهندسی', 'erfan-sanat' ),
		'truck'      => __( 'ارسال', 'erfan-sanat' ),
		'star'       => __( 'ستاره', 'erfan-sanat' ),
	);
}

/**
 * Social network choices.
 *
 * @return array<string,string>
 */
function es_social_choices() {
	return array(
		'aparat'    => __( 'آپارات', 'erfan-sanat' ),
		'instagram' => __( 'اینستاگرام', 'erfan-sanat' ),
		'telegram'  => __( 'تلگرام', 'erfan-sanat' ),
		'whatsapp'  => __( 'واتساپ', 'erfan-sanat' ),
		'youtube'   => __( 'یوتیوب', 'erfan-sanat' ),
		'linkedin'  => __( 'لینکدین', 'erfan-sanat' ),
		'twitter'   => __( 'ایکس (توییتر)', 'erfan-sanat' ),
		'facebook'  => __( 'فیسبوک', 'erfan-sanat' ),
		'eitaa'     => __( 'ایتا', 'erfan-sanat' ),
		'rss'       => __( 'خوراک RSS', 'erfan-sanat' ),
	);
}

/**
 * Order-type choices shared by products and general settings.
 *
 * @return array<string,string>
 */
function es_order_type_choices() {
	return array(
		'online_cart'    => __( 'خرید آنلاین (سبد خرید)', 'erfan-sanat' ),
		'phone_inquiry'  => __( 'استعلام تلفنی', 'erfan-sanat' ),
		'official_tender'=> __( 'مناقصه / مکاتبه رسمی', 'erfan-sanat' ),
	);
}

/**
 * The complete options schema.
 *
 * @return array
 */
function es_options_schema() {
	static $schema = null;
	if ( null !== $schema ) {
		return $schema;
	}

	$font_choices   = es_font_choices();
	$icon_choices   = es_icon_choices();
	$social_choices = es_social_choices();
	$order_types    = es_order_type_choices();

	$social_repeater = array(
		array(
			'key'      => 'network',
			'type'     => 'select',
			'label'    => __( 'شبکه', 'erfan-sanat' ),
			'default'  => 'instagram',
			'choices'  => $social_choices,
			'sanitize' => 'key',
		),
		array(
			'key'      => 'icon',
			'type'     => 'icon',
			'label'    => __( 'آیکون', 'erfan-sanat' ),
			'default'  => 'star',
			'choices'  => $icon_choices,
			'sanitize' => 'key',
		),
		array(
			'key'      => 'url',
			'type'     => 'url',
			'label'    => __( 'نشانی', 'erfan-sanat' ),
			'default'  => '',
			'sanitize' => 'url',
		),
		array(
			'key'      => 'label',
			'type'     => 'text',
			'label'    => __( 'برچسب دسترسی‌پذیری', 'erfan-sanat' ),
			'default'  => '',
			'sanitize' => 'text',
		),
	);

	$schema = array(
		'version' => '1.0.0',
		'tabs'    => array(

			/* ------------------------------------------------------------- *
			 * 1. GENERAL
			 * ------------------------------------------------------------- */
			'general'    => array(
				'label'       => __( 'عمومی', 'erfan-sanat' ),
				'description' => __( 'اطلاعات پایه کسب‌وکار، راه‌های تماس و رفتار عمومی سایت.', 'erfan-sanat' ),
				'icon'        => 'dashicons-admin-generic',
				'fields'      => array(
					array(
						'key'      => 'site_direction',
						'type'     => 'select',
						'label'    => __( 'جهت نمایش سایت', 'erfan-sanat' ),
						'desc'     => __( 'قالب برای محتوای فارسی راست‌به‌چپ ساخته شده است. «خودکار» جهت را از زبان وردپرس می‌خواند.', 'erfan-sanat' ),
						'default'  => 'rtl',
						'sanitize' => 'key',
						'choices'  => array(
							'rtl'  => __( 'راست‌به‌چپ (پیشنهادی)', 'erfan-sanat' ),
							'ltr'  => __( 'چپ‌به‌راست', 'erfan-sanat' ),
							'auto' => __( 'خودکار (از زبان وردپرس)', 'erfan-sanat' ),
						),
					),
					array(
						'key'      => 'site_tagline_fa',
						'type'     => 'text',
						'label'    => __( 'شعار کوتاه مجموعه', 'erfan-sanat' ),
						'default'  => 'نورپردازی شهری و المان‌های نوری',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'company_legal_name',
						'type'     => 'text',
						'label'    => __( 'نام حقوقی کامل', 'erfan-sanat' ),
						'default'  => 'شرکت دانش‌بنیان عرفان صنعت اصفهان',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'phone_primary',
						'type'     => 'text',
						'label'    => __( 'تلفن اصلی', 'erfan-sanat' ),
						'default'  => '031-91091011',
						'sanitize' => 'text',
						'desc'     => __( 'در هدر، فوتر و بخش‌های تماس نمایش داده می‌شود.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'phone_sales',
						'type'     => 'text',
						'label'    => __( 'تلفن واحد فروش', 'erfan-sanat' ),
						'default'  => '031-91091011',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'phone_note',
						'type'     => 'text',
						'label'    => __( 'توضیح تلفن', 'erfan-sanat' ),
						'default'  => '۱۰ خط ویژه',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'mobile_primary',
						'type'     => 'text',
						'label'    => __( 'همراه / واتساپ', 'erfan-sanat' ),
						'default'  => '09137976915',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'email_primary',
						'type'     => 'email',
						'label'    => __( 'ایمیل اصلی', 'erfan-sanat' ),
						'default'  => 'info@erfansanat.com',
						'sanitize' => 'email',
					),
					array(
						'key'      => 'working_hours',
						'type'     => 'text',
						'label'    => __( 'ساعات کاری', 'erfan-sanat' ),
						'default'  => 'شنبه تا پنجشنبه، ۷ صبح تا ۴:۳۰ بعدازظهر',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'address_line',
						'type'     => 'textarea',
						'label'    => __( 'نشانی', 'erfan-sanat' ),
						'default'  => 'اصفهان، خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، پلاک ۱۱۹',
						'sanitize' => 'textarea',
					),
					array(
						'key'      => 'postal_code',
						'type'     => 'text',
						'label'    => __( 'کدپستی', 'erfan-sanat' ),
						'default'  => '۸۴۱۸۱۴۸۶۷۸',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'map_coords',
						'type'     => 'text',
						'label'    => __( 'مختصات نقشه (lat,lng)', 'erfan-sanat' ),
						'default'  => '32.500000,51.400000',
						'sanitize' => 'text',
						'desc'     => __( 'مثال: 32.653431,51.666115 — برای مکان‌نما و نقشهٔ داخلی سایت.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'enable_back_to_top',
						'type'     => 'toggle',
						'label'    => __( 'دکمهٔ بازگشت به بالا', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'enable_whatsapp_float',
						'type'     => 'toggle',
						'label'    => __( 'دکمهٔ شناور واتساپ', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'enable_breadcrumbs',
						'type'     => 'toggle',
						'label'    => __( 'نمایش مسیر راهنما (breadcrumb)', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'enable_search_in_header',
						'type'     => 'toggle',
						'label'    => __( 'جست‌وجو در هدر', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'enable_scroll_progress',
						'type'     => 'toggle',
						'label'    => __( 'نوار پیشرفت اسکرول', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'enable_preloader',
						'type'     => 'toggle',
						'label'    => __( 'نمایش لودینگ اولیه', 'erfan-sanat' ),
						'default'  => false,
						'sanitize' => 'bool',
						'desc'     => __( 'برای سرعت بارگذاری توصیه نمی‌شود؛ فقط در صورت نیاز طراحی فعال کنید.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'maintenance_mode',
						'type'     => 'toggle',
						'label'    => __( 'حالت تعمیر (فقط مدیران مشاهده کنند)', 'erfan-sanat' ),
						'default'  => false,
						'sanitize' => 'bool',
						'cap'      => 'manage_options',
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 2. BRANDING
			 * ------------------------------------------------------------- */
			'branding'   => array(
				'label'       => __( 'برند', 'erfan-sanat' ),
				'description' => __( 'لوگو، نشان تجاری و عناصر هویتی.', 'erfan-sanat' ),
				'icon'        => 'dashicons-awards',
				'fields'      => array(
					array(
						'key'      => 'logo_main',
						'type'     => 'image',
						'label'    => __( 'لوگوی اصلی', 'erfan-sanat' ),
						'default'  => 0,
						'sanitize' => 'int',
						'desc'     => __( 'در صورت خالی بودن، نشان متنی «ES» استفاده می‌شود.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'logo_light',
						'type'     => 'image',
						'label'    => __( 'لوگوی روشن (فوتر)', 'erfan-sanat' ),
						'default'  => 0,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'logo_width',
						'type'     => 'range',
						'label'    => __( 'عرض لوگو (پیکسل)', 'erfan-sanat' ),
						'default'  => 168,
						'min'      => 80,
						'max'      => 320,
						'step'     => 2,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'brand_short',
						'type'     => 'text',
						'label'    => __( 'نشان کوتاه', 'erfan-sanat' ),
						'default'  => 'ES',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'brand_name_fa',
						'type'     => 'text',
						'label'    => __( 'نام برند (فارسی)', 'erfan-sanat' ),
						'default'  => 'عرفان صنعت اصفهان',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'brand_name_en',
						'type'     => 'text',
						'label'    => __( 'نام برند (انگلیسی)', 'erfan-sanat' ),
						'default'  => 'ERFAN SANAT',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'iso_badge_text',
						'type'     => 'text',
						'label'    => __( 'متن نشان ISO', 'erfan-sanat' ),
						'default'  => 'گواهی ISO 9001:2015',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'favicon',
						'type'     => 'image',
						'label'    => __( 'فاوآیکون', 'erfan-sanat' ),
						'default'  => 0,
						'sanitize' => 'int',
						'desc'     => __( 'در صورت خالی بودن، فاوآیکون پیش‌فرض وردپرس نمایش داده می‌شود.', 'erfan-sanat' ),
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 3. HEADER
			 * ------------------------------------------------------------- */
			'header'     => array(
				'label'       => __( 'هدر', 'erfan-sanat' ),
				'description' => __( 'ساختار نوار بالایی، منو و دکمهٔ فراخوان.', 'erfan-sanat' ),
				'icon'        => 'dashicons-align-center',
				'fields'      => array(
					array(
						'key'      => 'header_sticky',
						'type'     => 'toggle',
						'label'    => __( 'هدر چسبان', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'header_style',
						'type'     => 'select',
						'label'    => __( 'حالت هدر', 'erfan-sanat' ),
						'default'  => 'dark',
						'sanitize' => 'key',
						'choices'  => array(
							'dark'        => __( 'تیره (پیش‌فرض)', 'erfan-sanat' ),
							'transparent' => __( 'شفاف روی تصویر (صفحهٔ اصلی)', 'erfan-sanat' ),
						),
					),
					array(
						'key'      => 'header_show_topbar',
						'type'     => 'toggle',
						'label'    => __( 'نمایش نوار بالایی', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'topbar_text',
						'type'     => 'text',
						'label'    => __( 'متن نوار بالایی', 'erfan-sanat' ),
						'default'  => 'طراحی، تولید و اجرای پروژه‌های نورپردازی شهری — دارای گواهی ISO 9001:2015',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'header_cta_text',
						'type'     => 'text',
						'label'    => __( 'متن دکمهٔ فراخوان', 'erfan-sanat' ),
						'default'  => 'دریافت مشاوره رایگان',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'header_cta_url',
						'type'     => 'url',
						'label'    => __( 'نشانی دکمهٔ فراخوان', 'erfan-sanat' ),
						'default'  => '#contact',
						'sanitize' => 'url',
					),
					array(
						'key'      => 'header_show_cart',
						'type'     => 'toggle',
						'label'    => __( 'نمایش سبد خرید (ووکامرس)', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'header_show_account',
						'type'     => 'toggle',
						'label'    => __( 'نمایش حساب کاربری (ووکامرس)', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'header_show_phone',
						'type'     => 'toggle',
						'label'    => __( 'نمایش شمارهٔ تماس در هدر', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'header_cta_style',
						'type'     => 'select',
						'label'    => __( 'سبک دکمهٔ فراخوان', 'erfan-sanat' ),
						'default'  => 'primary',
						'sanitize' => 'key',
						'choices'  => array(
							'primary' => __( 'طلایی (اصلی)', 'erfan-sanat' ),
							'outline' => __( 'خطی', 'erfan-sanat' ),
						),
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 4. NAVIGATION
			 * ------------------------------------------------------------- */
			'navigation' => array(
				'label'       => __( 'ناوبری', 'erfan-sanat' ),
				'description' => __( 'رفتار منوی دسکتاپ و موبایل.', 'erfan-sanat' ),
				'icon'        => 'dashicons-menu-alt',
				'fields'      => array(
					array(
						'key'      => 'nav_breakpoint',
						'type'     => 'number',
						'label'    => __( 'نقطهٔ شکست موبایل (پیکسل)', 'erfan-sanat' ),
						'default'  => 1024,
						'min'      => 768,
						'max'      => 1400,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'nav_hover_effect',
						'type'     => 'select',
						'label'    => __( 'جلوهٔ هاور منو', 'erfan-sanat' ),
						'default'  => 'underline',
						'sanitize' => 'key',
						'choices'  => array(
							'underline' => __( 'خط زیرین طلایی', 'erfan-sanat' ),
							'pill'      => __( 'پس‌زمینهٔ قرص‌شکل', 'erfan-sanat' ),
							'none'      => __( 'بدون جلوه', 'erfan-sanat' ),
						),
					),
					array(
						'key'      => 'nav_mobile_style',
						'type'     => 'select',
						'label'    => __( 'سبک منوی موبایل', 'erfan-sanat' ),
						'default'  => 'drawer',
						'sanitize' => 'key',
						'choices'  => array(
							'drawer'   => __( 'کشویی از کنار', 'erfan-sanat' ),
							'dropdown' => __( 'بازشو زیر هدر', 'erfan-sanat' ),
						),
					),
					array(
						'key'      => 'nav_mobile_footer_text',
						'type'     => 'text',
						'label'    => __( 'متن پایین منوی موبایل', 'erfan-sanat' ),
						'default'  => 'مشاورهٔ رایگان و استعلام قیمت روز',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'nav_labels',
						'type'     => 'multiselect',
						'label'    => __( 'موارد نمایش‌داده‌شده در هدر', 'erfan-sanat' ),
						'default'  => array( 'menu', 'search', 'cart', 'cta' ),
						'sanitize' => 'keys',
						'choices'  => array(
							'menu'   => __( 'منوی اصلی', 'erfan-sanat' ),
							'search' => __( 'جست‌وجو', 'erfan-sanat' ),
							'cart'   => __( 'سبد خرید', 'erfan-sanat' ),
							'cta'    => __( 'دکمهٔ فراخوان', 'erfan-sanat' ),
						),
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 5. FOOTER
			 * ------------------------------------------------------------- */
			'footer'     => array(
				'label'       => __( 'فوتر', 'erfan-sanat' ),
				'description' => __( 'ستون‌ها، متن‌ها و نشان‌های اعتماد.', 'erfan-sanat' ),
				'icon'        => 'dashicons-editor-insertmore',
				'fields'      => array(
					array(
						'key'      => 'footer_about',
						'type'     => 'editor',
						'label'    => __( 'دربارهٔ کوتاه در فوتر', 'erfan-sanat' ),
						'default'  => 'شرکت دانش‌بنیان عرفان صنعت اصفهان؛ پیشگام در طراحی، تولید و اجرای پروژه‌های نورپردازی شهری و صنعتی با بیش از دو دهه تجربه و گواهی ISO 9001:2015.',
						'sanitize' => 'html',
					),
					array(
						'key'      => 'footer_copyright',
						'type'     => 'text',
						'label'    => __( 'متن کپی‌رایت', 'erfan-sanat' ),
						'default'  => '© عرفان صنعت اصفهان — تمامی حقوق محفوظ است.',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'footer_show_socials',
						'type'     => 'toggle',
						'label'    => __( 'نمایش شبکه‌های اجتماعی', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'footer_show_newsletter',
						'type'     => 'toggle',
						'label'    => __( 'نمایش فرم خبرنامه', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
						'desc'     => __( 'فرم خبرنامه از طریق هوک es_footer_newsletter رندر می‌شود و بدون افزونه پیام موفق نمایش می‌دهد.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'footer_badges',
						'type'     => 'repeater',
						'label'    => __( 'نشان‌های اعتماد', 'erfan-sanat' ),
						'default'  => array(
							array( 'icon' => 'certified', 'title' => 'ISO 9001:2015', 'desc' => 'سیستم مدیریت کیفیت' ),
							array( 'icon' => 'shield', 'title' => '۲۴ ماه گارانتی', 'desc' => 'تعویض بی‌قید و شرط' ),
							array( 'icon' => 'drop', 'title' => 'IP67 / IP68', 'desc' => 'مقاوم در برابر آب و گردوغبار' ),
							array( 'icon' => 'chip', 'title' => 'کنترل هوشمند', 'desc' => 'پروتکل DMX و SPI' ),
						),
						'sanitize' => 'repeater',
						'fields'   => array(
							array(
								'key'      => 'icon',
								'type'     => 'icon',
								'label'    => __( 'آیکون', 'erfan-sanat' ),
								'default'  => 'certified',
								'choices'  => $icon_choices,
								'sanitize' => 'key',
							),
							array(
								'key'      => 'title',
								'type'     => 'text',
								'label'    => __( 'عنوان', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'text',
							),
							array(
								'key'      => 'desc',
								'type'     => 'text',
								'label'    => __( 'توضیح', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'text',
							),
						),
					),
					array(
						'key'      => 'footer_cta_enable',
						'type'     => 'toggle',
						'label'    => __( 'نمایش نوار فراخوان پیش از فوتر', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
						'desc'     => __( 'در صفحهٔ اصلی نمایش داده نمی‌شود (فراخوان اختصاصی خود را دارد).', 'erfan-sanat' ),
					),
					array(
						'key'      => 'footer_cta_title',
						'type'     => 'text',
						'label'    => __( 'فراخوان فوتر — تیتر', 'erfan-sanat' ),
						'default'  => 'پروژه‌ای در ذهن دارید؟',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'footer_cta_text',
						'type'     => 'textarea',
						'label'    => __( 'فراخوان فوتر — توضیح', 'erfan-sanat' ),
						'default'  => 'کارشناسان عرفان صنعت برای انتخاب، قیمت‌گذاری و اجرای المان‌های نوری در کنار شما هستند.',
						'sanitize' => 'textarea',
					),
					array(
						'key'      => 'footer_cta_btn_text',
						'type'     => 'text',
						'label'    => __( 'فراخوان فوتر — متن دکمه', 'erfan-sanat' ),
						'default'  => 'درخواست مشاوره',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'footer_cta_btn_url',
						'type'     => 'url',
						'label'    => __( 'فراخوان فوتر — نشانی دکمه', 'erfan-sanat' ),
						'default'  => '',
						'sanitize' => 'url',
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 6. COLORS
			 * ------------------------------------------------------------- */
			'colors'     => array(
				'label'       => __( 'رنگ‌ها', 'erfan-sanat' ),
				'description' => __( 'متغیرهای رنگی سیستم طراحی؛ در قالب CSS custom properties تزریق می‌شوند.', 'erfan-sanat' ),
				'icon'        => 'dashicons-art',
				'fields'      => array(
					array(
						'key'      => 'color_primary',
						'type'     => 'color',
						'label'    => __( 'رنگ اصلی (طلایی)', 'erfan-sanat' ),
						'default'  => '#f2b32c',
						'sanitize' => 'color',
					),
					array(
						'key'      => 'color_secondary',
						'type'     => 'color',
						'label'    => __( 'رنگ مکمل (شبانه)', 'erfan-sanat' ),
						'default'  => '#16202e',
						'sanitize' => 'color',
					),
					array(
						'key'      => 'color_accent',
						'type'     => 'color',
						'label'    => __( 'رنگ تأکیدی (درخشش)', 'erfan-sanat' ),
						'default'  => '#ffd980',
						'sanitize' => 'color',
					),
					array(
						'key'      => 'color_background',
						'type'     => 'color',
						'label'    => __( 'پس‌زمینه', 'erfan-sanat' ),
						'default'  => '#f7f8fa',
						'sanitize' => 'color',
					),
					array(
						'key'      => 'color_surface',
						'type'     => 'color',
						'label'    => __( 'سطح (کارت)', 'erfan-sanat' ),
						'default'  => '#ffffff',
						'sanitize' => 'color',
					),
					array(
						'key'      => 'color_surface_alt',
						'type'     => 'color',
						'label'    => __( 'سطح دوم', 'erfan-sanat' ),
						'default'  => '#eef1f6',
						'sanitize' => 'color',
					),
					array(
						'key'      => 'color_text',
						'type'     => 'color',
						'label'    => __( 'متن اصلی', 'erfan-sanat' ),
						'default'  => '#101722',
						'sanitize' => 'color',
					),
					array(
						'key'      => 'color_muted',
						'type'     => 'color',
						'label'    => __( 'متن کم‌رنگ', 'erfan-sanat' ),
						'default'  => '#5c6675',
						'sanitize' => 'color',
					),
					array(
						'key'      => 'color_border',
						'type'     => 'color',
						'label'    => __( 'رنگ حاشیه', 'erfan-sanat' ),
						'default'  => '#e3e7ee',
						'sanitize' => 'color',
					),
					array(
						'key'      => 'radius_base',
						'type'     => 'range',
						'label'    => __( 'شعاع گردی (پیکسل)', 'erfan-sanat' ),
						'default'  => 18,
						'min'      => 0,
						'max'      => 36,
						'step'     => 1,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'container_width',
						'type'     => 'range',
						'label'    => __( 'عرض محتوا (پیکسل)', 'erfan-sanat' ),
						'default'  => 1240,
						'min'      => 960,
						'max'      => 1600,
						'step'     => 20,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'enable_glow',
						'type'     => 'toggle',
						'label'    => __( 'جلوهٔ درخشش طلایی', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'glow_strength',
						'type'     => 'range',
						'label'    => __( 'شدت درخشش (٪)', 'erfan-sanat' ),
						'default'  => 45,
						'min'      => 0,
						'max'      => 100,
						'step'     => 5,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'hero_overlay_opacity',
						'type'     => 'range',
						'label'    => __( 'شفافیت پوشش تصویر هیرو (٪)', 'erfan-sanat' ),
						'default'  => 78,
						'min'      => 20,
						'max'      => 96,
						'step'     => 2,
						'sanitize' => 'int',
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 7. TYPOGRAPHY
			 * ------------------------------------------------------------- */
			'typography' => array(
				'label'       => __( 'تایپوگرافی', 'erfan-sanat' ),
				'description' => __( 'فونت‌ها به‌صورت محلی از پوشهٔ assets/fonts بارگذاری می‌شوند؛ هیچ فونتی از CDN دریافت نمی‌شود.', 'erfan-sanat' ),
				'icon'        => 'dashicons-editor-textcolor',
				'fields'      => array(
					array(
						'key'      => 'font_body',
						'type'     => 'font',
						'label'    => __( 'فونت متن', 'erfan-sanat' ),
						'default'  => 'vazirmatn',
						'choices'  => $font_choices,
						'sanitize' => 'key',
					),
					array(
						'key'      => 'font_heading',
						'type'     => 'font',
						'label'    => __( 'فونت تیترها', 'erfan-sanat' ),
						'default'  => 'vazirmatn',
						'choices'  => $font_choices,
						'sanitize' => 'key',
					),
					array(
						'key'      => 'font_size_base',
						'type'     => 'range',
						'label'    => __( 'اندازهٔ متن پایه (پیکسل)', 'erfan-sanat' ),
						'default'  => 17,
						'min'      => 14,
						'max'      => 22,
						'step'     => 1,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'font_size_h1',
						'type'     => 'range',
						'label'    => __( 'اندازهٔ تیتر یک (پیکسل)', 'erfan-sanat' ),
						'default'  => 60,
						'min'      => 34,
						'max'      => 96,
						'step'     => 2,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'heading_weight',
						'type'     => 'select',
						'label'    => __( 'وزن تیترها', 'erfan-sanat' ),
						'default'  => '800',
						'sanitize' => 'key',
						'choices'  => array(
							'600' => __( 'نیمه‌ضخیم', 'erfan-sanat' ),
							'700' => __( 'ضخیم', 'erfan-sanat' ),
							'800' => __( 'خیلی ضخیم', 'erfan-sanat' ),
							'900' => __( 'سیاه', 'erfan-sanat' ),
						),
					),
					array(
						'key'      => 'line_height',
						'type'     => 'range',
						'label'    => __( 'ارتفاع خط (٪)', 'erfan-sanat' ),
						'default'  => 190,
						'min'      => 150,
						'max'      => 220,
						'step'     => 5,
						'sanitize' => 'int',
						'desc'     => __( 'برای متون فارسی مقدار بیشتری توصیه می‌شود.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'numeral_style',
						'type'     => 'select',
						'label'    => __( 'سبک اعداد', 'erfan-sanat' ),
						'default'  => 'fa',
						'sanitize' => 'key',
						'choices'  => array(
							'fa' => __( 'فارسی (۱۲۳)', 'erfan-sanat' ),
							'en' => __( 'انگلیسی (123)', 'erfan-sanat' ),
						),
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 8. HOMEPAGE
			 * ------------------------------------------------------------- */
			'homepage'   => array(
				'label'       => __( 'صفحهٔ اصلی', 'erfan-sanat' ),
				'description' => __( 'چیدمان و محتوای نُه بخش صفحهٔ اصلی. هر بخش را می‌توان خاموش/روشن کرد.', 'erfan-sanat' ),
				'icon'        => 'dashicons-admin-home',
				'fields'      => array(
					array(
						'key'      => 'home_hero_enable',
						'type'     => 'toggle',
						'label'    => __( 'بخش هیرو', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'home_hero_eyebrow',
						'type'     => 'text',
						'label'    => __( 'هیرو — برچسب بالایی', 'erfan-sanat' ),
						'default'  => 'شرکت دانش‌بنیان عرفان صنعت اصفهان — گواهی ISO 9001',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_hero_title',
						'type'     => 'text',
						'label'    => __( 'هیرو — تیتر', 'erfan-sanat' ),
						'default'  => 'درخشان‌تر از همیشه،',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_hero_title_accent',
						'type'     => 'text',
						'label'    => __( 'هیرو — تیتر رنگی', 'erfan-sanat' ),
						'default'  => 'آینده‌ای روشن برای ایران می‌سازیم',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_hero_text',
						'type'     => 'textarea',
						'label'    => __( 'هیرو — توضیح', 'erfan-sanat' ),
						'default'  => 'با بیش از دو دهه تجربه، پیشگام در طراحی، تولید و اجرای پروژه‌های روشنایی شهری و صنعتی؛ روشنایی‌بخش خیابان‌ها، پارک‌ها، میادین، ساختمان‌ها و صنایع کشور هستیم.',
						'sanitize' => 'textarea',
					),
					array(
						'key'      => 'home_hero_btn1_text',
						'type'     => 'text',
						'label'    => __( 'هیرو — دکمهٔ اول', 'erfan-sanat' ),
						'default'  => 'مشاهده پروژه‌های نورپردازی',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_hero_btn1_url',
						'type'     => 'url',
						'label'    => __( 'هیرو — نشانی دکمهٔ اول', 'erfan-sanat' ),
						'default'  => '#projects',
						'sanitize' => 'url',
					),
					array(
						'key'      => 'home_hero_btn2_text',
						'type'     => 'text',
						'label'    => __( 'هیرو — دکمهٔ دوم', 'erfan-sanat' ),
						'default'  => 'دریافت مشاوره رایگان',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_hero_btn2_url',
						'type'     => 'url',
						'label'    => __( 'هیرو — نشانی دکمهٔ دوم', 'erfan-sanat' ),
						'default'  => '#contact',
						'sanitize' => 'url',
					),
					array(
						'key'      => 'home_hero_image',
						'type'     => 'image',
						'label'    => __( 'هیرو — تصویر پس‌زمینه', 'erfan-sanat' ),
						'default'  => 0,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'home_hero_stats',
						'type'     => 'repeater',
						'label'    => __( 'هیرو — آمار', 'erfan-sanat' ),
						'default'  => array(
							array( 'value' => '۲۵+', 'label' => 'سال تجربه درخشان' ),
							array( 'value' => '۸۵۰+', 'label' => 'پروژهٔ تکمیل‌شده' ),
							array( 'value' => '۲۸۹+', 'label' => 'مشتری فعال' ),
							array( 'value' => '۹۰+', 'label' => 'محصول متنوع' ),
						),
						'sanitize' => 'repeater',
						'fields'   => array(
							array(
								'key'      => 'value',
								'type'     => 'text',
								'label'    => __( 'مقدار', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'text',
							),
							array(
								'key'      => 'label',
								'type'     => 'text',
								'label'    => __( 'برچسب', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'text',
							),
						),
					),
					array(
						'key'      => 'home_ticker_enable',
						'type'     => 'toggle',
						'label'    => __( 'بخش نوار شهرها', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'home_ticker_label',
						'type'     => 'text',
						'label'    => __( 'برچسب نوار شهرها', 'erfan-sanat' ),
						'default'  => 'URBAN LIGHTING — EST. 2000',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_ticker_title',
						'type'     => 'text',
						'label'    => __( 'تیتر نوار شهرها', 'erfan-sanat' ),
						'default'  => 'جلب اعتماد شما باعث افتخار ماست — مفتخریم از همکاری با تمام شهرهای کشور',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_ticker_items',
						'type'     => 'repeater',
						'label'    => __( 'شهرها', 'erfan-sanat' ),
						'default'  => array(
							array( 'name' => 'اصفهان' ),
							array( 'name' => 'تهران' ),
							array( 'name' => 'شیراز' ),
							array( 'name' => 'مشهد' ),
							array( 'name' => 'کرمان' ),
							array( 'name' => 'البرز' ),
							array( 'name' => 'تبریز' ),
							array( 'name' => 'اهواز' ),
							array( 'name' => 'قم' ),
							array( 'name' => 'رشت' ),
							array( 'name' => 'یزد' ),
							array( 'name' => 'خرم‌آباد' ),
							array( 'name' => 'بندرعباس' ),
							array( 'name' => 'گیلان' ),
						),
						'sanitize' => 'repeater',
						'fields'   => array(
							array(
								'key'      => 'name',
								'type'     => 'text',
								'label'    => __( 'نام شهر', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'text',
							),
							array(
								'key'      => 'url',
								'type'     => 'url',
								'label'    => __( 'نشانی (اختیاری)', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'url',
							),
						),
					),
					array(
						'key'      => 'home_projects_enable',
						'type'     => 'toggle',
						'label'    => __( 'بخش پروژه‌ها', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'home_projects_eyebrow',
						'type'     => 'text',
						'label'    => __( 'پروژه‌ها — برچسب', 'erfan-sanat' ),
						'default'  => 'نمونه‌کارهای برتر',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_projects_title',
						'type'     => 'text',
						'label'    => __( 'پروژه‌ها — تیتر', 'erfan-sanat' ),
						'default'  => 'پروژه‌های نورپردازی شهری',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_projects_text',
						'type'     => 'textarea',
						'label'    => __( 'پروژه‌ها — توضیح', 'erfan-sanat' ),
						'default'  => 'نمونه پروژه‌های نورپردازی حرفه‌ای شهری؛ هر پروژه، روایتی از هنر، مهندسی و نور است که هویت بصری شهر را برای همیشه دگرگون می‌کند.',
						'sanitize' => 'textarea',
					),
					array(
						'key'      => 'home_projects_count',
						'type'     => 'number',
						'label'    => __( 'پروژه‌ها — تعداد', 'erfan-sanat' ),
						'default'  => 3,
						'min'      => 1,
						'max'      => 9,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'home_projects_source',
						'type'     => 'select',
						'label'    => __( 'پروژه‌ها — منبع', 'erfan-sanat' ),
						'default'  => 'recent',
						'sanitize' => 'key',
						'choices'  => array(
							'recent'   => __( 'آخرین پروژه‌ها', 'erfan-sanat' ),
							'featured' => __( 'پروژه‌های شاخص (برچسب ویژه)', 'erfan-sanat' ),
						),
					),
					array(
						'key'      => 'home_projects_link_text',
						'type'     => 'text',
						'label'    => __( 'پروژه‌ها — متن دکمهٔ آرشیو', 'erfan-sanat' ),
						'default'  => 'مشاهدهٔ آرشیو کامل پروژه‌ها',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_about_enable',
						'type'     => 'toggle',
						'label'    => __( 'بخش دربارهٔ ما', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'home_about_eyebrow',
						'type'     => 'text',
						'label'    => __( 'درباره — برچسب', 'erfan-sanat' ),
						'default'  => 'دربارهٔ شرکت دانش‌بنیان',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_about_title',
						'type'     => 'text',
						'label'    => __( 'درباره — تیتر', 'erfan-sanat' ),
						'default'  => 'بیش از ۲۵ سال تجربه، حاصلِ کارِ مفیدِ بی‌وقفه',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_about_content',
						'type'     => 'editor',
						'label'    => __( 'درباره — متن', 'erfan-sanat' ),
						'default'  => '<p><strong>شرکت دانش‌بنیان عرفان صنعت اصفهان</strong> در زمینه نورپردازی شهری، با سابقه‌ای درخشان و متخصصانی مجرب، طیف وسیعی از خدمات را به <strong>شهرداری‌ها</strong>، <strong>سازمان‌های عمرانی</strong> و <strong>پیمانکاران پروژه‌های شهری</strong> ارائه می‌دهد.</p>',
						'sanitize' => 'html',
					),
					array(
						'key'      => 'home_about_image',
						'type'     => 'image',
						'label'    => __( 'درباره — تصویر', 'erfan-sanat' ),
						'default'  => 0,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'home_about_features',
						'type'     => 'repeater',
						'label'    => __( 'درباره — فهرست ویژگی‌ها', 'erfan-sanat' ),
						'default'  => array(
							array( 'title' => 'طراحی، تولید و اجرای پروژه‌های روشنایی شهری و صنعتی' ),
							array( 'title' => 'دارای گواهینامهٔ بین‌المللی ISO 9001:2015' ),
							array( 'title' => '۲۴ ماه گارانتی تعویض + ۵ سال خدمات پس از فروش' ),
						),
						'sanitize' => 'repeater',
						'fields'   => array(
							array(
								'key'      => 'title',
								'type'     => 'text',
								'label'    => __( 'متن', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'text',
							),
						),
					),
					array(
						'key'      => 'home_about_btn_text',
						'type'     => 'text',
						'label'    => __( 'درباره — متن دکمه', 'erfan-sanat' ),
						'default'  => 'کسب اطلاعات بیشتر',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_about_btn_url',
						'type'     => 'url',
						'label'    => __( 'درباره — نشانی دکمه', 'erfan-sanat' ),
						'default'  => '#',
						'sanitize' => 'url',
					),
					array(
						'key'      => 'home_products_enable',
						'type'     => 'toggle',
						'label'    => __( 'بخش محصولات', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'home_products_eyebrow',
						'type'     => 'text',
						'label'    => __( 'محصولات — برچسب', 'erfan-sanat' ),
						'default'  => 'محبوب‌ترین تولیدات شرکت',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_products_title',
						'type'     => 'text',
						'label'    => __( 'محصولات — تیتر', 'erfan-sanat' ),
						'default'  => 'برخی از محصولات عرفان صنعت',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_products_text',
						'type'     => 'textarea',
						'label'    => __( 'محصولات — توضیح', 'erfan-sanat' ),
						'default'  => 'با بهره‌گیری از به‌روزترین تجهیزات و کارشناسان متخصص، مجموعه‌ای بی‌نظیر از محصولات نورپردازی پیشرفته را عرضه می‌کنیم؛ روشنایی را به شهر خود دعوت کنید.',
						'sanitize' => 'textarea',
					),
					array(
						'key'      => 'home_products_badge',
						'type'     => 'text',
						'label'    => __( 'محصولات — برچسب ویژگی', 'erfan-sanat' ),
						'default'  => 'کنترل هوشمند — WiFi و پروتکل NRF در تمامی محصولات',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_products_count',
						'type'     => 'number',
						'label'    => __( 'محصولات — تعداد', 'erfan-sanat' ),
						'default'  => 5,
						'min'      => 2,
						'max'      => 12,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'home_products_custom_order_text',
						'type'     => 'text',
						'label'    => __( 'محصولات — متن کارت سفارش اختصاصی', 'erfan-sanat' ),
						'default'  => 'طراحی و ساخت سفارشی مطابق طرح اختصاصی شما',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_services_enable',
						'type'     => 'toggle',
						'label'    => __( 'بخش خدمات', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'home_services_eyebrow',
						'type'     => 'text',
						'label'    => __( 'خدمات — برچسب', 'erfan-sanat' ),
						'default'  => 'خدمات حرفه‌ای',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_services_title',
						'type'     => 'text',
						'label'    => __( 'خدمات — تیتر', 'erfan-sanat' ),
						'default'  => 'خدمات نورپردازی شهری',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_services_text',
						'type'     => 'textarea',
						'label'    => __( 'خدمات — توضیح', 'erfan-sanat' ),
						'default'  => 'از میدان تا پل؛ راهکارهایی جامع برای شهرداری‌ها، سازمان‌های عمرانی و پیمانکاران پروژه‌های شهری.',
						'sanitize' => 'textarea',
					),
					array(
						'key'      => 'home_services_items',
						'type'     => 'repeater',
						'label'    => __( 'خدمات — فهرست', 'erfan-sanat' ),
						'default'  => array(
							array(
								'title' => 'نورپردازی میدان',
								'desc'  => 'خلق نقطه‌کانونی بصری در قلب شهر با المان‌ها و سازه‌های نوری باشکوه',
								'icon'  => 'city',
							),
							array(
								'title' => 'نورپردازی خیابان و بلوار',
								'desc'  => 'روشنایی یکنواخت و ایمن معابر با چراغ‌های LED کم‌مصرف',
								'icon'  => 'bulb',
							),
							array(
								'title' => 'نورپردازی پل',
								'desc'  => 'تأکید بر خطوط سازه و ایجاد نشانه‌های شهری ماندگار',
								'icon'  => 'tunnel',
							),
							array(
								'title' => 'نورپردازی المان‌های شهری',
								'desc'  => 'طراحی و ساخت المان‌های نوری اختصاصی مطابق هویت شهر',
								'icon'  => 'tree',
							),
							array(
								'title' => 'نورپردازی ساختمان',
								'desc'  => 'معماری نوری نما با والواشر، پروژکتور و شست‌وشوی دیواره',
								'icon'  => 'certified',
							),
						),
						'sanitize' => 'repeater',
						'fields'   => array(
							array(
								'key'      => 'title',
								'type'     => 'text',
								'label'    => __( 'عنوان', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'text',
							),
							array(
								'key'      => 'desc',
								'type'     => 'textarea',
								'label'    => __( 'توضیح', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'textarea',
							),
							array(
								'key'      => 'icon',
								'type'     => 'icon',
								'label'    => __( 'آیکون', 'erfan-sanat' ),
								'default'  => 'city',
								'choices'  => $icon_choices,
								'sanitize' => 'key',
							),
							array(
								'key'      => 'image',
								'type'     => 'image',
								'label'    => __( 'تصویر', 'erfan-sanat' ),
								'default'  => 0,
								'sanitize' => 'int',
							),
						),
					),
					array(
						'key'      => 'home_why_enable',
						'type'     => 'toggle',
						'label'    => __( 'بخش چرا ما', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'home_why_eyebrow',
						'type'     => 'text',
						'label'    => __( 'چرا ما — برچسب', 'erfan-sanat' ),
						'default'  => 'چرا عرفان صنعت؟',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_why_title',
						'type'     => 'text',
						'label'    => __( 'چرا ما — تیتر', 'erfan-sanat' ),
						'default'  => 'تعهد ما به کیفیت و دوام',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_why_text',
						'type'     => 'textarea',
						'label'    => __( 'چرا ما — توضیح', 'erfan-sanat' ),
						'default'  => 'نورپردازی شهری سرمایه‌گذاری بلندمدت است؛ به همین دلیل هر محصول ما حاصل استانداردهای سخت‌گیرانه و تعهد به رضایت شماست.',
						'sanitize' => 'textarea',
					),
					array(
						'key'      => 'home_why_items',
						'type'     => 'repeater',
						'label'    => __( 'چرا ما — کارت‌ها', 'erfan-sanat' ),
						'default'  => array(
							array(
								'icon'  => 'certified',
								'title' => 'گواهی ISO 9001:2015',
								'desc'  => 'مهر تأیید بین‌المللی بر سیستم مدیریت کیفیت در تمامی فرآیندهای سازمان',
							),
							array(
								'icon'  => 'drop',
								'title' => 'استاندارد IP67 / IP68',
								'desc'  => 'مقاومت کامل در برابر آب، گرد و غبار؛ از باران‌های شمال تا کویر',
							),
							array(
								'icon'  => 'chip',
								'title' => 'کنترل هوشمند',
								'desc'  => 'مدیریت رنگ و افکت‌های نوری از راه دور با WiFi و پروتکل NRF',
							),
							array(
								'icon'  => 'shield',
								'title' => '۲۴ ماه گارانتی + ۵ سال خدمات',
								'desc'  => 'گارانتی تعویض و خدمات پس از فروش؛ چون اعتماد شما بزرگ‌ترین سرمایهٔ ماست',
							),
						),
						'sanitize' => 'repeater',
						'fields'   => array(
							array(
								'key'      => 'icon',
								'type'     => 'icon',
								'label'    => __( 'آیکون', 'erfan-sanat' ),
								'default'  => 'certified',
								'choices'  => $icon_choices,
								'sanitize' => 'key',
							),
							array(
								'key'      => 'title',
								'type'     => 'text',
								'label'    => __( 'عنوان', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'text',
							),
							array(
								'key'      => 'desc',
								'type'     => 'textarea',
								'label'    => __( 'توضیح', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'textarea',
							),
						),
					),
					array(
						'key'      => 'home_process_enable',
						'type'     => 'toggle',
						'label'    => __( 'بخش مراحل همکاری', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'home_process_title',
						'type'     => 'text',
						'label'    => __( 'مراحل — تیتر', 'erfan-sanat' ),
						'default'  => 'مسیر همکاری با ما؛ از ایده تا درخشش',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_process_steps',
						'type'     => 'repeater',
						'label'    => __( 'مراحل — گام‌ها', 'erfan-sanat' ),
						'default'  => array(
							array( 'title' => 'بازدید و مشاورهٔ رایگان', 'desc' => 'کارشناسان ما در محل پروژه حاضر می‌شوند و نیاز نوری را ارزیابی می‌کنند' ),
							array( 'title' => 'طراحی اختصاصی', 'desc' => 'طراحی سه‌بعدی، محاسبات نوری و ارائهٔ پلان اجرایی' ),
							array( 'title' => 'تولید با متریال درجه‌یک', 'desc' => 'تولید در کارخانهٔ عرفان صنعت با تست کیفیت پیش از ارسال' ),
							array( 'title' => 'نصب و اجرای حرفه‌ای', 'desc' => 'اجرای دقیق توسط تیم فنی مجرب و آموزش بهره‌بردار' ),
							array( 'title' => 'تحویل و پشتیبانی دائم', 'desc' => '۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش' ),
						),
						'sanitize' => 'repeater',
						'fields'   => array(
							array(
								'key'      => 'title',
								'type'     => 'text',
								'label'    => __( 'عنوان', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'text',
							),
							array(
								'key'      => 'desc',
								'type'     => 'textarea',
								'label'    => __( 'توضیح', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'textarea',
							),
						),
					),
					array(
						'key'      => 'home_blog_enable',
						'type'     => 'toggle',
						'label'    => __( 'بخش مقالات', 'erfan-sanat' ),
						'default'  => false,
						'sanitize' => 'bool',
						'desc'     => __( 'در طراحی مرجع این بخش وجود ندارد؛ برای نمایش مقالات فنی فعال کنید.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'home_blog_title',
						'type'     => 'text',
						'label'    => __( 'مقالات — تیتر', 'erfan-sanat' ),
						'default'  => 'مقالات فنی و آموزش‌ها',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_blog_count',
						'type'     => 'number',
						'label'    => __( 'مقالات — تعداد', 'erfan-sanat' ),
						'default'  => 3,
						'min'      => 1,
						'max'      => 6,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'home_cta_enable',
						'type'     => 'toggle',
						'label'    => __( 'بخش فراخوان پایانی', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'home_cta_eyebrow',
						'type'     => 'text',
						'label'    => __( 'فراخوان — برچسب', 'erfan-sanat' ),
						'default'  => 'مشاوره و استعلام قیمت رایگان',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_cta_title',
						'type'     => 'text',
						'label'    => __( 'فراخوان — تیتر', 'erfan-sanat' ),
						'default'  => 'روشنایی را به شهر خود دعوت کنید',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_cta_text',
						'type'     => 'textarea',
						'label'    => __( 'فراخوان — توضیح', 'erfan-sanat' ),
						'default'  => 'کارشناسان حرفه‌ای فروش پاسخگوی شما هستند؛ برای انتخاب بهترین المان نوری متناسب با پروژه‌تان، همین حالا تماس بگیرید. ارائهٔ مشاورهٔ رایگان، استعلام قیمت روز و راهنمایی کامل نصب و اجرا.',
						'sanitize' => 'textarea',
					),
					array(
						'key'      => 'home_cta_image',
						'type'     => 'image',
						'label'    => __( 'فراخوان — تصویر پس‌زمینه', 'erfan-sanat' ),
						'default'  => 0,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'home_cta_btn_text',
						'type'     => 'text',
						'label'    => __( 'فراخوان — متن دکمه', 'erfan-sanat' ),
						'default'  => 'همین حالا تماس بگیرید',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_cta_btn_url',
						'type'     => 'url',
						'label'    => __( 'فراخوان — نشانی دکمه', 'erfan-sanat' ),
						'default'  => '/contact/',
						'sanitize' => 'url',
						'desc'     => __( 'نشانی نسبی نیز پذیرفته می‌شود؛ مثال: /contact/', 'erfan-sanat' ),
					),
					array(
						'key'      => 'home_custom_enable',
						'type'     => 'toggle',
						'label'    => __( 'بخش طراحی و ساخت سفارشی', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'home_custom_title',
						'type'     => 'text',
						'label'    => __( 'سفارشی‌سازی — تیتر', 'erfan-sanat' ),
						'default'  => 'طراحی و ساخت سفارشی مطابق طرح اختصاصی شما',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_custom_text',
						'type'     => 'textarea',
						'label'    => __( 'سفارشی‌سازی — توضیح', 'erfan-sanat' ),
						'default'  => 'از ایده تا اجرا در کنار شما هستیم؛ تیم مهندسی عرفان صنعت المان نوری سفارشی شما را طراحی، نمونه‌سازی و در کارخانهٔ اختصاصی خود تولید می‌کند.',
						'sanitize' => 'textarea',
					),
					array(
						'key'      => 'home_custom_image',
						'type'     => 'image',
						'label'    => __( 'سفارشی‌سازی — تصویر', 'erfan-sanat' ),
						'default'  => 0,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'home_custom_btn_text',
						'type'     => 'text',
						'label'    => __( 'سفارشی‌سازی — متن دکمه', 'erfan-sanat' ),
						'default'  => 'درخواست طراحی سفارشی',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_custom_btn_url',
						'type'     => 'url',
						'label'    => __( 'سفارشی‌سازی — نشانی دکمه', 'erfan-sanat' ),
						'default'  => '/contact/',
						'sanitize' => 'url',
					),
					array(
						'key'      => 'home_products_feature',
						'type'     => 'textarea',
						'label'    => __( 'محصولات — نوار ویژگی', 'erfan-sanat' ),
						'default'  => '<strong>کنترل هوشمند</strong> — پشتیبانی از WiFi و پروتکل NRF روی تمام محصولات نسل جدید',
						'sanitize' => 'textarea',
						'desc'     => __( 'نوار برجسته‌ای که بالای عنوان بخش محصولات نمایش داده می‌شود.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'home_products_link_text',
						'type'     => 'text',
						'label'    => __( 'محصولات — متن پیوند آرشیو', 'erfan-sanat' ),
						'default'  => 'مشاهدهٔ همهٔ محصولات',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'home_process_image',
						'type'     => 'image',
						'label'    => __( 'فرایند تولید — تصویر', 'erfan-sanat' ),
						'default'  => 0,
						'sanitize' => 'int',
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 9. PRODUCTS
			 * ------------------------------------------------------------- */
			'products'   => array(
				'label'       => __( 'محصولات', 'erfan-sanat' ),
				'description' => __( 'چیدمان آرشیو محصولات و منطق خرید/استعلام.', 'erfan-sanat' ),
				'icon'        => 'dashicons-products',
				'fields'      => array(
					array(
						'key'      => 'product_archive_columns',
						'type'     => 'select',
						'label'    => __( 'تعداد ستون آرشیو', 'erfan-sanat' ),
						'default'  => '3',
						'sanitize' => 'key',
						'choices'  => array(
							'2' => '۲',
							'3' => '۳',
							'4' => '۴',
						),
					),
					array(
						'key'      => 'product_per_page',
						'type'     => 'number',
						'label'    => __( 'تعداد محصول در هر صفحه', 'erfan-sanat' ),
						'default'  => 12,
						'min'      => 3,
						'max'      => 48,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'product_archive_sidebar',
						'type'     => 'toggle',
						'label'    => __( 'نمایش ستون کناری در آرشیو', 'erfan-sanat' ),
						'default'  => false,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'product_show_filters',
						'type'     => 'toggle',
						'label'    => __( 'نمایش فیلترهای دسته/ویژگی', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'product_show_attributes_table',
						'type'     => 'toggle',
						'label'    => __( 'نمایش جدول مشخصات فنی', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'product_show_purchase_mode',
						'type'     => 'toggle',
						'label'    => __( 'احترام به حالت خرید/استعلام هر محصول', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
						'desc'     => __( 'در صورت خاموش بودن، همهٔ محصولات به‌صورت خرید آنلاین نمایش داده می‌شوند.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'product_default_order_type',
						'type'     => 'select',
						'label'    => __( 'حالت پیش‌فرض محصولات جدید', 'erfan-sanat' ),
						'default'  => 'phone_inquiry',
						'sanitize' => 'key',
						'choices'  => $order_types,
					),
					array(
						'key'      => 'product_inquiry_cta_text',
						'type'     => 'text',
						'label'    => __( 'متن دکمهٔ استعلام تلفنی', 'erfan-sanat' ),
						'default'  => 'استعلام قیمت و مشاورهٔ تلفنی',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'product_tender_cta_text',
						'type'     => 'text',
						'label'    => __( 'متن دکمهٔ مناقصه', 'erfan-sanat' ),
						'default'  => 'ارسال درخواست رسمی / مناقصه',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'product_contact_price_label',
						'type'     => 'text',
						'label'    => __( 'برچسب جایگزین قیمت', 'erfan-sanat' ),
						'default'  => 'استعلام قیمت',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'product_show_rating',
						'type'     => 'toggle',
						'label'    => __( 'نمایش امتیاز محصولات', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'product_related_count',
						'type'     => 'number',
						'label'    => __( 'تعداد محصولات مرتبط', 'erfan-sanat' ),
						'default'  => 3,
						'min'      => 0,
						'max'      => 8,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'product_catalog_mode',
						'type'     => 'toggle',
						'label'    => __( 'حالت کاتالوگ (مخفی‌سازی قیمت‌ها)', 'erfan-sanat' ),
						'default'  => false,
						'sanitize' => 'bool',
						'desc'     => __( 'در این حالت قیمت‌ها جایگزین برچسب استعلام می‌شوند و سبد خرید غیرفعال می‌شود.', 'erfan-sanat' ),
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 10. PROJECTS
			 * ------------------------------------------------------------- */
			'projects'   => array(
				'label'       => __( 'پروژه‌ها', 'erfan-sanat' ),
				'description' => __( 'آرشیو و صفحهٔ پروژه‌های نورپردازی.', 'erfan-sanat' ),
				'icon'        => 'dashicons-portfolio',
				'fields'      => array(
					array(
						'key'      => 'project_archive_columns',
						'type'     => 'select',
						'label'    => __( 'تعداد ستون آرشیو', 'erfan-sanat' ),
						'default'  => '3',
						'sanitize' => 'key',
						'choices'  => array(
							'2' => '۲',
							'3' => '۳',
							'4' => '۴',
						),
					),
					array(
						'key'      => 'project_per_page',
						'type'     => 'number',
						'label'    => __( 'تعداد پروژه در هر صفحه', 'erfan-sanat' ),
						'default'  => 9,
						'min'      => 3,
						'max'      => 36,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'project_show_filters',
						'type'     => 'toggle',
						'label'    => __( 'نمایش فیلترهای دسته/موقعیت', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'project_archive_sidebar',
						'type'     => 'toggle',
						'label'    => __( 'نمایش ستون کناری در آرشیو پروژه‌ها', 'erfan-sanat' ),
						'desc'     => __( 'اگر خاموش باشد، آرشیو پروژه‌ها تمام‌عرض نمایش داده می‌شود.', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'project_show_specs',
						'type'     => 'toggle',
						'label'    => __( 'نمایش جدول مشخصات پروژه', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'project_related_count',
						'type'     => 'number',
						'label'    => __( 'تعداد پروژه‌های مرتبط', 'erfan-sanat' ),
						'default'  => 3,
						'min'      => 0,
						'max'      => 9,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'project_archive_title',
						'type'     => 'text',
						'label'    => __( 'تیتر آرشیو پروژه‌ها', 'erfan-sanat' ),
						'default'  => 'پروژه‌های نورپردازی شهری',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'project_archive_text',
						'type'     => 'textarea',
						'label'    => __( 'توضیح آرشیو پروژه‌ها', 'erfan-sanat' ),
						'default'  => 'مروری بر پروژه‌های اجراشدهٔ عرفان صنعت در شهرهای مختلف کشور؛ هر پروژه با جزئیات فنی، تعداد پیکسل و توان مصرفی.',
						'sanitize' => 'textarea',
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 11. BLOG
			 * ------------------------------------------------------------- */
			'blog'       => array(
				'label'       => __( 'بلاگ', 'erfan-sanat' ),
				'description' => __( 'آرشیو مقالات، صفحهٔ مطلب و اطلاعات فنی مقالات.', 'erfan-sanat' ),
				'icon'        => 'dashicons-welcome-write-blog',
				'fields'      => array(
					array(
						'key'      => 'blog_layout',
						'type'     => 'select',
						'label'    => __( 'چیدمان آرشیو', 'erfan-sanat' ),
						'default'  => 'grid',
						'sanitize' => 'key',
						'choices'  => array(
							'grid' => __( 'شبکه‌ای کارتی', 'erfan-sanat' ),
							'list' => __( 'فهرست افقی', 'erfan-sanat' ),
						),
					),
					array(
						'key'      => 'blog_columns',
						'type'     => 'select',
						'label'    => __( 'تعداد ستون', 'erfan-sanat' ),
						'default'  => '3',
						'sanitize' => 'key',
						'choices'  => array(
							'2' => '۲',
							'3' => '۳',
						),
					),
					array(
						'key'      => 'blog_sidebar',
						'type'     => 'toggle',
						'label'    => __( 'نمایش ستون کناری', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'blog_excerpt_length',
						'type'     => 'number',
						'label'    => __( 'طول خلاصه (کلمه)', 'erfan-sanat' ),
						'default'  => 24,
						'min'      => 8,
						'max'      => 80,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'blog_show_reading_time',
						'type'     => 'toggle',
						'label'    => __( 'نمایش زمان مطالعه', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'blog_show_author',
						'type'     => 'toggle',
						'label'    => __( 'نمایش نویسنده', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'blog_show_toc',
						'type'     => 'toggle',
						'label'    => __( 'نمایش فهرست مطالب مقاله', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'blog_slug',
						'type'     => 'text',
						'label'    => __( 'پیشوند نشانی مقالات', 'erfan-sanat' ),
						'default'  => 'blog',
						'sanitize' => 'key',
						'desc'     => __( 'مثال: blog → /blog/عنوان-مقاله/. پس از تغییر، از تب «ابزارها» قواعد بازنویسی را بازسازی کنید.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'blog_slug_rewrite',
						'type'     => 'toggle',
						'label'    => __( 'اعمال پیشوند نشانی مقالات', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
						'desc'     => __( 'در صورت خاموش بودن، نشانی مقالات به ساختار پیش‌فرض وردپرس بازمی‌گردد.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'blog_related_count',
						'type'     => 'number',
						'label'    => __( 'تعداد مقالات مرتبط', 'erfan-sanat' ),
						'default'  => 3,
						'min'      => 0,
						'max'      => 6,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'blog_per_page',
						'type'     => 'number',
						'label'    => __( 'تعداد مقالات در هر صفحه', 'erfan-sanat' ),
						'default'  => 9,
						'min'      => 3,
						'max'      => 36,
						'sanitize' => 'int',
						'desc'     => __( 'در صفحهٔ «آرشیو مقالات» و آرشیو بلاگ اعمال می‌شود.', 'erfan-sanat' ),
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 12. WOOCOMMERCE
			 * ------------------------------------------------------------- */
			'woocommerce' => array(
				'label'       => __( 'ووکامرس', 'erfan-sanat' ),
				'description' => __( 'یکپارچگی با ووکامرس؛ در صورت غیرفعال بودن ووکامرس این تنظیمات بی‌اثر هستند.', 'erfan-sanat' ),
				'icon'        => 'dashicons-cart',
				'fields'      => array(
					array(
						'key'      => 'wc_enable_integration',
						'type'     => 'toggle',
						'label'    => __( 'فعال‌سازی یکپارچگی قالب با ووکامرس', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'wc_shop_title',
						'type'     => 'text',
						'label'    => __( 'تیتر صفحهٔ فروشگاه', 'erfan-sanat' ),
						'default'  => 'فروشگاه تجهیزات نورپردازی',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'wc_shop_text',
						'type'     => 'textarea',
						'label'    => __( 'توضیح صفحهٔ فروشگاه', 'erfan-sanat' ),
						'default'  => 'ریسه‌های LED، پوینت‌لایت پیکسل، کنترلرهای DMX و تجهیزات جانبی نورپردازی؛ همراه با پشتیبانی فنی و مشاورهٔ تخصصی.',
						'sanitize' => 'textarea',
					),
					array(
						'key'      => 'wc_show_breadcrumb',
						'type'     => 'toggle',
						'label'    => __( 'نمایش مسیر راهنما در صفحات فروشگاه', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'wc_single_layout',
						'type'     => 'select',
						'label'    => __( 'چیدمان صفحهٔ محصول', 'erfan-sanat' ),
						'default'  => 'classic',
						'sanitize' => 'key',
						'choices'  => array(
							'classic' => __( 'کلاسیک (گالری چپ/راست)', 'erfan-sanat' ),
							'wide'    => __( 'عریض (گالری تمام‌عرض بالا)', 'erfan-sanat' ),
						),
					),
					array(
						'key'      => 'wc_trust_items',
						'type'     => 'repeater',
						'label'    => __( 'نشان‌های اعتماد کنار دکمهٔ خرید', 'erfan-sanat' ),
						'default'  => array(
							array( 'icon' => 'truck', 'title' => 'ارسال به سراسر کشور' ),
							array( 'icon' => 'shield', 'title' => 'گارانتی اصالت کالا' ),
							array( 'icon' => 'tools', 'title' => 'پشتیبانی فنی تخصصی' ),
						),
						'sanitize' => 'repeater',
						'fields'   => array(
							array(
								'key'      => 'icon',
								'type'     => 'icon',
								'label'    => __( 'آیکون', 'erfan-sanat' ),
								'default'  => 'shield',
								'choices'  => $icon_choices,
								'sanitize' => 'key',
							),
							array(
								'key'      => 'title',
								'type'     => 'text',
								'label'    => __( 'متن', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'text',
							),
						),
					),
					array(
						'key'      => 'wc_inquiry_note',
						'type'     => 'textarea',
						'label'    => __( 'توضیح بخش استعلام محصول', 'erfan-sanat' ),
						'default'  => 'کارشناسان فروش ما برای تعیین قیمت روز، انتخاب مدل مناسب و راهنمایی نصب در خدمت شما هستند.',
						'sanitize' => 'textarea',
					),
					array(
						'key'      => 'wc_enable_checkout_note',
						'type'     => 'toggle',
						'label'    => __( 'نمایش یادداشت فنی در تسویه‌حساب', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'wc_checkout_note',
						'type'     => 'text',
						'label'    => __( 'متن یادداشت تسویه‌حساب', 'erfan-sanat' ),
						'default'  => 'برای سفارش‌های خاص و پروژه‌ای پیش از پرداخت با پشتیبانی تماس بگیرید: ۰۳۱-۹۱۰۹۱۰۱۱',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'wc_shop_alias_products',
						'type'     => 'toggle',
						'label'    => __( 'پاسخ‌دهی به مسیر /products/', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
						'desc'     => __( 'مسیر جایگزین برای صفحهٔ فروشگاه، مطابق ساختار سایت مرجع کسب‌وکار.', 'erfan-sanat' ),
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 13. SOCIAL
			 * ------------------------------------------------------------- */
			'social'     => array(
				'label'       => __( 'شبکه‌های اجتماعی', 'erfan-sanat' ),
				'description' => __( 'پیوند شبکه‌های اجتماعی برای هدر و فوتر. آیکون‌ها به‌صورت SVG داخلی رندر می‌شوند.', 'erfan-sanat' ),
				'icon'        => 'dashicons-share',
				'fields'      => array(
					array(
						'key'      => 'social_items',
						'type'     => 'repeater',
						'label'    => __( 'فهرست شبکه‌ها', 'erfan-sanat' ),
						'default'  => array(
							array(
								'network' => 'aparat',
								'icon'    => 'star',
								'url'     => 'https://www.aparat.com/erfansanat',
								'label'   => 'آپارات عرفان صنعت',
							),
							array(
								'network' => 'whatsapp',
								'icon'    => 'phone',
								'url'     => 'https://api.whatsapp.com/send?phone=989137976915',
								'label'   => 'واتساپ عرفان صنعت',
							),
							array(
								'network' => 'telegram',
								'icon'    => 'mail',
								'url'     => 'https://t.me/s/erfansanat',
								'label'   => 'تلگرام عرفان صنعت',
							),
						),
						'sanitize' => 'repeater',
						'fields'   => $social_repeater,
					),
					array(
						'key'      => 'social_show_header',
						'type'     => 'toggle',
						'label'    => __( 'نمایش در نوار بالایی هدر', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'social_show_footer',
						'type'     => 'toggle',
						'label'    => __( 'نمایش در فوتر', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 14. CONTACT
			 * ------------------------------------------------------------- */
			'contact'    => array(
				'label'       => __( 'تماس با ما', 'erfan-sanat' ),
				'description' => __( 'فرم تماس بومی قالب (بدون افزونه)، اعتبارسنجی و ضداسپم.', 'erfan-sanat' ),
				'icon'        => 'dashicons-email-alt',
				'fields'      => array(
					array(
						'key'      => 'contact_recipient',
						'type'     => 'email',
						'label'    => __( 'ایمیل دریافت‌کنندهٔ پیام‌ها', 'erfan-sanat' ),
						'default'  => 'info@erfansanat.com',
						'sanitize' => 'email',
					),
					array(
						'key'      => 'contact_subject_prefix',
						'type'     => 'text',
						'label'    => __( 'پیشوند موضوع ایمیل', 'erfan-sanat' ),
						'default'  => '[تماس با ما]',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'contact_success_message',
						'type'     => 'text',
						'label'    => __( 'پیام موفقیت', 'erfan-sanat' ),
						'default'  => 'پیام شما با موفقیت ارسال شد. کارشناسان ما در اولین فرصت پاسخ می‌دهند.',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'contact_error_message',
						'type'     => 'text',
						'label'    => __( 'پیام خطای عمومی', 'erfan-sanat' ),
						'default'  => 'ارسال پیام ناموفق بود؛ لطفاً فیلدها را بررسی کرده و دوباره تلاش کنید.',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'contact_enable_honeypot',
						'type'     => 'toggle',
						'label'    => __( 'فعال‌سازی تلهٔ اسپم (Honeypot)', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'contact_rate_limit',
						'type'     => 'number',
						'label'    => __( 'حداکثر ارسال در دقیقه (هر آی‌پی)', 'erfan-sanat' ),
						'default'  => 3,
						'min'      => 1,
						'max'      => 20,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'contact_departments',
						'type'     => 'repeater',
						'label'    => __( 'واحدهای پاسخ‌گویی', 'erfan-sanat' ),
						'default'  => array(
							array(
								'icon'  => 'phone',
								'title' => 'تماس مستقیم با کارشناسان فروش',
								'value' => '۰۳۱-۹۱۰۹۱۰۱۱',
								'url'   => 'tel:03191091011',
								'desc'  => '۱۰ خط ویژه',
							),
							array(
								'icon'  => 'mail',
								'title' => 'ایمیل شرکت',
								'value' => 'info@erfansanat.com',
								'url'   => 'mailto:info@erfansanat.com',
								'desc'  => 'پاسخ‌گویی در سریع‌ترین زمان',
							),
							array(
								'icon'  => 'clock',
								'title' => 'ساعات کاری',
								'value' => 'شنبه تا پنجشنبه، ۷ صبح تا ۴:۳۰ بعدازظهر',
								'url'   => '',
								'desc'  => 'به‌جز ایام تعطیل رسمی',
							),
						),
						'sanitize' => 'repeater',
						'fields'   => array(
							array(
								'key'      => 'icon',
								'type'     => 'icon',
								'label'    => __( 'آیکون', 'erfan-sanat' ),
								'default'  => 'phone',
								'choices'  => $icon_choices,
								'sanitize' => 'key',
							),
							array(
								'key'      => 'title',
								'type'     => 'text',
								'label'    => __( 'عنوان', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'text',
							),
							array(
								'key'      => 'value',
								'type'     => 'text',
								'label'    => __( 'مقدار', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'text',
							),
							array(
								'key'      => 'url',
								'type'     => 'url',
								'label'    => __( 'پیوند', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'url',
							),
							array(
								'key'      => 'desc',
								'type'     => 'text',
								'label'    => __( 'توضیح', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'text',
							),
						),
					),
					array(
						'key'      => 'contact_subjects',
						'type'     => 'repeater',
						'label'    => __( 'موضوع‌های فرم تماس', 'erfan-sanat' ),
						'default'  => array(
							array( 'title' => 'استعلام قیمت و خرید' ),
							array( 'title' => 'مشاورهٔ فنی و طراحی' ),
							array( 'title' => 'همکاری و مناقصه' ),
							array( 'title' => 'پشتیبانی پس از فروش' ),
						),
						'sanitize' => 'repeater',
						'fields'   => array(
							array(
								'key'      => 'title',
								'type'     => 'text',
								'label'    => __( 'عنوان موضوع', 'erfan-sanat' ),
								'default'  => '',
								'sanitize' => 'text',
							),
						),
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 15. SEO
			 * ------------------------------------------------------------- */
			'seo'        => array(
				'label'       => __( 'سئو', 'erfan-sanat' ),
				'description' => __( 'خروجی‌های سئوی قالب. قالب جایگزین افزونه‌های سئو نمی‌شود و در صورت فعال بودن Yoast/RankMath خروجی‌های خود را غیرفعال می‌کند.', 'erfan-sanat' ),
				'icon'        => 'dashicons-chart-line',
				'fields'      => array(
					array(
						'key'      => 'seo_enable_meta',
						'type'     => 'toggle',
						'label'    => __( 'تولید متا تگ‌های توصیفی', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'seo_enable_og',
						'type'     => 'toggle',
						'label'    => __( 'خروجی Open Graph / Twitter Card', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'seo_enable_schema',
						'type'     => 'toggle',
						'label'    => __( 'خروجی JSON-LD سازمان، مسیر راهنما و مقاله', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'seo_org_type',
						'type'     => 'select',
						'label'    => __( 'نوع سازمان در اسکیما', 'erfan-sanat' ),
						'default'  => 'Organization',
						'sanitize' => 'key',
						'choices'  => array(
							'Organization'      => __( 'سازمان', 'erfan-sanat' ),
							'LocalBusiness'     => __( 'کسب‌وکار محلی', 'erfan-sanat' ),
							'Manufacturer'      => __( 'تولیدکننده', 'erfan-sanat' ),
							'Corporation'       => __( 'شرکت', 'erfan-sanat' ),
						),
					),
					array(
						'key'      => 'seo_org_logo',
						'type'     => 'image',
						'label'    => __( 'لوگوی سازمان (اسکیما)', 'erfan-sanat' ),
						'default'  => 0,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'seo_default_og_image',
						'type'     => 'image',
						'label'    => __( 'تصویر پیش‌فرض اشتراک‌گذاری', 'erfan-sanat' ),
						'default'  => 0,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'seo_twitter_handle',
						'type'     => 'text',
						'label'    => __( 'حساب توییتر/ایکس', 'erfan-sanat' ),
						'default'  => '',
						'sanitize' => 'text',
					),
					array(
						'key'      => 'seo_title_separator',
						'type'     => 'select',
						'label'    => __( 'جداکنندهٔ عنوان', 'erfan-sanat' ),
						'default'  => '|',
						'sanitize' => 'key',
						'choices'  => array(
							'|' => '|',
							'-' => '-',
							'•' => '•',
							'–' => '–',
						),
					),
					array(
						'key'      => 'seo_noindex_search',
						'type'     => 'toggle',
						'label'    => __( 'noindex صفحهٔ نتایج جست‌وجو', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'seo_keywords',
						'type'     => 'textarea',
						'label'    => __( 'کلیدواژه‌های پیش‌فرض', 'erfan-sanat' ),
						'default'  => 'نورپردازی شهری، المان نوری، تونل نوری، ریسه LED، پوینت لایت پیکسل، عرفان صنعت اصفهان',
						'sanitize' => 'textarea',
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 16. PERFORMANCE
			 * ------------------------------------------------------------- */
			'performance' => array(
				'label'       => __( 'کارایی', 'erfan-sanat' ),
				'description' => __( 'بهینه‌سازی بارگذاری دارایی‌ها و منابع. هر گزینه مستقل و قابل بازگشت است.', 'erfan-sanat' ),
				'icon'        => 'dashicons-performance',
				'fields'      => array(
					array(
						'key'      => 'perf_defer_js',
						'type'     => 'toggle',
						'label'    => __( 'بارگذاری تأخیری اسکریپت‌های قالب', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'perf_preload_fonts',
						'type'     => 'toggle',
						'label'    => __( 'Preload فونت‌های محلی', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'perf_lazy_load',
						'type'     => 'toggle',
						'label'    => __( 'بارگذاری تنبل تصاویر', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'perf_disable_emoji',
						'type'     => 'toggle',
						'label'    => __( 'حذف اسکریپت ایموجی وردپرس', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'perf_disable_embeds',
						'type'     => 'toggle',
						'label'    => __( 'حذف اسکریپت embed وردپرس در فرانت‌اند', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'perf_disable_jquery_migrate',
						'type'     => 'toggle',
						'label'    => __( 'حذف jQuery Migrate در فرانت‌اند', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
						'desc'     => __( 'در صورت نیاز افزونه‌های قدیمی، این گزینه را خاموش کنید.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'perf_remove_global_styles',
						'type'     => 'toggle',
						'label'    => __( 'حذف CSS سراسری بلوک‌ها در فرانت‌اند', 'erfan-sanat' ),
						'default'  => false,
						'sanitize' => 'bool',
						'desc'     => __( 'فقط در صورت استفاده نکردن از ویرایشگر بلوکی فعال کنید.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'perf_dns_prefetch',
						'type'     => 'textarea',
						'label'    => __( 'دامنه‌های DNS Prefetch (هر خط یک دامنه)', 'erfan-sanat' ),
						'default'  => '',
						'sanitize' => 'textarea',
						'desc'     => __( 'به‌صورت پیش‌فرض خالی است؛ قالب هیچ منبع خارجی بارگذاری نمی‌کند.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'perf_critical_css',
						'type'     => 'toggle',
						'label'    => __( 'تزریق متغیرهای رنگ به‌صورت inline (حذف درخواست اضافه)', 'erfan-sanat' ),
						'default'  => true,
						'sanitize' => 'bool',
					),
				),
			),

			/* ------------------------------------------------------------- *
			 * 17. ADVANCED
			 * ------------------------------------------------------------- */
			'advanced'   => array(
				'label'       => __( 'پیشرفته', 'erfan-sanat' ),
				'description' => __( 'کدهای سفارشی، مدیریت داده و ابزارهای توسعه. این بخش تنها برای مدیران قابل دسترسی است.', 'erfan-sanat' ),
				'icon'        => 'dashicons-admin-tools',
				'fields'      => array(
					array(
						'key'      => 'custom_css',
						'type'     => 'textarea',
						'label'    => __( 'CSS سفارشی', 'erfan-sanat' ),
						'default'  => '',
						'sanitize' => 'css',
						'desc'     => __( 'این کد در فرانت‌اند و در یک استایل داخلی اضافه می‌شود.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'custom_head_code',
						'type'     => 'textarea',
						'label'    => __( 'کد در <head>', 'erfan-sanat' ),
						'default'  => '',
						'sanitize' => 'html_head',
						'cap'      => 'unfiltered_html',
						'desc'     => __( 'فقط کاربران دارای دسترسی unfiltered_html می‌توانند ذخیره کنند.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'custom_footer_code',
						'type'     => 'textarea',
						'label'    => __( 'کد پیش از </body>', 'erfan-sanat' ),
						'default'  => '',
						'sanitize' => 'html_head',
						'cap'      => 'unfiltered_html',
					),
					array(
						'key'      => 'enable_debug',
						'type'     => 'toggle',
						'label'    => __( 'حالت اشکال‌زدایی قالب', 'erfan-sanat' ),
						'default'  => false,
						'sanitize' => 'bool',
						'desc'     => __( 'نمایش هشدارهای قابل مشاهده فقط برای مدیران.', 'erfan-sanat' ),
					),
					array(
						'key'      => 'delete_data_on_uninstall',
						'type'     => 'toggle',
						'label'    => __( 'حذف داده‌های قالب هنگام حذف قالب', 'erfan-sanat' ),
						'default'  => false,
						'sanitize' => 'bool',
					),
					array(
						'key'      => 'menu_priority',
						'type'     => 'number',
						'label'    => __( 'ترتیب منوی مدیریت', 'erfan-sanat' ),
						'default'  => 59,
						'min'      => 3,
						'max'      => 99,
						'sanitize' => 'int',
					),
					array(
						'key'      => 'allow_svg_upload',
						'type'     => 'toggle',
						'label'    => __( 'اجازهٔ بارگذاری SVG برای مدیران', 'erfan-sanat' ),
						'default'  => false,
						'sanitize' => 'bool',
						'desc'     => __( 'فعال‌سازی این گزینه امنیتی است؛ فقط در صورت نیاز و با آگاهی کامل روشن کنید.', 'erfan-sanat' ),
					),
				),
			),
		),
	);

	/**
	 * Filters the theme options schema.
	 *
	 * @param array $schema Complete schema definition.
	 */
	return apply_filters( 'es_options_schema', $schema );
}

/**
 * Flatten every field definition of a tab (or all tabs) into key => field.
 *
 * @param string|null $tab Tab slug, or null for all tabs.
 * @return array<string,array>
 */
function es_schema_fields( $tab = null ) {
	$schema = es_options_schema();
	$fields = array();

	foreach ( $schema['tabs'] as $tab_slug => $tab_data ) {
		if ( null !== $tab && $tab_slug !== $tab ) {
			continue;
		}
		foreach ( (array) $tab_data['fields'] as $field ) {
			if ( empty( $field['key'] ) ) {
				continue;
			}
			$field['tab'] = $tab_slug;
			if ( ! isset( $field['cap'] ) ) {
				$field['cap'] = 'manage_options';
			}
			if ( ! isset( $field['sanitize'] ) ) {
				$field['sanitize'] = 'text';
			}
			$fields[ $field['key'] ] = $field;
		}
	}

	return $fields;
}

/**
 * Default values for every option, derived from the schema.
 *
 * @return array<string,mixed>
 */
function es_schema_defaults() {
	static $defaults = null;
	if ( null !== $defaults ) {
		return $defaults;
	}

	$defaults = array();
	foreach ( es_schema_fields() as $key => $field ) {
		$defaults[ $key ] = array_key_exists( 'default', $field ) ? $field['default'] : '';
	}

	return $defaults;
}

/**
 * Tab slug => label map (used by the admin UI).
 *
 * @return array<string,string>
 */
function es_schema_tabs() {
	$tabs = array();
	foreach ( es_options_schema()['tabs'] as $slug => $data ) {
		$tabs[ $slug ] = $data['label'];
	}
	return $tabs;
}
