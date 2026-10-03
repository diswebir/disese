<?php
/**
 * WooCommerce integration.
 *
 * The store keeps the native WooCommerce flow (archive, single, cart,
 * checkout, my account, variable products, attributes) and adds the company's
 * purchasing model on top of it: online cart, phone inquiry or official
 * tender, each with its own call to action, price label and quantity rules.
 *
 * Every function is guarded by es_woocommerce_active() so the theme keeps
 * working on a site without WooCommerce.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is WooCommerce loaded?
 *
 * @return bool
 */
function es_woocommerce_active() {
	return class_exists( 'WooCommerce' ) && function_exists( 'wc_get_product' );
}

/**
 * Theme support for WooCommerce.
 *
 * @return void
 */
function es_woocommerce_setup() {
	if ( ! es_woocommerce_active() ) {
		return;
	}

	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 1200,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				// Kept static on purpose: options must not be read this early.
				'default_columns' => 3,
				'min_columns'     => 2,
				'max_columns'     => 4,
			),
		)
	);

	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'es_woocommerce_setup', 20 );

/* -------------------------------------------------------------------------
 * Layout wrappers
 * ---------------------------------------------------------------------- */

/**
 * Open the WooCommerce content wrapper.
 *
 * @return void
 */
function es_woocommerce_wrapper_start() {
	echo '<div class="es-container es-woo"><div class="es-woo__content">';
}

/**
 * Close the WooCommerce content wrapper.
 *
 * @return void
 */
function es_woocommerce_wrapper_end() {
	echo '</div></div>';
}

if ( es_woocommerce_active() ) {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	add_action( 'woocommerce_before_main_content', 'es_woocommerce_wrapper_start', 10 );
	add_action( 'woocommerce_after_main_content', 'es_woocommerce_wrapper_end', 10 );

	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	add_action( 'woocommerce_before_main_content', 'es_woocommerce_breadcrumb', 20 );

	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
	add_action( 'woocommerce_sidebar', 'es_woocommerce_sidebar', 10 );
}

/**
 * Breadcrumb, rendered through the theme's own breadcrumb builder.
 *
 * @return void
 */
function es_woocommerce_breadcrumb() {
	if ( ! es_opt( 'wc_show_breadcrumb', true ) ) {
		return;
	}

	es_breadcrumbs();
}

/**
 * Shop sidebar.
 *
 * @return void
 */
function es_woocommerce_sidebar() {
	if ( ! es_opt( 'product_archive_sidebar', true ) ) {
		return;
	}

	get_sidebar( 'shop' );
}

/* -------------------------------------------------------------------------
 * Loop and archive settings
 * ---------------------------------------------------------------------- */

/**
 * Products per page.
 *
 * @return int
 */
function es_woocommerce_products_per_page() {
	return max( 1, (int) es_opt( 'product_per_page', 12 ) );
}
add_filter( 'loop_shop_per_page', 'es_woocommerce_products_per_page', 20 );

/**
 * Loop columns.
 *
 * @return int
 */
function es_woocommerce_loop_columns() {
	return max( 2, min( 4, (int) es_opt( 'product_archive_columns', 3 ) ) );
}
add_filter( 'loop_shop_columns', 'es_woocommerce_loop_columns', 20 );

/**
 * Related products arguments.
 *
 * @param array $args Related products args.
 * @return array
 */
function es_woocommerce_related_args( $args ) {
	$args['posts_per_page'] = max( 1, (int) es_opt( 'product_related_count', 3 ) );
	$args['columns']        = es_woocommerce_loop_columns();

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'es_woocommerce_related_args', 20 );

/**
 * Archive heading from the theme options.
 *
 * @return void
 */
