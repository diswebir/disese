<?php
/**
 * Demo / first-run content installer.
 *
 * Every function here is idempotent: running it twice updates instead of
 * duplicating. Content is created through WordPress APIs only, and every item
 * created by the installer is tagged with the `_es_demo` meta key so it can be
 * recognised later.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Find a post created by the installer by its slug.
 *
 * @param string $slug      Post slug.
 * @param string $post_type Post type.
 * @return int Post id or 0.
 */
function es_find_demo_post( $slug, $post_type = 'post' ) {
	$existing = get_posts(
		array(
			'name'           => $slug,
			'post_type'      => $post_type,
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);

	return $existing ? (int) $existing[0] : 0;
}

/**
 * Create or update a post from installer data.
 *
 * @param array $data Post data (post_title, post_name, post_content, ...).
 * @return int Post id.
 */
function es_upsert_demo_post( array $data ) {
	$post_type = isset( $data['post_type'] ) ? $data['post_type'] : 'post';

	$existing_id = 0;

	if ( ! empty( $data['lookup_meta'] ) && is_array( $data['lookup_meta'] ) ) {
		$key   = key( $data['lookup_meta'] );
		$value = reset( $data['lookup_meta'] );

		$found = get_posts(
			array(
				'post_type'      => $post_type,
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'post_status'    => 'any',
				'meta_query'     => array(
					array(
						'key'   => $key,
						'value' => $value,
					),
				),
			)
		);

		$existing_id = $found ? (int) $found[0] : 0;
		unset( $data['lookup_meta'] );
	}

	if ( ! $existing_id && ! empty( $data['post_name'] ) ) {
		$existing_id = es_find_demo_post( $data['post_name'], $post_type );
	}

	$data['ID'] = $existing_id;

	$post_id = wp_insert_post( wp_slash( $data ), true );

	if ( is_wp_error( $post_id ) ) {
		return 0;
	}

	update_post_meta( $post_id, '_es_demo', 1 );

	return (int) $post_id;
}

/**
 * Ensure a term exists and return its id.
 *
 * @param string $name     Term name.
 * @param string $taxonomy Taxonomy.
 * @param string $slug     Term slug.
 * @param int    $parent   Parent term id.
 * @return int Term id.
 */
function es_ensure_term( $name, $taxonomy, $slug = '', $parent = 0 ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return 0;
	}

	$slug     = $slug ? $slug : sanitize_title( $name );
	$existing = get_term_by( 'slug', $slug, $taxonomy );

	if ( $existing && ! is_wp_error( $existing ) ) {
		if ( $parent && (int) $existing->parent !== (int) $parent ) {
			wp_update_term( $existing->term_id, $taxonomy, array( 'parent' => (int) $parent ) );
		}

		return (int) $existing->term_id;
	}

	$created = wp_insert_term(
		$name,
		$taxonomy,
		array(
			'slug'   => $slug,
			'parent' => (int) $parent,
		)
	);

	if ( is_wp_error( $created ) ) {
		return 0;
	}

	return (int) $created['term_id'];
}

/**
 * Install the business taxonomy terms (product, project, blog).
 *
 * @return array<string,int> Slug => created/updated count.
 */
function es_install_default_terms() {
	$report = array(
		'product_cat'      => 0,
		'project_cat'      => 0,
		'project_location' => 0,
		'blog_cat'         => 0,
		'blog_tag'         => 0,
	);

	foreach ( es_default_project_categories() as $slug => $label ) {
		if ( es_ensure_term( $label, 'project_cat', $slug ) ) {
			$report['project_cat']++;
		}
	}

	foreach ( es_default_project_locations() as $slug => $label ) {
		if ( es_ensure_term( $label, 'project_location', $slug ) ) {
			$report['project_location']++;
		}
	}

	foreach ( es_default_blog_categories() as $slug => $label ) {
		if ( es_ensure_term( $label, 'category', $slug ) ) {
			$report['blog_cat']++;
		}
	}

	foreach ( es_default_blog_tags() as $slug => $label ) {
		if ( es_ensure_term( $label, 'post_tag', $slug ) ) {
			$report['blog_tag']++;
		}
	}

	if ( taxonomy_exists( 'product_cat' ) ) {
		foreach ( es_default_product_categories() as $parent_slug => $group ) {
			$parent_id = es_ensure_term( $group['label'], 'product_cat', $parent_slug );

			if ( $parent_id ) {
				$report['product_cat']++;
			}

			foreach ( $group['children'] as $child_slug => $child_label ) {
				if ( es_ensure_term( $child_label, 'product_cat', $child_slug, $parent_id ) ) {
					$report['product_cat']++;
				}
			}
		}
	}

	return $report;
}

