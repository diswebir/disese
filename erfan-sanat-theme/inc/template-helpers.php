<?php
/**
 * View helpers shared by every template and template part.
 *
 * Templates stay declarative: fetching media, icons, breadcrumbs, related
 * content and formatting logic lives here so nothing is duplicated.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inline SVG icon library (self-hosted, no icon font, no external request).
 *
 * @param string $name Icon key.
 * @return string Inner SVG markup (already KSES-filtered).
 */
function es_icon_library( $name ) {
	$path = static function ( $d ) {
		return '<path d="' . $d . '"/>';
	};

	$icons = array(
		'spark'      => '<path d="M12 2l1.9 5.6L19.5 9l-5.6 1.9L12 16.5l-1.9-5.6L4.5 9l5.6-1.4L12 2z"/><path d="M18.5 15l.9 2.6 2.6.9-2.6.9-.9 2.6-.9-2.6-2.6-.9 2.6-.9.9-2.6z"/>',
		'bulb'       => '<path d="M9 18h6"/><path d="M10 21h4"/><path d="M12 3a6 6 0 0 0-3.6 10.8c.5.4.8 1 .9 1.6l.1.6h5.2l.1-.6c.1-.6.4-1.2.9-1.6A6 6 0 0 0 12 3z"/>',
		'bolt'       => '<path d="M13 2L4.5 13.5H11l-1 8.5 8.5-11.5H12l1-8.5z"/>',
		'shield'     => '<path d="M12 3l7 3v5.5c0 4.3-2.9 8.2-7 9.5-4.1-1.3-7-5.2-7-9.5V6l7-3z"/><path d="M9 12l2 2 4-4"/>',
		'drop'       => '<path d="M12 3.5S6.5 9.7 6.5 14a5.5 5.5 0 0 0 11 0c0-4.3-5.5-10.5-5.5-10.5z"/>',
		'chip'       => '<rect x="7" y="7" width="10" height="10" rx="2"/><path d="M4 10h3M4 14h3M17 10h3M17 14h3M10 4v3M14 4v3M10 17v3M14 17v3"/>',
		'certified'  => '<circle cx="12" cy="10" r="6"/><path d="M9 15.5L8 22l4-2 4 2-1-6.5"/>',
		'city'       => '<path d="M3 21h18"/><path d="M5 21V9l5-3v15"/><path d="M14 21V11l5 3v7"/><path d="M8 11h1M8 14h1M8 17h1M16 15h1M16 18h1"/>',
		'tree'       => '<path d="M12 3l4.5 6h-9L12 3z"/><path d="M12 9l5.5 7h-11L12 9z"/><path d="M12 16v5"/>',
		'tunnel'     => '<path d="M4 20V11a8 8 0 0 1 16 0v9"/><path d="M8 20v-9a4 4 0 0 1 8 0v9"/><path d="M3 20h18"/>',
		'phone'      => '<path d="M5 3.5h3l1.5 4-2 1.5a12 12 0 0 0 6 6l1.5-2 4 1.5v3c0 1-.8 1.8-1.8 1.7A16.5 16.5 0 0 1 3.3 5.3C3.2 4.3 4 3.5 5 3.5z"/>',
		'mail'       => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3.5 6.5l8.5 6 8.5-6"/>',
		'clock'      => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3.5 2"/>',
		'pin'        => '<path d="M12 21s6.5-6.2 6.5-11a6.5 6.5 0 0 0-13 0C5.5 14.8 12 21 12 21z"/><circle cx="12" cy="10" r="2.4"/>',
		'cart'       => '<path d="M3 4h2.5l2.2 10.5h9.6L19 7H6"/><circle cx="9" cy="19" r="1.6"/><circle cx="17" cy="19" r="1.6"/>',
		'tools'      => '<path d="M14.5 3.5l3 3-2 2-3-3 2-2z"/><path d="M12.5 8.5L4 17v3h3l8.5-8.5"/><path d="M17 14l4 4-3 3-4-4"/>',
		'truck'      => '<path d="M3 6h10v9H3z"/><path d="M13 9h4l3 3v3h-7z"/><circle cx="7" cy="17.5" r="1.6"/><circle cx="17" cy="17.5" r="1.6"/>',
		'star'       => '<path d="M12 3.5l2.7 5.6 6 .8-4.4 4.2 1.1 6-5.4-3-5.4 3 1.1-6L3.3 9.9l6-.8L12 3.5z"/>',
		'menu'       => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'      => '<path d="M6 6l12 12M18 6L6 18"/>',
		'search'     => '<circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/>',
		'arrow'      => '<path d="M14 5l-7 7 7 7"/>',
		'arrow-up'   => '<path d="M12 19V5"/><path d="M6 11l6-6 6 6"/>',
		'check'      => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
		'chevron'    => '<path d="M9 6l6 6-6 6"/>',
		'filter'     => '<path d="M4 6h16l-6 7v6l-4-2v-4L4 6z"/>',
		'play'       => '<circle cx="12" cy="12" r="8.5"/><path d="M10.5 8.5l5 3.5-5 3.5z"/>',
		'download'   => '<path d="M12 4v10"/><path d="M8 11l4 4 4-4"/><path d="M5 19h14"/>',
		'calendar'   => '<rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M3.5 10h17M8 3.5V6M16 3.5V6"/>',
		'user'       => '<circle cx="12" cy="9" r="3.5"/><path d="M5 20c1.2-3.4 3.7-5 7-5s5.8 1.6 7 5"/>',
		'tag'        => '<path d="M12.5 3.5H20v7.5l-8.5 8.5L4 12l8.5-8.5z"/><circle cx="16.8" cy="7.2" r="1.3"/>',
		'folder'     => '<path d="M4 7a2 2 0 0 1 2-2h3.2l2 2H18a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7z"/>',
		'zoom'       => '<circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5M8.5 11h5M11 8.5v5"/>',
		'plus'       => '<path d="M12 5v14M5 12h14"/>',
		'minus'      => '<path d="M5 12h14"/>',
		'quote'      => '<path d="M9 6.5C6.5 8 5.5 10 5.5 12.5c0 2.4 1.4 4 3.4 4s3.1-1.4 3.1-3.2c0-1.7-1.2-3-2.9-3-.3 0-.6 0-.8.1.3-1.2 1.6-2.4 3.2-3.2L9 6.5z"/><path d="M17 6.5c-2.5 1.5-3.5 3.5-3.5 6 0 2.4 1.4 4 3.4 4s3.1-1.4 3.1-3.2c0-1.7-1.2-3-2.9-3-.3 0-.6 0-.8.1.3-1.2 1.6-2.4 3.2-3.2L17 6.5z"/>',
		'grid'       => '<rect x="4" y="4" width="7" height="7" rx="1.5"/><rect x="13" y="4" width="7" height="7" rx="1.5"/><rect x="4" y="13" width="7" height="7" rx="1.5"/><rect x="13" y="13" width="7" height="7" rx="1.5"/>',
		'aparat'     => '<circle cx="7" cy="7.5" r="2"/><circle cx="17" cy="7.5" r="2"/><circle cx="7" cy="16.5" r="2"/><circle cx="17" cy="16.5" r="2"/>',
		'instagram'  => '<rect x="4" y="4" width="16" height="16" rx="4.5"/><circle cx="12" cy="12" r="3.6"/><circle cx="17" cy="7" r="1.1" fill="currentColor" stroke="none"/>',
		'telegram'   => '<path d="M20.5 4.5L3.8 10.9c-.9.3-.9 1.5 0 1.8l3.9 1.3 1.5 4.6c.2.7 1.1.9 1.6.3l2.2-2.4 3.9 2.9c.6.4 1.4.1 1.6-.6l2.6-13.5c.1-.8-.6-1.4-1.3-1.1z"/>',
		'whatsapp'   => '<path d="M12 3.5a8.5 8.5 0 0 0-7.2 13l-1 3.9 4-1a8.5 8.5 0 1 0 4.2-15.9z"/><path d="M9 9.2c0 3 2.5 5.5 5.5 5.5.5 0 1-.5 1-1l-1.4-.8-1 .9c-1-.4-1.9-1.3-2.3-2.3l.9-1-.8-1.4c-.5 0-1 .5-1 1z"/>',
		'youtube'    => '<rect x="3" y="6" width="18" height="12" rx="3.5"/><path d="M11 9.5l4 2.5-4 2.5z"/>',
		'linkedin'   => '<rect x="4" y="4" width="16" height="16" rx="3"/><path d="M8 10.5V16M8 7.8v.2M12 16v-3.2c0-1.3.8-2.1 1.9-2.1 1 0 1.6.7 1.6 2V16"/>',
		'twitter'    => '<path d="M5 5l6 8-6 6h2.5l4.7-4.8 3.6 4.8H19l-6.2-8.2L18.4 5H16l-4.4 4.5L8.4 5H5z"/>',
		'facebook'   => '<path d="M14.5 8.5h2.2V5.6h-2.4c-2.3 0-3.6 1.4-3.6 3.7v1.6H8.4v3h2.3V21h3.2v-7.1h2.3l.4-3h-2.7V9.7c0-.8.3-1.2 1.1-1.2z"/>',
		'eitaa'      => '<circle cx="12" cy="12" r="8.5"/><path d="M8.5 13.2l3.4 3.3 3.6-6.5"/>',
		'rss'        => '<path d="M6 6.5c6.3 0 11.5 5.2 11.5 11.5"/><path d="M6 12.5c3 0 5.5 2.5 5.5 5.5"/><circle cx="7.2" cy="17.8" r="1.3" fill="currentColor" stroke="none"/>',
	);

	$inner = isset( $icons[ $name ] ) ? $icons[ $name ] : $icons['spark'];

	return es_kses_svg( $inner );
}