function es_woocommerce_archive_heading() {
	if ( ! is_shop() && ! is_product_taxonomy() ) {
		return;
	}

	$title = (string) es_opt( 'wc_shop_title', __( 'فروشگاه تجهیزات نورپردازی', 'erfan-sanat' ) );
	$text  = (string) es_opt( 'wc_shop_text', '' );

	if ( is_product_taxonomy() ) {
		$term  = get_queried_object();
		$title = $term && ! is_wp_error( $term ) ? $term->name : $title;
		$text  = $term && ! is_wp_error( $term ) && $term->description ? $term->description : $text;
	}

	es_section_header(
		array(
			'eyebrow' => __( 'فروشگاه', 'erfan-sanat' ),
			'title'   => $title,
			'text'    => wp_strip_all_tags( $text ),
			'tag'     => 'h1',
			'align'   => 'start',
		)
	);
}
add_action( 'woocommerce_before_shop_loop', 'es_woocommerce_archive_heading', 5 );

/* -------------------------------------------------------------------------
 * Purchasing model
 * ---------------------------------------------------------------------- */

/**
 * Is the product purchasable through the online cart?
 *
 * @param int $product_id Product id.
 * @return bool
 */
function es_product_is_purchasable_online( $product_id ) {
	$flag = get_post_meta( $product_id, '_es_is_purchasable_online', true );

	// Unset flag keeps the native WooCommerce behaviour.
	if ( '' === $flag ) {
		$order_type = (string) get_post_meta( $product_id, '_es_order_type', true );

		return '' === $order_type || 'online_cart' === $order_type;
	}

	return (bool) $flag;
}

/**
 * Effective purchasing model of a product.
 *
 * @param int $product_id Product id.
 * @return string online_cart|phone_inquiry|official_tender
 */
function es_product_purchase_model( $product_id ) {
	if ( ! es_product_is_purchasable_online( $product_id ) ) {
		$type = (string) get_post_meta( $product_id, '_es_order_type', true );

		if ( in_array( $type, array( 'phone_inquiry', 'official_tender' ), true ) ) {
			return $type;
		}

		return 'phone_inquiry';
	}

	return 'online_cart';
}

/**
 * Inquiry phone for a product (falls back to the site phone).
 *
 * @param int $product_id Product id.
 * @return string
 */
function es_product_inquiry_phone( $product_id ) {
	$phone = (string) get_post_meta( $product_id, '_es_inquiry_phone', true );

	if ( ! $phone ) {
		$phone = (string) es_opt( 'phone_sales', '' );
	}

	if ( ! $phone ) {
		$phone = (string) es_opt( 'phone_primary', '' );
	}

	return $phone;
}

/**
 * Replace the price label when a product carries a custom badge.
 *
 * @param string     $price_html Price html.
 * @param WC_Product $product    Product.
 * @return string
 */
function es_woocommerce_price_html( $price_html, $product ) {
	if ( ! $product instanceof WC_Product ) {
		return $price_html;
	}

	$badge = (string) get_post_meta( $product->get_id(), '_es_custom_price_badge', true );

	if ( $badge ) {
		return '<span class="es-price-badge">' . esc_html( $badge ) . '</span>';
	}

	if ( ! es_product_is_purchasable_online( $product->get_id() ) ) {
		$label = (string) es_opt( 'product_contact_price_label', __( 'قیمت: تماس بگیرید', 'erfan-sanat' ) );

		return '<span class="es-price-badge es-price-badge--inquiry">' . esc_html( $label ) . '</span>';
	}

	return $price_html;
}
add_filter( 'woocommerce_get_price_html', 'es_woocommerce_price_html', 20, 2 );

/**
 * Remove the online purchase ability for inquiry-only products.
 *
 * @param bool       $purchasable Current state.
 * @param WC_Product $product     Product.
 * @return bool
 */
function es_woocommerce_is_purchasable( $purchasable, $product ) {
	if ( is_admin() || ! $product instanceof WC_Product ) {
		return $purchasable;
	}

	if ( ! es_product_is_purchasable_online( $product->get_id() ) ) {
		return false;
	}

	return $purchasable;
}
add_filter( 'woocommerce_is_purchasable', 'es_woocommerce_is_purchasable', 20, 2 );

