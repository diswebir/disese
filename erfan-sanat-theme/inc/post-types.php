<?php
/**
 * Custom post types, permalink structures and rewrite rules.
 *
 * Business data lives in posts (never in theme options), per the project
 * architecture rules: projects are content, not settings.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the `project` post type.
 *
 * @return void
 */
function es_register_post_types() {
	$labels = array(
		'name'                  => _x( 'پروژه‌ها', 'Post type general name', 'erfan-sanat' ),
		'singular_name'         => _x( 'پروژه', 'Post type singular name', 'erfan-sanat' ),
		'menu_name'             => _x( 'پروژه‌ها', 'Admin Menu text', 'erfan-sanat' ),
		'name_admin_bar'        => _x( 'پروژه', 'Add New on Toolbar', 'erfan-sanat' ),
		'add_new'               => __( 'افزودن پروژه', 'erfan-sanat' ),
		'add_new_item'          => __( 'افزودن پروژهٔ جدید', 'erfan-sanat' ),
		'new_item'              => __( 'پروژهٔ جدید', 'erfan-sanat' ),
		'edit_item'             => __( 'ویرایش پروژه', 'erfan-sanat' ),
		'view_item'             => __( 'مشاهدهٔ پروژه', 'erfan-sanat' ),
		'all_items'             => __( 'همهٔ پروژه‌ها', 'erfan-sanat' ),
		'search_items'          => __( 'جست‌وجوی پروژه', 'erfan-sanat' ),
		'not_found'             => __( 'پروژه‌ای یافت نشد.', 'erfan-sanat' ),
		'not_found_in_trash'    => __( 'پروژه‌ای در زباله‌دان یافت نشد.', 'erfan-sanat' ),
		'featured_image'        => __( 'تصویر شاخص پروژه', 'erfan-sanat' ),
		'set_featured_image'    => __( 'انتخاب تصویر شاخص', 'erfan-sanat' ),
		'remove_featured_image' => __( 'حذف تصویر شاخص', 'erfan-sanat' ),
		'use_featured_image'    => __( 'استفاده به‌عنوان تصویر شاخص', 'erfan-sanat' ),
		'archives'              => __( 'آرشیو پروژه‌ها', 'erfan-sanat' ),
		'item_published'        => __( 'پروژه منتشر شد.', 'erfan-sanat' ),
		'item_updated'          => __( 'پروژه به‌روزرسانی شد.', 'erfan-sanat' ),
	);

	register_post_type(
		'project',
		array(
			'labels'             => $labels,
			'description'        => __( 'پروژه‌های نورپردازی شهری، تونل‌های نوری و المان‌ها.', 'erfan-sanat' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_admin_bar'  => true,
			'show_in_rest'       => true,
			'menu_position'      => 21,
			'menu_icon'          => 'dashicons-portfolio',
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'hierarchical'       => false,
			'has_archive'        => 'projects',
			'rewrite'            => array(
				'slug'       => 'project',
				'with_front' => false,
				'feeds'      => true,
				'pages'      => true,
			),
			'query_var'          => true,
			'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes' ),
			'taxonomies'         => array( 'project_cat', 'project_location' ),
			'show_in_rest'       => true,
		)
	);
}
add_action( 'init', 'es_register_post_types', 5 );

/**
 * Register post meta used by REST / block editor contexts.
 *
 * @return void
 */
function es_register_post_meta() {
	$text_meta = array(
		'_es_project_client'     => 'string',
		'_es_completion_date'    => 'string',
		'_es_project_map_coords' => 'string',
		'_es_project_drone_video'=> 'string',
		'_es_technical_reviewer' => 'string',
		'_es_software_project_file' => 'integer',
	);

	$post_types = array( 'post', 'project', 'product' );

	foreach ( $text_meta as $key => $type ) {
		foreach ( $post_types as $post_type ) {
			register_post_meta(
				$post_type,
				$key,
				array(
					'type'              => $type,
					'single'            => true,
					'show_in_rest'      => false,
					'sanitize_callback' => 'integer' === $type ? 'es_sanitize_meta_int' : 'sanitize_text_field',
					'auth_callback'     => 'es_can_edit_post_meta',
				)
			);
		}
	}

	$number_meta = array(
		'_es_total_pixel_count'    => 'integer',
		'_es_total_power_kw'       => 'number',
		'_es_reading_time_min'     => 'integer',
		'_es_min_order_qty'        => 'integer',
		'_es_wattage_rating'       => 'number',
	);

	foreach ( $number_meta as $key => $type ) {
		foreach ( $post_types as $post_type ) {
			register_post_meta(
				$post_type,
				$key,
				array(
					'type'              => $type,
					'single'            => true,
					'show_in_rest'      => false,
					'sanitize_callback' => 'integer' === $type ? 'es_sanitize_meta_int' : 'es_sanitize_meta_float',
					'auth_callback'     => 'es_can_edit_post_meta',
				)
			);
		}
	}
}