/**
 * Render an inline SVG icon.
 *
 * @param string $name  Icon key.
 * @param string $class Extra CSS classes.
 * @param int    $size  Pixel size.
 * @return string
 */
function es_get_icon( $name, $class = 'es-icon', $size = 24 ) {
	$size = max( 12, min( 96, (int) $size ) );

	return sprintf(
		'<svg class="%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" role="img">%3$s</svg>',
		esc_attr( $class ),
		$size,
		es_icon_library( $name )
	);
}

/**
 * Print an inline SVG icon.
 *
 * @param string $name  Icon key.
 * @param string $class Extra CSS classes.
 * @param int    $size  Pixel size.
 * @return void
 */
function es_icon( $name, $class = 'es-icon', $size = 24 ) {
	echo es_get_icon( $name, $class, $size ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- es_get_icon() escapes every attribute and KSES-filters the markup.
}

/**
 * Social network SVG icon (falls back to the generic icon).
 *
 * @param string $network Network key.
 * @param int    $size    Pixel size.
 * @return string
 */
function es_get_social_icon( $network, $size = 20 ) {
	$available = array( 'aparat', 'instagram', 'telegram', 'whatsapp', 'youtube', 'linkedin', 'twitter', 'facebook', 'eitaa', 'rss' );
	$key       = in_array( $network, $available, true ) ? $network : 'star';

	return es_get_icon( $key, 'es-icon es-icon--social', $size );
}

/**
 * Attachment/image URL from an options image id.
 *
 * @param int    $attachment_id Attachment id.
 * @param string $size          Registered image size.
 * @param string $fallback      Fallback URL.
 * @return string
 */
function es_image_url( $attachment_id, $size = 'es-card-wide', $fallback = '' ) {
	$attachment_id = absint( $attachment_id );

	if ( $attachment_id ) {
		$url = wp_get_attachment_image_url( $attachment_id, $size );
		if ( $url ) {
			return $url;
		}
	}

	return $fallback;
}

/**
 * CSS background-image declaration for section backgrounds.
 *
 * @param int    $attachment_id Attachment id.
 * @param string $size          Image size.
 * @return string Ready-to-print style attribute value (escaped by caller).
 */
function es_bg_style( $attachment_id, $size = 'es-hero' ) {
	$url = es_image_url( $attachment_id, $size );

	return $url ? 'background-image:url(' . esc_url_raw( $url ) . ')' : '';
}

/**
 * Persian (or Latin) numerals based on the typography option.
 *
 * @param string|int $value Raw number/string.
 * @return string
 */
function es_num( $value ) {
	$value = (string) $value;

	if ( 'fa' !== es_opt( 'numeral_style', 'fa' ) ) {
		return $value;
	}

	return strtr(
		$value,
		array(
			'0' => '۰',
			'1' => '۱',
			'2' => '۲',
			'3' => '۳',
			'4' => '۴',
			'5' => '۵',
			'6' => '۶',
			'7' => '۷',
			'8' => '۸',
			'9' => '۹',
		)
	);
}

/**
 * Zero padded Persian index used by numbered cards (۰۱, ۰۲ …).
 *
 * @param int $index 1-based position.
 * @return string
 */
function es_index_label( $index ) {
	return es_num( str_pad( (string) absint( $index ), 2, '0', STR_PAD_LEFT ) );
}

/**
 * Excerpt trimmed to a word count with a Persian-aware ellipsis.
 *
 * @param int    $words Word count.
 * @param string $more  Trailing marker.
 * @param int    $post_id Optional post id.
 * @return string
 */
function es_excerpt( $words = 24, $more = '…', $post_id = 0 ) {
	$words = max( 4, (int) $words );

	if ( has_excerpt( $post_id ) ) {
		$text = wp_strip_all_tags( get_the_excerpt( $post_id ) );
	} else {
		$text = wp_strip_all_tags( get_the_content( '', false, $post_id ) );
		$text = preg_replace( '/\[[^\]]+\]/', '', $text );
	}

	$text  = trim( preg_replace( '/\s+/u', ' ', (string) $text ) );
	$parts = preg_split( '/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY );

	if ( ! is_array( $parts ) ) {
		return '';
	}

	if ( count( $parts ) <= $words ) {
		return $text;
	}

	return implode( ' ', array_slice( $parts, 0, $words ) ) . $more;
}

/**
 * Estimated reading time in minutes (Persian text reads ~200 wpm).
 *
 * @param int $post_id Post id.
 * @return int
 */
function es_reading_time( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();

	$override = (int) get_post_meta( $post_id, '_es_reading_time_min', true );
	if ( $override > 0 ) {
		return $override;
	}

	$content = wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) );
	$words   = max( 1, count( preg_split( '/\s+/u', trim( $content ), -1, PREG_SPLIT_NO_EMPTY ) ) );

	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Convert a phone number into a tel: href.
 *
 * @param string $phone Raw phone.
 * @return string
 */
function es_phone_href( $phone ) {
	$digits = preg_replace( '/[^0-9+]/', '', (string) $phone );
	$digits = ltrim( (string) $digits, '+' );

	if ( '' === $digits ) {
		return '';
	}

	if ( 0 === strpos( $digits, '00' ) ) {
		$digits = substr( $digits, 2 );
	} elseif ( 0 === strpos( $digits, '0' ) ) {
		$digits = '98' . substr( $digits, 1 );
	}

	return 'tel:+' . $digits;
}

/**
 * WhatsApp deep link from a mobile number.
 *
 * @param string $phone Raw phone.
 * @return string
 */
function es_whatsapp_href( $phone ) {
	$digits = preg_replace( '/[^0-9]/', '', (string) $phone );

	if ( '' === $digits ) {
		return '';
	}

	if ( 0 === strpos( $digits, '0' ) ) {
		$digits = '98' . substr( $digits, 1 );
	}

	return 'https://api.whatsapp.com/send?phone=' . $digits;
}

/**
 * Default social list merged with the configured rows.
 *
 * @return array<int,array>
 */
function es_get_social_items() {
	$items = es_opt( 'social_items', array() );

	if ( ! is_array( $items ) ) {
		return array();
	}

	return array_values(
		array_filter(
			$items,
			static function ( $item ) {
				return is_array( $item ) && ! empty( $item['url'] );
			}
		)
	);
}

/**
 * Posts page (blog archive) URL, honouring the configured blog slug.
 *
 * @return string
 */
function es_blog_url() {
	$page_id = (int) get_option( 'page_for_posts' );

	if ( $page_id ) {
		return get_permalink( $page_id );
	}

	$slug = es_clean_text( es_opt( 'blog_slug', 'blog' ) );

	return $slug ? home_url( '/' . sanitize_title( $slug ) . '/' ) : home_url( '/' );
}

/**
 * Shop / products archive URL (WooCommerce aware, with /products/ alias).
 *
 * @return string
 */
function es_shop_url() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$shop = wc_get_page_permalink( 'shop' );
		if ( $shop ) {
			return $shop;
		}
	}

	return home_url( '/products/' );
}