/**
 * Query arguments for the shop loop: honour the catalog mode.
 *
 * @param array $args Loop args.
 * @return array
 */
function es_woocommerce_loop_args( $args ) {
	$args['columns'] = es_woocommerce_loop_columns();

	return $args;
}
add_filter( 'woocommerce_loop_query_args', 'es_woocommerce_loop_args', 20 );

/**
 * Show or hide the native add-to-cart button and inject our CTA instead.
 *
 * @return void
 */
function es_woocommerce_purchase_cta() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$model = es_product_purchase_model( $product->get_id() );

	if ( 'online_cart' === $model ) {
		return;
	}

	$phone   = es_product_inquiry_phone( $product->get_id() );
	$tel     = es_phone_href( $phone );
	$subject = rawurlencode( sprintf( '%s — %s', __( 'استعلام محصول', 'erfan-sanat' ), $product->get_name() ) );

	$text = 'official_tender' === $model
		? (string) es_opt( 'product_tender_cta_text', __( 'ارسال استعلام مناقصه', 'erfan-sanat' ) )
		: (string) es_opt( 'product_inquiry_cta_text', __( 'استعلام قیمت و مشاوره', 'erfan-sanat' ) );

	echo '<div class="es-product-cta es-product-cta--' . esc_attr( $model ) . '">';

	if ( 'official_tender' === $model ) {
		printf(
			'<a class="es-btn es-btn--primary es-btn--lg" href="mailto:%1$s?subject=%2$s">%3$s</a>',
			esc_attr( antispambot( (string) es_opt( 'email_primary', get_option( 'admin_email' ) ) ) ),
			esc_attr( $subject ),
			esc_html( $text )
		);
	} elseif ( $tel ) {
		printf(
			'<a class="es-btn es-btn--primary es-btn--lg" href="%1$s">%2$s %3$s</a>',
			esc_url( $tel ),
			es_get_icon( 'phone', 'es-icon', 18 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html( $text )
		);
	}

	$note = (string) es_opt( 'wc_inquiry_note', '' );

	if ( $note ) {
		printf( '<p class="es-product-cta__note">%s</p>', esc_html( $note ) );
	}

	echo '</div>';
}
add_action( 'woocommerce_after_shop_loop_item', 'es_woocommerce_purchase_cta', 5 );
add_action( 'woocommerce_single_product_summary', 'es_woocommerce_single_purchase_cta', 35 );

/**
 * Single product: replace add-to-cart with the inquiry CTA when needed.
 *
 * @return void
 */
function es_woocommerce_single_purchase_cta() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	if ( 'online_cart' === es_product_purchase_model( $product->get_id() ) ) {
		return;
	}

	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	es_woocommerce_purchase_cta();
}

/**
 * Enforce the minimum order quantity on the native add-to-cart flow.
 *
 * @param array $args Quantity input arguments.
 * @return array
 */
function es_quantity_input_args( $args ) {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return $args;
	}

	$min = (int) get_post_meta( $product->get_id(), '_es_min_order_qty', true );

	if ( $min > 1 ) {
		$args['min_value'] = $min;

		if ( isset( $args['input_value'] ) && (int) $args['input_value'] < $min ) {
			$args['input_value'] = $min;
		}
	}

	return $args;
}
add_filter( 'woocommerce_quantity_input_args', 'es_quantity_input_args', 20 );

/**
 * Validate the minimum quantity server side.
 *
 * @param bool $passed     Current validation state.
 * @param int  $product_id Product id.
 * @param int  $quantity   Quantity.
 * @return bool
 */