/**
 * Auth callback for registered post meta.
 *
 * @param bool   $allowed  Current decision.
 * @param string $meta_key Meta key.
 * @param int    $post_id  Post id.
 * @param int    $user_id  User id.
 * @return bool
 */
function es_can_edit_post_meta( $allowed, $meta_key, $post_id, $user_id ) {
	if ( $user_id && user_can( $user_id, 'edit_post', $post_id ) ) {
		return true;
	}

	return (bool) $allowed;
}

/**
 * Meta sanitizer: positive integer.
 *
 * WordPress passes four arguments to a registered meta sanitize callback
 * ($value, $meta_key, $object_type, $object_subtype). PHP's own absint() and
 * floatval() are internal functions that reject the extra arguments, so thin
 * userland wrappers are required here.
 *
 * @param mixed $value Raw value.
 * @return int
 */
function es_sanitize_meta_int( $value ) {
	return absint( $value );
}

/**
 * Meta sanitizer: float number.
 *
 * @param mixed $value Raw value.
 * @return float
 */
function es_sanitize_meta_float( $value ) {
	return (float) $value;
}

/**
 * Meta sanitizer: checkbox style boolean stored as '1' / ''.
 *
 * @param mixed $value Raw value.
 * @return string
 */
function es_sanitize_meta_bool( $value ) {
	return $value ? '1' : '';
}

add_action( 'init', 'es_register_post_meta', 6 );

/**
 * Blog permalink structure: /{blog_slug}/{postname}/.
 *
 * Implemented with a filter instead of rewriting the global permalink
 * setting, so the theme never touches site-wide configuration implicitly.
 *
 * @param string  $permalink Permalink.
 * @param WP_Post $post      Post object.
 * @return string
 */
function es_filter_post_permalink( $permalink, $post ) {
	if ( ! $post instanceof WP_Post || 'post' !== $post->post_type ) {
		return $permalink;
	}

	if ( ! es_opt( 'blog_slug_rewrite', true ) ) {
		return $permalink;
	}

	$slug = sanitize_title( es_opt( 'blog_slug', 'blog' ) );
	if ( ! $slug ) {
		return $permalink;
	}

	$structure = get_option( 'permalink_structure' );
	if ( ! $structure ) {
		return $permalink;
	}

	// Only rewrite pretty permalinks that are not already prefixed.
	if ( false !== strpos( $permalink, '/' . $slug . '/' ) ) {
		return $permalink;
	}

	$home_path = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$home_path = '/' . trim( $home_path, '/' );
	$home_path = '/' === $home_path ? '' : $home_path;

	$relative = $permalink;
	if ( $home_path && 0 === strpos( $permalink, home_url() ) ) {
		$relative = substr( $permalink, strlen( untrailingslashit( home_url() ) ) );
	} else {
		$relative = '/' . ltrim( (string) wp_parse_url( $permalink, PHP_URL_PATH ), '/' );
	}

	$relative = ltrim( preg_replace( '#^' . preg_quote( $home_path, '#' ) . '#', '', $relative ), '/' );

	return untrailingslashit( home_url() ) . '/' . $slug . '/' . $relative;
}
add_filter( 'post_link', 'es_filter_post_permalink', 10, 2 );

/**
 * Add rewrite rules for the blog slug and the shop aliases.
 *
 * @return void
 */
