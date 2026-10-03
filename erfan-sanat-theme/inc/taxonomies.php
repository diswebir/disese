<?php
/**
 * Custom taxonomies for projects.
 *
 * Taxonomy terms (cities, project categories) are content managed by editors;
 * the theme only registers the structures and their rewrite bases.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register `project_cat` and `project_location`.
 *
 * @return void
 */
function es_register_taxonomies() {
	register_taxonomy(
		'project_cat',
		array( 'project' ),
		array(
			'labels'            => array(
				'name'              => _x( 'دسته‌های پروژه', 'taxonomy general name', 'erfan-sanat' ),
				'singular_name'     => _x( 'دستهٔ پروژه', 'taxonomy singular name', 'erfan-sanat' ),
				'search_items'      => __( 'جست‌وجوی دستهٔ پروژه', 'erfan-sanat' ),
				'all_items'         => __( 'همهٔ دسته‌های پروژه', 'erfan-sanat' ),
				'parent_item'       => __( 'دستهٔ مادر', 'erfan-sanat' ),
				'parent_item_colon' => __( 'دستهٔ مادر:', 'erfan-sanat' ),
				'edit_item'         => __( 'ویرایش دسته', 'erfan-sanat' ),
				'update_item'       => __( 'به‌روزرسانی دسته', 'erfan-sanat' ),
				'add_new_item'      => __( 'افزودن دستهٔ جدید', 'erfan-sanat' ),
				'new_item_name'     => __( 'نام دستهٔ جدید', 'erfan-sanat' ),
				'menu_name'         => __( 'دسته‌های پروژه', 'erfan-sanat' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'show_in_nav_menus' => true,
			'rewrite'           => array(
				'slug'         => 'project-category',
				'with_front'   => false,
				'hierarchical' => true,
			),
		)
	);

	register_taxonomy(
		'project_location',
		array( 'project' ),
		array(
			'labels'            => array(
				'name'              => _x( 'موقعیت پروژه', 'taxonomy general name', 'erfan-sanat' ),
				'singular_name'     => _x( 'موقعیت', 'taxonomy singular name', 'erfan-sanat' ),
				'search_items'      => __( 'جست‌وجوی موقعیت', 'erfan-sanat' ),
				'all_items'         => __( 'همهٔ موقعیت‌ها', 'erfan-sanat' ),
				'edit_item'         => __( 'ویرایش موقعیت', 'erfan-sanat' ),
				'update_item'       => __( 'به‌روزرسانی موقعیت', 'erfan-sanat' ),
				'add_new_item'      => __( 'افزودن موقعیت جدید', 'erfan-sanat' ),
				'new_item_name'     => __( 'نام موقعیت جدید', 'erfan-sanat' ),
				'menu_name'         => __( 'موقعیت پروژه', 'erfan-sanat' ),
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'show_in_nav_menus' => true,
			'rewrite'           => array(
				'slug'       => 'project-location',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'es_register_taxonomies', 6 );

/**
 * Project category terms shipped with the theme (used by the demo installer).
 *
 * @return array<string,string> slug => label
 */
function es_default_project_categories() {
	return array(
		'urban-beautification'            => __( 'زیباسازی شهری', 'erfan-sanat' ),
		'light-tunnels-walkways'          => __( 'تونل‌های نوری و مسیرهای عبوری', 'erfan-sanat' ),
		'bridges-monuments'               => __( 'پل‌ها و یادمان‌ها', 'erfan-sanat' ),
		'parks-landscapes'                => __( 'پارک‌ها و فضاهای سبز', 'erfan-sanat' ),
		'occasional-lighting-projects'    => __( 'پروژه‌های مناسبتی', 'erfan-sanat' ),
	);
}

/**
 * Project location terms shipped with the theme (used by the demo installer).
 *
 * @return array<string,string> slug => label
 */
function es_default_project_locations() {
	return array(
		'isfahan'      => __( 'اصفهان', 'erfan-sanat' ),
		'tehran'       => __( 'تهران', 'erfan-sanat' ),
		'mashhad'      => __( 'مشهد', 'erfan-sanat' ),
		'shiraz'       => __( 'شیراز', 'erfan-sanat' ),
		'south-coasts' => __( 'سواحل جنوب', 'erfan-sanat' ),
		'other-cities' => __( 'سایر شهرها', 'erfan-sanat' ),
	);
}

/**
 * Blog categories shipped with the theme (used by the demo installer).
 *
 * @return array<string,string> slug => label
 */
function es_default_blog_categories() {
	return array(
		'software-controllers-tutorials' => __( 'آموزش نرم‌افزار و کنترلرها', 'erfan-sanat' ),
		'lighting-standards'             => __( 'استانداردهای روشنایی', 'erfan-sanat' ),
		'troubleshooting-wiring'         => __( 'عیب‌یابی و سیم‌کشی', 'erfan-sanat' ),
		'company-news-events'            => __( 'اخبار و رویدادهای شرکت', 'erfan-sanat' ),
	);
}

/**
 * Blog tags shipped with the theme (used by the demo installer).
 *
 * @return array<string,string> slug => label
 */
function es_default_blog_tags() {
	return array(
		'ws2811-controller'          => __( 'کنترلر WS2811', 'erfan-sanat' ),
		'dmx-addressing'             => __( 'آدرس‌دهی DMX', 'erfan-sanat' ),
		'voltage-drop-calculation'   => __( 'محاسبهٔ افت ولتاژ', 'erfan-sanat' ),
	);
}

/**
 * WooCommerce product categories shipped with the theme.
 *
 * Parent slug => array( parent label, children slug => label ).
 *
 * @return array<string,array>
 */
function es_default_product_categories() {
	return array(
		'urban-lighting-elements'         => array(
			'label'    => __( 'المان‌های نورپردازی شهری', 'erfan-sanat' ),
			'children' => array(
				'square-elements'      => __( 'المان‌های مربعی', 'erfan-sanat' ),
				'urban-chandeliers'    => __( 'لوسترهای شهری', 'erfan-sanat' ),
				'pole-mounted-elements'=> __( 'المان‌های تیرنصب', 'erfan-sanat' ),
				'light-trees'          => __( 'درختان نوری', 'erfan-sanat' ),
				'light-tunnels'        => __( 'تونل‌های نوری', 'erfan-sanat' ),
			),
		),
		'decorative-light-strings'        => array(
			'label'    => __( 'ریسه‌های تزئینی', 'erfan-sanat' ),
			'children' => array(
				'pixel-strings-berry'  => __( 'ریسه‌های پیکسلی فندقی', 'erfan-sanat' ),
				'neon-flex'            => __( 'نئون فلکس', 'erfan-sanat' ),
				'strip-lights'         => __( 'نوارهای LED', 'erfan-sanat' ),
				'string-fairy-lights'  => __( 'ریسه‌های سوزنی و فلکسی', 'erfan-sanat' ),
			),
		),
		'architectural-facade-lighting'   => array(
			'label'    => __( 'نورپردازی نما و معماری', 'erfan-sanat' ),
			'children' => array(
				'wall-washers'   => __( 'والواشرها', 'erfan-sanat' ),
				'flood-lights'   => __( 'پروژکتورها', 'erfan-sanat' ),
				'inground-lights'=> __( 'چراغ‌های زمینی', 'erfan-sanat' ),
				'jet-lights'     => __( 'جت‌لایت‌ها', 'erfan-sanat' ),
			),
		),
		'pixel-point-lights'              => array(
			'label'    => __( 'پوینت‌لایت‌های پیکسلی', 'erfan-sanat' ),
			'children' => array(
				'dmx-point-lights' => __( 'پوینت‌لایت DMX', 'erfan-sanat' ),
				'spi-point-lights' => __( 'پوینت‌لایت SPI', 'erfan-sanat' ),
				'digital-tubes-3d' => __( 'تیوب‌های دیجیتال سه‌بعدی', 'erfan-sanat' ),
			),
		),
		'controllers-power-supplies'      => array(
			'label'    => __( 'کنترلرها و منابع تغذیه', 'erfan-sanat' ),
			'children' => array(
				'dmx-artnet-controllers'      => __( 'کنترلرهای DMX / ArtNet', 'erfan-sanat' ),
				'spi-led-controllers'         => __( 'کنترلرهای SPI', 'erfan-sanat' ),
				'switching-power-supplies'    => __( 'منابع تغذیه سوئیچینگ', 'erfan-sanat' ),
				'waterproof-connectors-cables'=> __( 'کانکتور و کابل ضدآب', 'erfan-sanat' ),
			),
		),
	);
}

/**
 * WooCommerce global attributes shipped with the theme.
 *
 * @return array<string,array>
 */
function es_default_product_attributes() {
	return array(
		'voltage'          => array(
			'label'  => __( 'ولتاژ', 'erfan-sanat' ),
			'type'   => 'select',
			'order'  => 'num',
			'terms'  => array(
				'12v-dc' => '12V DC',
				'24v-dc' => '24V DC',
				'220v-ac'=> '220V AC',
			),
		),
		'ip-rating'        => array(
			'label'  => __( 'درجهٔ حفاظت', 'erfan-sanat' ),
			'type'   => 'select',
			'order'  => 'name',
			'terms'  => array(
				'ip65' => 'IP65',
				'ip67' => 'IP67',
				'ip68' => 'IP68',
			),
		),
		'control-protocol' => array(
			'label'  => __( 'پروتکل کنترل', 'erfan-sanat' ),
			'type'   => 'select',
			'order'  => 'name',
			'terms'  => array(
				'dmx512'       => 'DMX512',
				'spi-ws2811'   => 'SPI - WS2811',
				'spi-ucs1903'  => 'SPI - UCS1903',
				'analog-mono'  => 'Analog / Mono',
			),
		),
		'light-color'      => array(
			'label'  => __( 'رنگ نور', 'erfan-sanat' ),
			'type'   => 'select',
			'order'  => 'name',
			'terms'  => array(
				'full-color-rgb'    => __( 'فول‌کالر RGB', 'erfan-sanat' ),
				'rgbw'              => 'RGBW',
				'sun-amber'         => __( 'کهربایی خورشیدی', 'erfan-sanat' ),
				'warm-white-3000k'  => __( 'سفید گرم ۳۰۰۰K', 'erfan-sanat' ),
				'cool-white-6000k'  => __( 'سفید سرد ۶۰۰۰K', 'erfan-sanat' ),
			),
		),
		'beam-angle'       => array(
			'label'  => __( 'زاویهٔ پرتو', 'erfan-sanat' ),
			'type'   => 'select',
			'order'  => 'name',
			'terms'  => array(
				'lens-15-deg'  => __( 'لنز ۱۵ درجه', 'erfan-sanat' ),
				'lens-45-deg'  => __( 'لنز ۴۵ درجه', 'erfan-sanat' ),
				'lens-120-deg' => __( 'لنز ۱۲۰ درجه', 'erfan-sanat' ),
			),
		),
		'body-material'    => array(
			'label'  => __( 'جنس بدنه', 'erfan-sanat' ),
			'type'   => 'select',
			'order'  => 'name',
			'terms'  => array(
				'aluminum-diecast' => __( 'آلومینیوم دایکست', 'erfan-sanat' ),
				'galvanized-iron'  => __( 'آهن گالوانیزه', 'erfan-sanat' ),
				'polycarbonate'    => __( 'پلی‌کربنات', 'erfan-sanat' ),
			),
		),
	);
}