function es_add_to_cart_validation( $passed, $product_id, $quantity ) {
	$min = (int) get_post_meta( $product_id, '_es_min_order_qty', true );

	if ( $min > 1 && (int) $quantity < $min ) {
		wc_add_notice(
			sprintf(
				/* translators: 1: quantity, 2: minimum */
				__( 'حداقل تعداد سفارش این محصول %1$s عدد است (تعداد انتخابی شما: %2$s).', 'erfan-sanat' ),
				es_num( number_format_i18n( $min ) ),
				es_num( number_format_i18n( (int) $quantity ) )
			),
			'error'
		);

		return false;
	}

	// Inquiry-only products cannot be added to the cart.
	if ( ! es_product_is_purchasable_online( $product_id ) ) {
		wc_add_notice( __( 'این محصول به‌صورت آنلاین قابل خرید نیست؛ برای استعلام قیمت با کارشناسان تماس بگیرید.', 'erfan-sanat' ), 'error' );

		return false;
	}

	return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'es_add_to_cart_validation', 20, 3 );

/* -------------------------------------------------------------------------
 * Single product extras
 * ---------------------------------------------------------------------- */

/**
 * Technical specification table on the single product page.
 *
 * @return void
 */
function es_woocommerce_specs_table() {
	global $product;

	if ( ! $product instanceof WC_Product || ! es_opt( 'product_show_attributes_table', true ) ) {
		return;
	}

	$rows = es_product_spec_rows( $product->get_id() );

	if ( ! $rows ) {
		return;
	}

	echo '<section class="es-specs" aria-labelledby="es-specs-title">';
	printf( '<h2 class="es-specs__title" id="es-specs-title">%s</h2>', esc_html__( 'مشخصات فنی', 'erfan-sanat' ) );
	echo '<table class="es-specs__table"><tbody>';

	foreach ( $rows as $label => $value ) {
		printf(
			'<tr><th scope="row">%1$s</th><td>%2$s</td></tr>',
			esc_html( $label ),
			wp_kses_post( $value )
		);
	}

	echo '</tbody></table></section>';
}
add_action( 'woocommerce_single_product_summary', 'es_woocommerce_specs_table', 45 );

/**
 * Specification rows for a product (meta + global attributes).
 *
 * @param int $product_id Product id.
 * @return array<string,string>
 */
function es_product_spec_rows( $product_id ) {
	$rows = array();

	$map = array(
		'_es_wattage_rating'       => array( __( 'توان مصرفی', 'erfan-sanat' ), 'W' ),
		'_es_chip_brand'           => array( __( 'برند چیپ / درایور', 'erfan-sanat' ), '' ),
		'_es_production_lead_time' => array( __( 'زمان تولید و تحویل', 'erfan-sanat' ), '' ),
	);

	foreach ( $map as $key => $config ) {
		$value = get_post_meta( $product_id, $key, true );

		if ( '' !== $value && 0 !== $value && '0' !== $value ) {
			$display = es_num( (string) $value ) . ( $config[1] ? ' ' . $config[1] : '' );
			$rows[ $config[0] ] = $display;
		}
	}

	$min_qty = (int) get_post_meta( $product_id, '_es_min_order_qty', true );

	if ( $min_qty > 1 ) {
		$rows[ __( 'حداقل تعداد سفارش', 'erfan-sanat' ) ] = es_num( number_format_i18n( $min_qty ) ) . ' ' . __( 'عدد', 'erfan-sanat' );
	}

	// Global attributes (pa_*) registered by WooCommerce.
	$product = wc_get_product( $product_id );

	if ( $product ) {
		$attributes = $product->get_attributes();

		foreach ( $attributes as $attribute ) {
			if ( ! $attribute instanceof WC_Product_Attribute ) {
				continue;
			}

			$label = wc_attribute_label( $attribute->get_name() );
			$value = '';

			if ( $attribute->is_taxonomy() ) {
				$terms = wc_get_product_terms( $product_id, $attribute->get_name(), array( 'fields' => 'names' ) );
				$value = implode( '، ', $terms );
			} else {
				$value = implode( '، ', $attribute->get_options() );
			}

			if ( $value ) {
				$rows[ $label ] = esc_html( $value );
			}
		}
	}

	/**
	 * Filters the product specification rows.
	 *
	 * @param array $rows       Label => value.
	 * @param int   $product_id Product id.
	 */
	return apply_filters( 'es_product_spec_rows', $rows, $product_id );
}