function es_register_rewrite_rules() {
	$slug = sanitize_title( es_opt( 'blog_slug', 'blog' ) );

	if ( $slug && ! (int) get_option( 'page_for_posts' ) ) {
		add_rewrite_rule(
			'^' . preg_quote( $slug, '/' ) . '/page/([0-9]+)/?$',
			'index.php?post_type=post&paged=$matches[1]',
			'top'
		);
		add_rewrite_rule(
			'^' . preg_quote( $slug, '/' ) . '/?$',
			'index.php?post_type=post',
			'top'
		);
		add_rewrite_rule(
			'^' . preg_quote( $slug, '/' ) . '/([^/]+)/?$',
			'index.php?name=$matches[1]',
			'top'
		);
	}

	// /products/ alias for the WooCommerce shop page.
	if ( es_opt( 'wc_shop_alias_products', true ) && function_exists( 'wc_get_page_id' ) ) {
		$shop_id = wc_get_page_id( 'shop' );
		if ( $shop_id > 0 ) {
			add_rewrite_rule( '^products/?$', 'index.php?post_type=product&page_id=' . (int) $shop_id, 'top' );
			add_rewrite_rule( '^products/page/([0-9]+)/?$', 'index.php?post_type=product&paged=$matches[1]', 'top' );
		}
	}
}
add_action( 'init', 'es_register_rewrite_rules', 7 );

/**
 * Keep query vars the theme relies on.
 *
 * @param string[] $vars Public query vars.
 * @return string[]
 */
function es_query_vars( $vars ) {
	$vars[] = 'es_filter_cat';
	$vars[] = 'es_filter_location';
	$vars[] = 'es_orderby';
	$vars[] = 'es_min_price';
	$vars[] = 'es_max_price';

	return $vars;
}
add_filter( 'query_vars', 'es_query_vars' );

/**
 * Flush rewrite rules on activation and when the blog slug changes.
 *
 * @return void
 */
function es_flush_rewrite_rules() {
	es_register_rewrite_rules();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'es_flush_rewrite_rules' );

/**
 * Rebuild rewrite rules after an options save that changes routing.
 *
 * @param string $tab Saved tab.
 * @return void
 */
function es_maybe_flush_rewrites_after_save( $tab ) {
	if ( in_array( $tab, array( 'blog', 'woocommerce', 'advanced' ), true ) ) {
		es_flush_rewrite_rules();
	}
}
add_action( 'es_options_saved', 'es_maybe_flush_rewrites_after_save' );

/**
 * Ensure the blog archive resolves even without a "posts page".
 *
 * @param WP_Query $query Main query.
 * @return void
 */
function es_fix_blog_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->is_home() && (int) get_option( 'page_for_posts' ) ) {
		$query->set( 'post_type', 'post' );
	}
}
add_action( 'pre_get_posts', 'es_fix_blog_archive_query' );

/**
 * Projects archive ordering and pagination from theme options.
 *
 * @param WP_Query $query Main query.
 * @return void
 */
function es_projects_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( ! $query->is_post_type_archive( 'project' ) && ! $query->is_tax( array( 'project_cat', 'project_location' ) ) ) {
		return;
	}

	$per_page = (int) es_opt( 'project_per_page', 9 );
	if ( $per_page > 0 ) {
		$query->set( 'posts_per_page', $per_page );
	}

	$cat      = sanitize_title( get_query_var( 'es_filter_cat' ) );
	$location = sanitize_title( get_query_var( 'es_filter_location' ) );

	$tax_query = array();

	if ( $cat ) {
		$tax_query[] = array(
			'taxonomy' => 'project_cat',
			'field'    => 'slug',
			'terms'    => $cat,
		);
	}

	if ( $location ) {
		$tax_query[] = array(
			'taxonomy' => 'project_location',
			'field'    => 'slug',
			'terms'    => $location,
		);
	}

	if ( $tax_query ) {
		$query->set( 'tax_query', $tax_query );
	}

	$orderby = sanitize_key( get_query_var( 'es_orderby' ) );
	if ( 'date_asc' === $orderby ) {
		$query->set( 'orderby', 'date' );
		$query->set( 'order', 'ASC' );
	} elseif ( 'title' === $orderby ) {
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'es_projects_archive_query' );

/**
 * Admin toolbar shortcut: add a project straight from the toolbar.
 *
 * The `admin_bar_menu` hook passes the WP_Admin_Bar object (not an array), so
 * the node has to be registered through add_node().
 *
 * @param WP_Admin_Bar $wp_admin_bar Toolbar instance.
 * @return void
 */
function es_admin_bar_links( $wp_admin_bar ) {
	if ( ! current_user_can( 'edit_posts' ) || ! $wp_admin_bar instanceof WP_Admin_Bar ) {
		return;
	}

	$wp_admin_bar->add_node(
		array(
			'id'    => 'es-new-project',
			'title' => __( 'پروژهٔ جدید', 'erfan-sanat' ),
			'href'  => admin_url( 'post-new.php?post_type=project' ),
		)
	);
}
add_action( 'admin_bar_menu', 'es_admin_bar_links', 80 );