/**
 * Product categories archive URL.
 *
 * @return string
 */
function es_product_categories_url() {
	return home_url( '/product-category/' );
}

/**
 * Projects archive URL.
 *
 * @return string
 */
function es_projects_url() {
	$post_type = get_post_type_object( 'project' );

	if ( $post_type && ! empty( $post_type->has_archive ) ) {
		$url = get_post_type_archive_link( 'project' );
		if ( $url ) {
			return $url;
		}
	}

	return home_url( '/projects/' );
}

/**
 * Render an accessible breadcrumb trail with schema.org markup.
 *
 * @return void
 */
function es_breadcrumbs() {
	if ( ! es_opt( 'enable_breadcrumbs', true ) || is_front_page() ) {
		return;
	}

	$items = es_get_breadcrumb_items();

	if ( count( $items ) < 2 ) {
		return;
	}

	$last_index = count( $items ) - 1;

	echo '<nav class="es-breadcrumbs" aria-label="' . esc_attr__( 'مسیر راهنما', 'erfan-sanat' ) . '"><ol class="es-breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">';

	foreach ( $items as $index => $item ) {
		printf(
			'<li class="es-breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><meta itemprop="position" content="%1$d">',
			(int) ( $index + 1 )
		);

		if ( $index < $last_index && ! empty( $item['url'] ) ) {
			printf(
				'<a class="es-breadcrumbs__link" href="%1$s" itemprop="item"><span itemprop="name">%2$s</span></a>',
				esc_url( $item['url'] ),
				esc_html( $item['label'] )
			);
		} else {
			printf( '<span class="es-breadcrumbs__current" itemprop="name" aria-current="page">%s</span>', esc_html( $item['label'] ) );
		}

		if ( $index < $last_index ) {
			echo '<span class="es-breadcrumbs__sep" aria-hidden="true">' . esc_html( '›' ) . '</span>';
		}

		echo '</li>';
	}

	echo '</ol></nav>';
}