/**
 * Install the WooCommerce global attributes and their terms.
 *
 * Uses the official WooCommerce CRUD API when available; falls back to the
 * taxonomy registration table for plain WordPress installs (no-op).
 *
 * @return int Number of attributes created/updated.
 */
function es_install_product_attributes() {
	if ( ! function_exists( 'wc_create_attribute' ) || ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
		return 0;
	}

	$created = 0;
	$existing = array();

	foreach ( wc_get_attribute_taxonomies() as $attribute ) {
		$existing[ $attribute->attribute_name ] = (int) $attribute->attribute_id;
	}

	foreach ( es_default_product_attributes() as $slug => $definition ) {
		$label   = $definition['label'];
		$taxonomy = wc_attribute_taxonomy_name( $slug );

		if ( ! isset( $existing[ $slug ] ) ) {
			$attribute_id = wc_create_attribute(
				array(
					'name'         => $label,
					'slug'         => $slug,
					'type'         => 'select',
					'order_by'     => $definition['order'],
					'has_archives' => false,
				)
			);

			if ( is_wp_error( $attribute_id ) ) {
				continue;
			}

			$created++;
		}

		// Terms can only be inserted once the pa_* taxonomy is registered.
		if ( ! taxonomy_exists( $taxonomy ) ) {
			register_taxonomy(
				$taxonomy,
				array( 'product' ),
				array(
					'hierarchical' => false,
					'show_ui'      => false,
					'query_var'    => true,
					'rewrite'      => false,
				)
			);
		}

		foreach ( $definition['terms'] as $term_slug => $term_name ) {
			es_ensure_term( $term_name, $taxonomy, $term_slug );
		}
	}

	if ( $created ) {
		delete_transient( 'wc_attribute_taxonomies' );
	}

	return $created;
}

/**
 * Create the WooCommerce core pages when WooCommerce is active.
 *
 * @return int Number of pages created or verified.
 */
function es_install_woocommerce_pages() {
	if ( ! es_woocommerce_active() ) {
		return 0;
	}

	$count = 0;

	if ( class_exists( 'WC_Install' ) && method_exists( 'WC_Install', 'create_pages' ) ) {
		WC_Install::create_pages();
	}

	$pages = array(
		'shop'      => array(
			'title'   => __( 'فروشگاه', 'erfan-sanat' ),
			'content' => '',
		),
		'cart'      => array(
			'title'   => __( 'سبد خرید', 'erfan-sanat' ),
			'content' => '[woocommerce_cart]',
		),
		'checkout'  => array(
			'title'   => __( 'تسویه‌حساب', 'erfan-sanat' ),
			'content' => '[woocommerce_checkout]',
		),
		'myaccount' => array(
			'title'   => __( 'حساب کاربری من', 'erfan-sanat' ),
			'content' => '[woocommerce_my_account]',
		),
	);

	foreach ( $pages as $key => $page ) {
		$page_id = (int) wc_get_page_id( $key );

		if ( $page_id > 0 && get_post( $page_id ) ) {
			$count++;
			continue;
		}

		$new_id = wp_insert_post(
			array(
				'post_title'     => $page['title'],
				'post_content'   => $page['content'],
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'comment_status' => 'closed',
			),
			true
		);

		if ( is_wp_error( $new_id ) ) {
			continue;
		}

		update_post_meta( (int) $new_id, '_es_demo', 1 );

		if ( 'shop' === $key ) {
			update_option( 'woocommerce_shop_page_id', (int) $new_id );
		} else {
			update_option( 'woocommerce_' . $key . '_page_id', (int) $new_id );
		}

		$count++;
	}

	return $count;
}