/**
 * Product tabs: description, specs, downloads (datasheet / schematic / video).
 *
 * @param array $tabs Existing tabs.
 * @return array
 */
function es_woocommerce_product_tabs( $tabs ) {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return $tabs;
	}

	$product_id = $product->get_id();
	$datasheet  = (int) get_post_meta( $product_id, '_es_technical_datasheet_pdf', true );
	$schematic  = (int) get_post_meta( $product_id, '_es_wiring_schematic_img', true );
	$video      = (string) get_post_meta( $product_id, '_es_demo_video_url', true );

	if ( $datasheet || $schematic || $video ) {
		$tabs['es_media'] = array(
			'title'    => __( 'دیتاشیت و ویدیو', 'erfan-sanat' ),
			'priority' => 25,
			'callback' => 'es_woocommerce_media_tab',
		);
	}

	if ( $product->has_attributes() ) {
		$tabs['additional_information']['priority'] = 20;
	}

	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'es_woocommerce_product_tabs', 20 );

/**
 * Render the datasheet/schematic/video tab.
 *
 * @return void
 */
function es_woocommerce_media_tab() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$product_id = $product->get_id();
	$datasheet  = (int) get_post_meta( $product_id, '_es_technical_datasheet_pdf', true );
	$schematic  = (int) get_post_meta( $product_id, '_es_wiring_schematic_img', true );
	$video      = (string) get_post_meta( $product_id, '_es_demo_video_url', true );

	echo '<div class="es-product-media">';

	if ( $datasheet ) {
		$url = wp_get_attachment_url( $datasheet );

		if ( $url ) {
			printf(
				'<a class="es-btn es-btn--outline" href="%1$s" download>%2$s</a>',
				esc_url( $url ),
				esc_html__( 'دانلود دیتاشیت فنی', 'erfan-sanat' )
			);
		}
	}

	if ( $schematic ) {
		echo '<figure class="es-product-media__figure">';
		echo wp_get_attachment_image( $schematic, 'large', false, array( 'class' => 'es-product-media__image', 'loading' => 'lazy' ) );
		printf( '<figcaption>%s</figcaption>', esc_html__( 'شماتیک سیم‌کشی و نقشهٔ اتصال', 'erfan-sanat' ) );
		echo '</figure>';
	}

	if ( $video ) {
		printf(
			'<p class="es-product-media__video"><a class="es-btn es-btn--primary" href="%1$s" target="_blank" rel="noopener nofollow">%2$s</a></p>',
			esc_url( $video ),
			esc_html__( 'تماشای ویدیوی محصول', 'erfan-sanat' )
		);
	}

	echo '</div>';
}

/**
 * Trust badges under the single product summary.
 *
 * @return void
 */