/**
 * Breadcrumb data for the current request.
 *
 * @return array<int,array{label:string,url:string}>
 */
function es_get_breadcrumb_items() {
	$items = array(
		array(
			'label' => __( 'صفحهٔ اصلی', 'erfan-sanat' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( is_singular() ) {
		$post_type = get_post_type();

		if ( 'post' === $post_type ) {
			$items[] = array(
				'label' => __( 'مقالات', 'erfan-sanat' ),
				'url'   => es_blog_url(),
			);

			$categories = get_the_category();
			if ( ! empty( $categories ) ) {
				$primary = $categories[0];
				$items[] = array(
					'label' => $primary->name,
					/* translators: %s: category id. */
					'url'   => get_category_link( $primary->term_id ),
				);
			}
		} elseif ( 'product' === $post_type ) {
			$items[] = array(
				'label' => __( 'فروشگاه', 'erfan-sanat' ),
				'url'   => es_shop_url(),
			);

			$terms = get_the_terms( get_the_ID(), 'product_cat' );
			if ( is_array( $terms ) && ! empty( $terms ) ) {
				$items[] = array(
					'label' => $terms[0]->name,
					'url'   => get_term_link( $terms[0] ),
				);
			}
		} elseif ( 'project' === $post_type ) {
			$items[] = array(
				'label' => __( 'پروژه‌ها', 'erfan-sanat' ),
				'url'   => es_projects_url(),
			);

			$terms = get_the_terms( get_the_ID(), 'project_cat' );
			if ( is_array( $terms ) && ! empty( $terms ) ) {
				$items[] = array(
					'label' => $terms[0]->name,
					'url'   => get_term_link( $terms[0] ),
				);
			}
		} else {
			$parent = wp_get_post_parent_id( get_the_ID() );
			if ( $parent ) {
				$items[] = array(
					'label' => get_the_title( $parent ),
					'url'   => get_permalink( $parent ),
				);
			}
		}

		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $term && ! is_wp_error( $term ) ) {
			if ( 'product_cat' === $term->taxonomy ) {
				$items[] = array(
					'label' => __( 'فروشگاه', 'erfan-sanat' ),
					'url'   => es_shop_url(),
				);
			} elseif ( 'project_cat' === $term->taxonomy || 'project_location' === $term->taxonomy ) {
				$items[] = array(
					'label' => __( 'پروژه‌ها', 'erfan-sanat' ),
					'url'   => es_projects_url(),
				);
			}

			$items[] = array(
				'label' => $term->name,
				'url'   => '',
			);
		}
	} elseif ( is_search() ) {
		$items[] = array(
			'label' => sprintf(
				/* translators: %s: search query. */
				__( 'نتیجهٔ جست‌وجو برای «%s»', 'erfan-sanat' ),
				get_search_query()
			),
			'url'   => '',
		);
	} elseif ( is_post_type_archive( 'project' ) ) {
		$items[] = array(
			'label' => __( 'پروژه‌ها', 'erfan-sanat' ),
			'url'   => '',
		);
	} elseif ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {
		$items[] = array(
			'label' => __( 'فروشگاه', 'erfan-sanat' ),
			'url'   => '',
		);
	} elseif ( is_404() ) {
		$items[] = array(
			'label' => __( 'صفحه یافت نشد', 'erfan-sanat' ),
			'url'   => '',
		);
	} elseif ( is_home() ) {
		$items[] = array(
			'label' => __( 'مقالات', 'erfan-sanat' ),
			'url'   => '',
		);
	} elseif ( is_archive() ) {
		$items[] = array(
			'label' => get_the_archive_title(),
			'url'   => '',
		);
	} elseif ( is_page() ) {
		$items[] = array(
			'label' => get_the_title(),
			'url'   => '',
		);
	}

	return $items;
}

/**
 * Accessible numbered pagination.
 *
 * @param WP_Query|null $query Optional custom query.
 * @return void
 */
function es_pagination( $query = null ) {
	$query = $query instanceof WP_Query ? $query : $GLOBALS['wp_query'];

	$total   = (int) $query->max_num_pages;
	$current = max( 1, (int) get_query_var( 'paged' ) );

	if ( $total < 2 ) {
		return;
	}

	if ( $query !== $GLOBALS['wp_query'] ) {
		$current = max( 1, (int) $query->get( 'paged' ) );
	}

	$links = paginate_links(
		array(
			'total'     => $total,
			'current'   => $current,
			'type'      => 'array',
			'mid_size'  => 1,
			'end_size'  => 1,
			'prev_text' => '<span aria-hidden="true">›</span><span class="screen-reader-text">' . esc_html__( 'صفحهٔ قبلی', 'erfan-sanat' ) . '</span>',
			'next_text' => '<span aria-hidden="true">‹</span><span class="screen-reader-text">' . esc_html__( 'صفحهٔ بعدی', 'erfan-sanat' ) . '</span>',
		)
	);

	if ( empty( $links ) ) {
		return;
	}

	echo '<nav class="es-pagination" aria-label="' . esc_attr__( 'صفحه‌بندی', 'erfan-sanat' ) . '"><ul class="es-pagination__list">';

	foreach ( $links as $link ) {
		echo '<li class="es-pagination__item">' . wp_kses_post( $link ) . '</li>';
	}

	echo '</ul></nav>';
}

/**
 * Related posts from the same category (posts, projects, products).
 *
 * @param int    $post_id Post id.
 * @param string $type    Post type to query.
 * @param string $taxonomy Taxonomy used to find the relation.
 * @param int    $count   How many items to return.
 * @return WP_Query
 */
function es_related_query( $post_id, $type = 'post', $taxonomy = 'category', $count = 3 ) {
	$count = max( 1, (int) $count );
	$args  = array(
		'post_type'           => $type,
		'posts_per_page'      => $count,
		'post__not_in'        => array( absint( $post_id ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'orderby'             => 'date',
	);

	$terms    = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
	$tax_args = array();

	if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
		$tax_args = array(
			'tax_query' => array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $terms,
				),
			),
		);
	}

	$query = new WP_Query( array_merge( $args, $tax_args ) );

	if ( ! $query->have_posts() && $tax_args ) {
		$query = new WP_Query( $args );
	}

	return $query;
}

/**
 * Render a responsive featured image (with a graceful placeholder).
 *
 * @param int    $post_id Post id.
 * @param string $size    Image size.
 * @param string $class   Extra classes.
 * @return void
 */
function es_post_thumbnail( $post_id = 0, $size = 'es-card', $class = 'es-card__image' ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();

	if ( has_post_thumbnail( $post_id ) ) {
		echo wp_get_attachment_image(
			get_post_thumbnail_id( $post_id ),
			$size,
			false,
			array(
				'class'    => $class,
				'loading'  => 'lazy',
				'decoding' => 'async',
			)
		);

		return;
	}

	printf(
		'<div class="%1$s es-card__image--placeholder" role="img" aria-label="%2$s">%3$s</div>',
		esc_attr( $class ),
		esc_attr__( 'تصویری برای این مطلب ثبت نشده است', 'erfan-sanat' ),
		es_get_icon( 'spark', 'es-placeholder-icon', 40 ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
}

/**
 * Section classes helper (keeps templates free of string concatenation).
 *
 * @param string $base  Base class.
 * @param array  $extra Extra classes.
 * @return string
 */
function es_classes( $base, array $extra = array() ) {
	$classes = array_filter( array_merge( array( $base ), $extra ) );

	return implode( ' ', array_map( 'sanitize_html_class', $classes ) );
}

/**
 * Category/term list as pill markup.
 *
 * @param int    $post_id  Post id.
 * @param string $taxonomy Taxonomy.
 * @param int    $limit    Max terms.
 * @return void
 */
function es_term_pills( $post_id, $taxonomy = 'category', $limit = 2 ) {
	$terms = get_the_terms( $post_id, $taxonomy );

	if ( ! is_array( $terms ) || empty( $terms ) ) {
		return;
	}

	echo '<div class="es-pills">';

	foreach ( array_slice( $terms, 0, max( 1, (int) $limit ) ) as $term ) {
		printf(
			'<a class="es-pill" href="%1$s">%2$s</a>',
			esc_url( (string) get_term_link( $term ) ),
			esc_html( $term->name )
		);
	}

	echo '</div>';
}

/**
 * Formatted price/label for a project or product card.
 *
 * @param int $post_id Post id.
 * @return string
 */
function es_price_badge( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();

	if ( 'product' === get_post_type( $post_id ) && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( $post_id );
		if ( $product ) {
			$badge = (string) get_post_meta( $post_id, '_es_custom_price_badge', true );
			if ( '' !== $badge ) {
				return $badge;
			}

			return $product->get_price_html();
		}
	}

	return (string) get_post_meta( $post_id, '_es_custom_price_badge', true );
}

/**
 * Plain-text meta description used by the SEO module.
 *
 * @return string
 */
function es_meta_description() {
	if ( is_singular() ) {
		$excerpt = es_excerpt( 30, '', get_the_ID() );
		if ( $excerpt ) {
			return $excerpt;
		}
	}

	if ( is_tax() || is_category() || is_tag() ) {
		$term = get_queried_object();
		if ( $term && ! is_wp_error( $term ) && ! empty( $term->description ) ) {
			return wp_strip_all_tags( $term->description );
		}
	}

	if ( is_archive() ) {
		if ( is_post_type_archive( 'project' ) ) {
			return es_clean_text( es_opt( 'project_archive_text', '' ) );
		}

		$title = wp_strip_all_tags( get_the_archive_title() );
		if ( $title ) {
			return $title;
		}
	}

	return (string) get_bloginfo( 'description', 'display' );
}

/**
 * Text direction of the site.
 *
 * The theme is built RTL-first for Persian content: it forces RTL output even
 * when the WordPress locale happens to be an LTR one, so the layout never
 * breaks for Persian-only installations. Filterable for multilingual setups.
 *
 * @return string 'rtl' or 'ltr'.
 */
function es_theme_direction() {
	$direction = is_rtl() ? 'rtl' : 'ltr';

	/**
	 * Filters the theme text direction.
	 *
	 * @param string $direction 'rtl' or 'ltr'.
	 */
	$direction = (string) apply_filters( 'es_theme_direction', $direction );

	return 'ltr' === $direction ? 'ltr' : 'rtl';
}

/**
 * Whether the given option key is enabled (shortcut for templates).
 *
 * @param string $key Option key.
 * @return bool
 */
function es_enabled( $key ) {
	return (bool) es_opt( $key, false );
}

/**
 * Fallback navigation when no menu is assigned to a location.
 *
 * Keeps the site navigable right after activation instead of rendering an
 * empty nav element.
 *
 * @param array $args wp_nav_menu() arguments.
 * @return void
 */
function es_nav_menu_fallback( $args = array() ) {
	$menu_class = isset( $args['menu_class'] ) ? $args['menu_class'] : 'es-nav__list';

	$items = array(
		array(
			'label' => __( 'صفحهٔ اصلی', 'erfan-sanat' ),
			'url'   => home_url( '/' ),
		),
		array(
			'label' => __( 'پروژه‌ها', 'erfan-sanat' ),
			'url'   => es_projects_url(),
		),
	);

	if ( es_woocommerce_active() ) {
		$items[] = array(
			'label' => __( 'فروشگاه', 'erfan-sanat' ),
			'url'   => es_shop_url(),
		);
	}

	$items[] = array(
		'label' => __( 'مقالات', 'erfan-sanat' ),
		'url'   => es_blog_url(),
	);

	$items[] = array(
		'label' => __( 'تماس با ما', 'erfan-sanat' ),
		'url'   => home_url( '/contact/' ),
	);

	echo '<ul class="' . esc_attr( $menu_class ) . '">';

	foreach ( $items as $item ) {
		printf(
			'<li class="menu-item"><a class="es-nav__link" href="%1$s">%2$s</a></li>',
			esc_url( $item['url'] ),
			esc_html( $item['label'] )
		);
	}

	echo '</ul>';
}

/**
 * Standard section header (eyebrow + title + description).
 *
 * @param array $args eyebrow, title, text, align, tag, id.
 * @return void
 */
function es_section_header( array $args = array() ) {
	$eyebrow = isset( $args['eyebrow'] ) ? (string) $args['eyebrow'] : '';
	$title   = isset( $args['title'] ) ? (string) $args['title'] : '';
	$text    = isset( $args['text'] ) ? (string) $args['text'] : '';
	$align   = isset( $args['align'] ) ? sanitize_html_class( $args['align'] ) : 'center';
	$tag     = isset( $args['tag'] ) && in_array( $args['tag'], array( 'h1', 'h2', 'h3' ), true ) ? $args['tag'] : 'h2';
	$id      = isset( $args['id'] ) ? sanitize_html_class( $args['id'] ) : '';

	if ( ! $eyebrow && ! $title && ! $text ) {
		return;
	}

	printf( '<header class="es-section__header es-section__header--%s">', esc_attr( $align ) );

	if ( $eyebrow ) {
		printf(
			'<p class="es-eyebrow"><span class="es-eyebrow__dot" aria-hidden="true"></span>%s</p>',
			esc_html( $eyebrow )
		);
	}

	if ( $title ) {
		printf(
			'<%1$s class="es-section__title"%2$s>%3$s</%1$s>',
			esc_attr( $tag ),
			$id ? ' id="' . esc_attr( $id ) . '"' : '',
			esc_html( $title )
		);
	}

	if ( $text ) {
		printf( '<p class="es-section__text">%s</p>', esc_html( $text ) );
	}

	echo '</header>';
}

/**
 * Empty state block used by archives, search and filters.
 *
 * @param array $args title, text, icon, button_text, button_url.
 * @return void
 */
function es_empty_state( array $args = array() ) {
	$title = isset( $args['title'] ) ? (string) $args['title'] : __( 'موردی یافت نشد.', 'erfan-sanat' );
	$text  = isset( $args['text'] ) ? (string) $args['text'] : '';
	$icon  = isset( $args['icon'] ) ? (string) $args['icon'] : 'spark';
	$btn   = isset( $args['button_text'] ) ? (string) $args['button_text'] : '';
	$url   = isset( $args['button_url'] ) ? (string) $args['button_url'] : '';

	echo '<div class="es-empty">';
	printf( '<span class="es-empty__icon">%s</span>', es_get_icon( $icon, 'es-icon', 36 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	printf( '<h2 class="es-empty__title">%s</h2>', esc_html( $title ) );

	if ( $text ) {
		printf( '<p class="es-empty__text">%s</p>', esc_html( $text ) );
	}

	if ( $btn && $url ) {
		printf(
			'<a class="es-btn es-btn--primary" href="%1$s">%2$s</a>',
			esc_url( $url ),
			esc_html( $btn )
		);
	}

	echo '</div>';
}

/**
 * Render a star rating (accessibility friendly, no icon font).
 *
 * @param float $rating Rating value 0-5.
 * @return string
 */
function es_stars( $rating ) {
	$rating = max( 0, min( 5, (float) $rating ) );
	$full   = (int) floor( $rating );
	$half   = ( $rating - $full ) >= 0.5;
	$html   = '<span class="es-stars" role="img" aria-label="' . esc_attr( sprintf( /* translators: %s: rating */ __( 'امتیاز %s از ۵', 'erfan-sanat' ), number_format_i18n( $rating, 1 ) ) ) . '">';

	for ( $i = 1; $i <= 5; $i++ ) {
		$state = 'off';
		if ( $i <= $full ) {
			$state = 'on';
		} elseif ( $half && $i === $full + 1 ) {
			$state = 'half';
		}
		$html .= '<span class="es-star es-star--' . esc_attr( $state ) . '" aria-hidden="true">★</span>';
	}

	$html .= '</span>';

	return $html;
}

/**
 * Add stable ids to the headings inside the content.
 *
 * Ids make the table of contents, deep links and screen-reader navigation
 * work, and they are reused by es_collect_headings().
 *
 * @param string $content Post content.
 * @return string
 */
function es_add_heading_ids( $content ) {
	if ( ! is_singular( array( 'post', 'page' ) ) || false === stripos( (string) $content, '<h2' ) ) {
		return $content;
	}

	$used = array();

	$content = preg_replace_callback(
		'#<h([23])(\s[^>]*)?>(.*?)</h\1>#is',
		function ( $matches ) use ( &$used ) {
			$attrs = isset( $matches[2] ) ? (string) $matches[2] : '';

			// Never touch a heading that already carries an id.
			if ( preg_match( '/\sid=(["'])/i', $attrs ) ) {
				return $matches[0];
			}

			$text = wp_strip_all_tags( $matches[3] );
			$slug = sanitize_title( $text );

			if ( ! $slug ) {
				$slug = 'section';
			}

			$id   = $slug;
			$i    = 2;
			while ( in_array( $id, $used, true ) ) {
				$id = $slug . '-' . $i;
				$i++;
			}
			$used[] = $id;

			return '<h' . $matches[1] . $attrs . ' id="' . esc_attr( $id ) . '">' . $matches[3] . '</h' . $matches[1] . '>';
		},
		(string) $content
	);

	return $content;
}
add_filter( 'the_content', 'es_add_heading_ids', 8 );

/**
 * Collect the h2/h3 headings of a piece of content for the table of contents.
 *
 * The ids follow the same scheme as es_add_heading_ids().
 *
 * @param string $content Raw content.
 * @return array<int,array{level:string,id:string,text:string}>
 */
function es_collect_headings( $content ) {
	$headings = array();

	if ( ! preg_match_all( '#<h([23])(\s[^>]*)?>(.*?)</h\1>#is', (string) $content, $matches, PREG_SET_ORDER ) ) {
		return $headings;
	}

	$used = array();

	foreach ( $matches as $match ) {
		$attrs = isset( $match[2] ) ? (string) $match[2] : '';
		$text  = trim( wp_strip_all_tags( $match[3] ) );

		if ( '' === $text ) {
			continue;
		}

		if ( preg_match( '/\sid=(["'])(.+?)\1/i', $attrs, $id_match ) ) {
			$id = $id_match[2];
		} else {
			$id = sanitize_title( $text );

			if ( ! $id ) {
				$id = 'section';
			}
		}

		while ( in_array( $id, $used, true ) ) {
			$id .= '-2';
		}

		$used[]     = $id;
		$headings[] = array(
			'level' => '2' === $match[1] ? 'h2' : 'h3',
			'id'    => $id,
			'text'  => $text,
		);
	}

	return $headings;
}