/**
 * Create a sample product (WooCommerce aware).
 *
 * @param array $data Product definition.
 * @return int Product id.
 */
function es_upsert_demo_product( array $data ) {
	if ( ! es_woocommerce_active() || ! function_exists( 'wc_get_product' ) ) {
		return 0;
	}

	$product_id = es_find_demo_post( $data['slug'], 'product' );

	$post_data = array(
		'ID'           => $product_id,
		'post_title'   => $data['title'],
		'post_name'    => $data['slug'],
		'post_content' => isset( $data['content'] ) ? $data['content'] : '',
		'post_excerpt' => isset( $data['excerpt'] ) ? $data['excerpt'] : '',
		'post_status'  => 'publish',
		'post_type'    => 'product',
	);

	$product_id = wp_insert_post( wp_slash( $post_data ), true );

	if ( is_wp_error( $product_id ) || ! $product_id ) {
		return 0;
	}

	update_post_meta( $product_id, '_es_demo', 1 );

	wp_set_object_terms( $product_id, 'simple', 'product_type' );

	update_post_meta( $product_id, '_sku', isset( $data['sku'] ) ? $data['sku'] : '' );
	update_post_meta( $product_id, '_regular_price', isset( $data['price'] ) ? (string) $data['price'] : '' );
	update_post_meta( $product_id, '_price', isset( $data['price'] ) ? (string) $data['price'] : '' );
	update_post_meta( $product_id, '_stock_status', 'instock' );
	update_post_meta( $product_id, '_manage_stock', 'no' );
	update_post_meta( $product_id, '_virtual', 'no' );
	update_post_meta( $product_id, '_sale_price', '' );

	// Erfan Sanat purchasing model.
	update_post_meta( $product_id, '_es_is_purchasable_online', ! empty( $data['purchasable'] ) );
	update_post_meta( $product_id, '_es_order_type', isset( $data['order_type'] ) ? $data['order_type'] : 'phone_inquiry' );
	update_post_meta( $product_id, '_es_custom_price_badge', isset( $data['badge'] ) ? $data['badge'] : '' );
	update_post_meta( $product_id, '_es_min_order_qty', isset( $data['min_qty'] ) ? (int) $data['min_qty'] : 1 );
	update_post_meta( $product_id, '_es_production_lead_time', isset( $data['lead_time'] ) ? $data['lead_time'] : '' );
	update_post_meta( $product_id, '_es_wattage_rating', isset( $data['wattage'] ) ? $data['wattage'] : 0 );
	update_post_meta( $product_id, '_es_chip_brand', isset( $data['chip'] ) ? $data['chip'] : '' );

	if ( ! empty( $data['categories'] ) ) {
		wp_set_object_terms( $product_id, $data['categories'], 'product_cat' );
	}

	// Global attributes.
	if ( ! empty( $data['attributes'] ) && function_exists( 'wc_get_attribute_taxonomies' ) ) {
		$attributes        = array();
		$attribute_counter = 0;

		foreach ( $data['attributes'] as $attribute_slug => $term_slugs ) {
			$taxonomy = wc_attribute_taxonomy_name( $attribute_slug );

			if ( ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}

			$term_ids = array();

			foreach ( (array) $term_slugs as $term_slug ) {
				$term = get_term_by( 'slug', $term_slug, $taxonomy );
				if ( $term && ! is_wp_error( $term ) ) {
					$term_ids[] = (int) $term->term_id;
				}
			}

			if ( ! $term_ids ) {
				continue;
			}

			$attribute = new WC_Product_Attribute();
			$attribute->set_id( 0 );
			$attribute->set_name( $taxonomy );
			$attribute->set_options( $term_ids );
			$attribute->set_position( $attribute_counter );
			$attribute->set_visible( true );
			$attribute->set_variation( false );

			$attributes[] = $attribute;
			$attribute_counter++;
		}

		if ( $attributes ) {
			$product = wc_get_product( $product_id );
			if ( $product ) {
				$product->set_attributes( $attributes );
				$product->save();
			}
		}
	}

	return (int) $product_id;
}

/**
 * Install the complete demo content set.
 *
 * @return array<string,int> Report of created/updated items.
 */