function es_woocommerce_trust_items() {
	$items = es_opt( 'wc_trust_items', array() );

	if ( ! is_array( $items ) || ! $items ) {
		return;
	}

	echo '<ul class="es-trust">';

	foreach ( $items as $item ) {
		if ( empty( $item['title'] ) ) {
			continue;
		}

		printf(
			'<li class="es-trust__item">%1$s<span>%2$s</span></li>',
			es_get_icon( isset( $item['icon'] ) ? $item['icon'] : 'certified', 'es-icon', 18 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html( $item['title'] )
		);
	}

	echo '</ul>';
}
add_action( 'woocommerce_single_product_summary', 'es_woocommerce_trust_items', 40 );

/**
 * Cart/checkout notes from the options.
 *
 * @param string $message Existing message.
 * @return string
 */
function es_woocommerce_checkout_note( $message ) {
	if ( ! es_opt( 'wc_enable_checkout_note', true ) ) {
		return $message;
	}

	$note = (string) es_opt( 'wc_checkout_note', '' );

	return $note ? $note : $message;
}
add_filter( 'woocommerce_checkout_privacy', 'es_woocommerce_checkout_note' );

/**
 * Header cart link (count + aria label).
 *
 * @return void
 */
function es_header_cart_link() {
	if ( ! es_woocommerce_active() ) {
		return;
	}

	$count = (int) WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;

	printf(
		'<a class="es-header-action es-header-action--cart%1$s" href="%2$s" data-es-cart-count="%3$d">
			%4$s<span class="es-header-action__count" aria-hidden="true">%5$s</span>
			<span class="screen-reader-text">%6$s</span>
		</a>',
		$count ? ' has-items' : '',
		esc_url( wc_get_cart_url() ),
		$count,
		es_get_icon( 'cart', 'es-icon', 22 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html( es_num( number_format_i18n( $count ) ) ),
		esc_html(
			sprintf(
				/* translators: %s: number of items */
				__( 'سبد خرید — %s کالا', 'erfan-sanat' ),
				es_num( number_format_i18n( $count ) )
			)
		)
	);
}

/**
 * Header account link.
 *
 * @return void
 */
function es_header_account_link() {
	if ( ! es_woocommerce_active() ) {
		return;
	}

	printf(
		'<a class="es-header-action es-header-action--account" href="%1$s">%2$s<span class="screen-reader-text">%3$s</span></a>',
		esc_url( wc_get_page_permalink( 'myaccount' ) ),
		es_get_icon( 'user', 'es-icon', 22 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html__( 'حساب کاربری من', 'erfan-sanat' )
	);
}

/**
 * Shop alias: /products/ resolves to the shop page.
 *
 * @return void
 */
function es_woocommerce_alias_rewrite() {
	if ( ! es_woocommerce_active() || ! es_opt( 'wc_shop_alias_products', true ) ) {
		return;
	}

	add_rewrite_rule( '^products/?$', 'index.php?post_type=product', 'top' );
}
add_action( 'init', 'es_woocommerce_alias_rewrite', 20 );

/**
 * Small feature chips shown on a product card.
 *
 * Combines the numeric meta fields with the most relevant global attributes
 * (voltage, IP rating, control protocol, light colour, beam angle).
 *
 * @param int $product_id Product id.
 * @return string[]
 */
function es_product_card_chips( $product_id ) {
	$chips = array();
	$watts = (float) get_post_meta( $product_id, '_es_wattage_rating', true );

	if ( $watts ) {
		$chips[] = es_num( (string) $watts ) . ' ' . __( 'وات', 'erfan-sanat' );
	}

	if ( ! es_woocommerce_active() ) {
		/**
		 * Filters the product card chips.
		 *
		 * @param string[] $chips      Chip labels.
		 * @param int      $product_id Product id.
		 */
		return apply_filters( 'es_product_card_chips', $chips, $product_id );
	}

	$chip_attributes = array( 'pa_voltage', 'pa_ip_rating', 'pa_control_protocol', 'pa_light_color', 'pa_beam_angle' );
	$product         = wc_get_product( $product_id );

	if ( $product ) {
		foreach ( $chip_attributes as $taxonomy ) {
			if ( count( $chips ) >= 4 ) {
				break;
			}

			if ( ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}

			$terms = wc_get_product_terms( $product_id, $taxonomy, array( 'fields' => 'names' ) );

			if ( ! $terms ) {
				continue;
			}

			foreach ( $terms as $term_name ) {
				$chips[] = $term_name;

				if ( count( $chips ) >= 4 ) {
					break;
				}
			}
		}
	}

	/** This filter is documented above. */
	return apply_filters( 'es_product_card_chips', array_slice( $chips, 0, 4 ), $product_id );
}