function es_install_demo_content() {
	$report = array(
		'projects' => 0,
		'pages'    => 0,
		'posts'    => 0,
		'products' => 0,
	);

	// 1) Terms first so content can be assigned.
	es_install_default_terms();

	if ( es_woocommerce_active() ) {
		es_install_product_attributes();
		es_install_woocommerce_pages();
	}

	// 2) Templates-driven pages.
	$pages = array(
		array(
			'title'    => __( 'درباره ما', 'erfan-sanat' ),
			'slug'     => 'about',
			'template' => 'templates/template-fullwidth.php',
			'content'  => '<p>' . __( 'شرکت دانش‌بنیان عرفان صنعت اصفهان، پیشگام در طراحی، تولید و اجرای پروژه‌های نورپردازی شهری و صنعتی است.', 'erfan-sanat' ) . '</p>',
		),
		array(
			'title'    => __( 'تماس با ما', 'erfan-sanat' ),
			'slug'     => 'contact',
			'template' => 'templates/template-contact.php',
			'content'  => '<p>' . __( 'برای مشاورهٔ رایگان، استعلام قیمت روز و راهنمایی فنی با ما در تماس باشید.', 'erfan-sanat' ) . '</p>',
		),
		array(
			'title'    => __( 'پروژه‌ها', 'erfan-sanat' ),
			'slug'     => 'projects',
			'template' => 'templates/template-projects.php',
			'content'  => '',
		),
		array(
			'title'    => __( 'محصولات', 'erfan-sanat' ),
			'slug'     => 'products',
			'template' => 'templates/template-products.php',
			'content'  => '',
		),
		array(
			'title'    => __( 'مقالات فنی', 'erfan-sanat' ),
			'slug'     => 'blog',
			'template' => 'templates/template-blog.php',
			'content'  => '',
		),
	);

	foreach ( $pages as $page ) {
		$page_id = es_find_demo_post( $page['slug'], 'page' );

		$page_id = es_upsert_demo_post(
			array(
				'ID'           => $page_id,
				'post_title'   => $page['title'],
				'post_name'    => $page['slug'],
				'post_content' => $page['content'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_author'  => get_current_user_id() ? get_current_user_id() : 1,
			)
		);

		if ( ! $page_id ) {
			continue;
		}

		if ( ! empty( $page['template'] ) ) {
			update_post_meta( $page_id, '_wp_page_template', $page['template'] );
		}

		$report['pages']++;
	}

	// 3) Projects.
	$projects = array(
		array(
			'title'      => __( 'تونل نوری کمانی — بلوار البرز', 'erfan-sanat' ),
			'slug'       => 'half-arch-light-tunnel-alborz',
			'excerpt'    => __( 'تونل نوری کمانی با ۹۰ هزار پیکسل فول‌کالر و کنترل DMX روی مسیر عبوری اصلی.', 'erfan-sanat' ),
			'cat'        => 'light-tunnels-walkways',
			'location'   => 'other-cities',
			'client'     => __( 'شهرداری منطقه', 'erfan-sanat' ),
			'pixels'     => 90000,
			'power'      => 42.5,
			'featured'   => true,
		),
		array(
			'title'      => __( 'گوی نورانی میدان شهدا — اصفهان', 'erfan-sanat' ),
			'slug'       => 'esfahan-shohada-light-sphere',
			'excerpt'    => __( 'المان حجمی کره‌ای درخشان به‌عنوان نقطه‌کانونی بصری میدان.', 'erfan-sanat' ),
			'cat'        => 'urban-beautification',
			'location'   => 'isfahan',
			'client'     => __( 'شهرداری اصفهان', 'erfan-sanat' ),
			'pixels'     => 24000,
			'power'      => 12.0,
			'featured'   => true,
		),
		array(
			'title'      => __( 'درخت نوری آرتام دو طبقه — قم', 'erfan-sanat' ),
			'slug'       => 'artam-light-tree-qom',
			'excerpt'    => __( 'المان درخت نوری دو طبقه با ۱۶ میلیون رنگ و کنترل اپلیکیشنی.', 'erfan-sanat' ),
			'cat'        => 'parks-landscapes',
			'location'   => 'other-cities',
			'client'     => __( 'شهرداری قم', 'erfan-sanat' ),
			'pixels'     => 48000,
			'power'      => 18.4,
			'featured'   => true,
		),
		array(
			'title'      => __( 'نورپردازی پل عابر — کرمان', 'erfan-sanat' ),
			'slug'       => 'kerman-pedestrian-bridge-lighting',
			'excerpt'    => __( 'معماری نوری پل با پروژکتورهای IP67 و کنترل هوشمند.', 'erfan-sanat' ),
			'cat'        => 'bridges-monuments',
			'location'   => 'other-cities',
			'client'     => __( 'سازمان عمرانی کرمان', 'erfan-sanat' ),
			'pixels'     => 16000,
			'power'      => 9.6,
			'featured'   => false,
		),
		array(
			'title'      => __( 'سقف نوری مکعبی کیش', 'erfan-sanat' ),
			'slug'       => 'cubic-light-canopy-kish',
			'excerpt'    => __( 'سقف نوری مکعبی با ریسه‌های پیکسلی و افکت‌های هم‌زمان.', 'erfan-sanat' ),
			'cat'        => 'occasional-lighting-projects',
			'location'   => 'south-coasts',
			'client'     => __( 'سازمان منطقهٔ آزاد', 'erfan-sanat' ),
			'pixels'     => 32000,
			'power'      => 14.2,
			'featured'   => false,
		),
		array(
			'title'      => __( 'نورپردازی میدان مرکزی — شیراز', 'erfan-sanat' ),
			'slug'       => 'shiraz-central-square-lighting',
			'excerpt'    => __( 'ترکیب المان‌های نوری و شست‌وشوی نما برای هویت‌بخشی به میدان.', 'erfan-sanat' ),
			'cat'        => 'urban-beautification',
			'location'   => 'shiraz',
			'client'     => __( 'شهرداری شیراز', 'erfan-sanat' ),
			'pixels'     => 52000,
			'power'      => 21.7,
			'featured'   => false,
		),
	);

	foreach ( $projects as $project ) {
		$content  = '<p>' . $project['excerpt'] . '</p>';
		$content .= '<p>' . __( 'این پروژه شامل طراحی اختصاصی، تولید در کارخانهٔ عرفان صنعت، نصب و راه‌اندازی کامل بوده است. کلیهٔ تجهیزات دارای گارانتی تعویض ۲۴ ماه و خدمات پس از فروش ۵ ساله هستند.', 'erfan-sanat' ) . '</p>';

		$project_id = es_upsert_demo_post(
			array(
				'post_title'   => $project['title'],
				'post_name'    => $project['slug'],
				'post_content' => $content,
				'post_excerpt' => $project['excerpt'],
				'post_status'  => 'publish',
				'post_type'    => 'project',
				'post_author'  => get_current_user_id() ? get_current_user_id() : 1,
			)
		);

		if ( ! $project_id ) {
			continue;
		}

		es_ensure_term( es_default_project_categories()[ $project['cat'] ], 'project_cat', $project['cat'] );
		es_ensure_term( es_default_project_locations()[ $project['location'] ], 'project_location', $project['location'] );

		wp_set_object_terms( $project_id, $project['cat'], 'project_cat' );
		wp_set_object_terms( $project_id, $project['location'], 'project_location' );

		update_post_meta( $project_id, '_es_project_client', $project['client'] );
		update_post_meta( $project_id, '_es_total_pixel_count', (int) $project['pixels'] );
		update_post_meta( $project_id, '_es_total_power_kw', (float) $project['power'] );
		update_post_meta( $project_id, '_es_completion_date', '۱۴۰۳/۰۲/۲۰' );
		update_post_meta( $project_id, '_es_featured', (bool) $project['featured'] );

		$report['projects']++;
	}

	// 4) Blog posts.
	$posts = array(
		array(
			'title'   => __( 'راهنمای آدرس‌دهی DMX برای پروژه‌های شهری', 'erfan-sanat' ),
			'slug'    => 'dmx-addressing-guide',
			'cat'     => 'software-controllers-tutorials',
			'tags'    => array( 'dmx-addressing', 'ws2811-controller' ),
			'excerpt' => __( 'آدرس‌دهی صحیح کنترلرها پایهٔ پایداری افکت‌های نوری در پروژه‌های بزرگ است؛ در این مقاله گام‌به‌گام بررسی می‌کنیم.', 'erfan-sanat' ),
			'reviewer'=> __( 'مهندس رضایی — واحد فنی', 'erfan-sanat' ),
		),
		array(
			'title'   => __( 'محاسبهٔ افت ولتاژ در ریسه‌های پیکسلی', 'erfan-sanat' ),
			'slug'    => 'voltage-drop-calculation',
			'cat'     => 'lighting-standards',
			'tags'    => array( 'voltage-drop-calculation' ),
			'excerpt' => __( 'چگونه با محاسبهٔ صحیح سطح مقطع و تزریق توان، از افت ولتاژ و کاهش شدت نور جلوگیری کنیم.', 'erfan-sanat' ),
			'reviewer'=> __( 'مهندس کریمی — واحد فنی', 'erfan-sanat' ),
		),
		array(
			'title'   => __( 'عیب‌یابی رایج در سیم‌کشی المان‌های نوری', 'erfan-sanat' ),
			'slug'    => 'lighting-wiring-troubleshooting',
			'cat'     => 'troubleshooting-wiring',
			'tags'    => array( 'ws2811-controller' ),
			'excerpt' => __( 'از قطعی سیم دیتا تا نویز منبع تغذیه؛ فهرست بررسی سریع برای تیم‌های اجرایی.', 'erfan-sanat' ),
			'reviewer'=> __( 'مهندس احمدی — پشتیبانی فنی', 'erfan-sanat' ),
		),
		array(
			'title'   => __( 'اخبار: بهره‌برداری از پروژهٔ نورپردازی تونل کمانی', 'erfan-sanat' ),
			'slug'    => 'tunnel-project-news',
			'cat'     => 'company-news-events',
			'tags'    => array(),
			'excerpt' => __( 'پروژهٔ تونل نوری کمانی با ۹۰ هزار پیکسل پس از ۴۵ روز کار پیوسته به بهره‌برداری رسید.', 'erfan-sanat' ),
			'reviewer'=> '',
		),
	);

	foreach ( $posts as $post ) {
		$faq = array();

		if ( 'dmx-addressing-guide' === $post['slug'] ) {
			$faq = array(
				array(
					'question' => __( 'حداکثر تعداد پیکسل روی یک خط DMX چقدر است؟', 'erfan-sanat' ),
					'answer'   => __( 'هر خط DMX می‌تواند ۵۱۲ کانال را پوشش دهد؛ با پیکسل‌های سه‌کاناله، عملاً ۱۷۰ پیکسل روی یک خط قابل آدرس‌دهی است.', 'erfan-sanat' ),
				),
				array(
					'question' => __( 'برای پروژه‌های بزرگ چند کنترلر لازم است؟', 'erfan-sanat' ),
					'answer'   => __( 'بسته به تعداد پیکسل و توزیع فیزیکی، معمولاً هر ۱۵۰۰ تا ۲۰۰۰ پیکسل یک کنترلر با تغذیهٔ مجزا توصیه می‌شود.', 'erfan-sanat' ),
				),
			);
		}

		$content  = '<p>' . $post['excerpt'] . '</p>';
		$content .= '<h2>' . __( 'مقدمه', 'erfan-sanat' ) . '</h2>';
		$content .= '<p>' . __( 'در پروژه‌های نورپردازی شهری، انتخاب درست تجهیزات و رعایت اصول فنی، تفاوت میان یک افکت پایدار و یک اجرای پرعارضه است. عرفان صنعت با تکیه بر دو دهه تجربه، این اصول را در قالب راهنماهای کاربردی منتشر می‌کند.', 'erfan-sanat' ) . '</p>';
		$content .= '<h2>' . __( 'نکات کلیدی اجرایی', 'erfan-sanat' ) . '</h2>';
		$content .= '<ul><li>' . __( 'استفاده از منبع تغذیهٔ با حاشیهٔ توان حداقل ۲۰ درصد', 'erfan-sanat' ) . '</li><li>' . __( 'تزریق توان در فواصل منظم برای جلوگیری از افت ولتاژ', 'erfan-sanat' ) . '</li><li>' . __( 'استفاده از کابل و کانکتور ضدآب مطابق IP68', 'erfan-sanat' ) . '</li></ul>';
		$content .= '<p>' . __( 'برای دریافت راهنمایی تخصصی در پروژهٔ خود، با کارشناسان فنی عرفان صنعت تماس بگیرید.', 'erfan-sanat' ) . '</p>';

		$post_id = es_upsert_demo_post(
			array(
				'post_title'   => $post['title'],
				'post_name'    => $post['slug'],
				'post_content' => $content,
				'post_excerpt' => $post['excerpt'],
				'post_status'  => 'publish',
				'post_type'    => 'post',
				'post_author'  => get_current_user_id() ? get_current_user_id() : 1,
			)
		);

		if ( ! $post_id ) {
			continue;
		}

		$category_id = es_ensure_term( es_default_blog_categories()[ $post['cat'] ], 'category', $post['cat'] );
		if ( $category_id ) {
			wp_set_post_terms( $post_id, array( $category_id ), 'category' );
		}

		if ( ! empty( $post['tags'] ) ) {
			$tag_ids = array();
			foreach ( $post['tags'] as $tag_slug ) {
				$tag_id = es_ensure_term( es_default_blog_tags()[ $tag_slug ], 'post_tag', $tag_slug );
				if ( $tag_id ) {
					$tag_ids[] = $tag_id;
				}
			}
			wp_set_post_terms( $post_id, $tag_ids, 'post_tag' );
		}

		// Populate the editorial metadata model: every demo article carries a
		// technical reviewer and a reading time derived from its body copy.
		update_post_meta(
			$post_id,
			'_es_technical_reviewer',
			$post['reviewer'] ? $post['reviewer'] : __( 'دفتر فنی عرفان صنعت', 'erfan-sanat' )
		);

		preg_match_all( '/\S+/u', wp_strip_all_tags( $content ), $word_matches );
		update_post_meta( $post_id, '_es_reading_time_min', max( 1, (int) ceil( count( $word_matches[0] ) / 190 ) ) );

		if ( $faq ) {
			update_post_meta( $post_id, '_es_faq_schema_repeater', $faq );
		}

		$report['posts']++;
	}

	// 5) Products.
	$products = array(
		array(
			'title'       => __( 'ریسه فندقی هفت‌رنگ پیکسلی', 'erfan-sanat' ),
			'slug'        => 'seven-color-pixel-string',
			'price'       => 6369000,
			'excerpt'     => __( 'ریسه پیکسلی فندقی با کنترل WS2811، بدنهٔ پلی‌کربنات ضدضربه و درجهٔ حفاظت IP67.', 'erfan-sanat' ),
			'categories'  => array( 'decorative-light-strings', 'pixel-strings-berry' ),
			'purchasable' => true,
			'order_type'  => 'online_cart',
			'min_qty'     => 5,
			'wattage'     => 12.5,
			'chip'        => 'WS2811',
			'lead_time'   => __( '۳ روز کاری', 'erfan-sanat' ),
			'sku'         => 'ES-STR-7C',
			'attributes'  => array(
				'voltage'          => array( '12v-dc' ),
				'ip-rating'        => array( 'ip67' ),
				'control-protocol' => array( 'spi-ws2811' ),
				'light-color'      => array( 'full-color-rgb' ),
				'body-material'    => array( 'polycarbonate' ),
			),
		),
		array(
			'title'       => __( 'پوینت‌لایت پیکسل ۴ سانتی‌متری', 'erfan-sanat' ),
			'slug'        => 'point-light-pixel-4cm',
			'price'       => 0,
			'excerpt'     => __( 'پوینت‌لایت پیکسلی فول‌کالر با قاب نگهدارنده PLT و درجهٔ حفاظت IP68.', 'erfan-sanat' ),
			'categories'  => array( 'pixel-point-lights', 'dmx-point-lights' ),
			'purchasable' => false,
			'order_type'  => 'phone_inquiry',
			'min_qty'     => 100,
			'badge'       => __( 'استعلام قیمت بر اساس تعداد', 'erfan-sanat' ),
			'wattage'     => 0.72,
			'chip'        => 'UCS1903',
			'lead_time'   => __( '۷ روز کاری', 'erfan-sanat' ),
			'sku'         => 'ES-PLP-4',
			'attributes'  => array(
				'voltage'          => array( '24v-dc' ),
				'ip-rating'        => array( 'ip68' ),
				'control-protocol' => array( 'spi-ucs1903' ),
				'beam-angle'       => array( 'lens-120-deg' ),
			),
		),
		array(
			'title'       => __( 'درخت نوری هوشمند آرتام', 'erfan-sanat' ),
			'slug'        => 'artam-smart-light-tree',
			'price'       => 0,
			'excerpt'     => __( 'المان درخت نوری با ۱۶ میلیون رنگ، کنترل اپلیکیشنی و پروتکل NRF.', 'erfan-sanat' ),
			'categories'  => array( 'urban-lighting-elements', 'light-trees' ),
			'purchasable' => false,
			'order_type'  => 'official_tender',
			'min_qty'     => 1,
			'badge'       => __( 'قیمت پروژه‌ای', 'erfan-sanat' ),
			'wattage'     => 240,
			'chip'        => 'NRF + SPI',
			'lead_time'   => __( '۲۱ روز کاری', 'erfan-sanat' ),
			'sku'         => 'ES-TREE-ARTAM',
			'attributes'  => array(
				'voltage'          => array( '220v-ac' ),
				'ip-rating'        => array( 'ip65' ),
				'body-material'    => array( 'galvanized-iron' ),
				'control-protocol' => array( 'analog-mono' ),
			),
		),
		array(
			'title'       => __( 'کنترلر DMX / ArtNet چهار پورت', 'erfan-sanat' ),
			'slug'        => 'dmx-artnet-4port-controller',
			'price'       => 4250000,
			'excerpt'     => __( 'کنترلر حرفه‌ای ArtNet با چهار پورت DMX512، مناسب پروژه‌های بزرگ شهری.', 'erfan-sanat' ),
			'categories'  => array( 'controllers-power-supplies', 'dmx-artnet-controllers' ),
			'purchasable' => true,
			'order_type'  => 'online_cart',
			'min_qty'     => 1,
			'wattage'     => 8,
			'chip'        => 'STM32',
			'lead_time'   => __( 'موجود در انبار', 'erfan-sanat' ),
			'sku'         => 'ES-CTL-DMX4',
			'attributes'  => array(
				'voltage'          => array( '220v-ac' ),
				'ip-rating'        => array( 'ip65' ),
				'control-protocol' => array( 'dmx512' ),
			),
		),
		array(
			'title'       => __( 'والواشر LED معماری ۳۶ وات', 'erfan-sanat' ),
			'slug'        => 'architectural-wall-washer-36w',
			'excerpt'     => __( 'والواشر LED با لنز ثانویه و زاویهٔ ۴۵ درجه برای نورپردازی نما.', 'erfan-sanat' ),
			'price'       => 0,
			'categories'  => array( 'architectural-facade-lighting', 'wall-washers' ),
			'purchasable' => false,
			'order_type'  => 'phone_inquiry',
			'min_qty'     => 10,
			'badge'       => __( 'استعلام روز', 'erfan-sanat' ),
			'wattage'     => 36,
			'chip'        => 'CREE',
			'lead_time'   => __( '۵ روز کاری', 'erfan-sanat' ),
			'sku'         => 'ES-WW-36',
			'attributes'  => array(
				'voltage'          => array( '220v-ac' ),
				'ip-rating'        => array( 'ip67' ),
				'beam-angle'       => array( 'lens-45-deg' ),
				'light-color'      => array( 'warm-white-3000k' ),
				'body-material'    => array( 'aluminum-diecast' ),
			),
		),
	);

	foreach ( $products as $product ) {
		$content  = '<p>' . $product['excerpt'] . '</p>';
		$content .= '<p>' . __( 'این محصول در کارخانهٔ عرفان صنعت اصفهان تولید می‌شود و دارای ۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش است.', 'erfan-sanat' ) . '</p>';

		$product_data               = $product;
		$product_data['title']      = $product['title'];
		$product_data['content']    = $content;

		if ( es_upsert_demo_product( $product_data ) ) {
			$report['products']++;
		}
	}

	return $report;
}
