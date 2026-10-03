<?php
/**
 * SEO output: meta description, Open Graph, Twitter cards, JSON-LD.
 *
 * The theme deliberately does not try to replace an SEO plugin: when a known
 * SEO plugin is active every theme-level output is skipped so no duplicate
 * titles/descriptions/schemas are emitted.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is a dedicated SEO plugin handling meta output?
 *
 * @return bool
 */
function es_seo_plugin_active() {
	$plugins = array(
		'WPSEO_VERSION',          // Yoast SEO.
		'RANK_MATH_VERSION',      // Rank Math.
		'AIOSEO_VERSION',         // All in One SEO.
		'SEOPRESS_VERSION',       // SEOPress.
		'THE_SEO_FRAMEWORK_VERSION',
	);

	foreach ( $plugins as $constant ) {
		if ( defined( $constant ) ) {
			return true;
		}
	}

	return (bool) apply_filters( 'es_seo_plugin_active', false );
}

/**
 * Title separator used by wp_get_document_title().
 *
 * @param string $separator Default separator.
 * @return string
 */
function es_document_title_separator( $separator ) {
	if ( es_seo_plugin_active() ) {
		return $separator;
	}

	return (string) es_opt( 'seo_title_separator', '|' );
}
add_filter( 'document_title_separator', 'es_document_title_separator' );

/**
 * Canonical URL (only when no SEO plugin outputs one).
 *
 * @return void
 */
function es_output_canonical() {
	if ( es_seo_plugin_active() || ! es_opt( 'seo_enable_meta', true ) ) {
		return;
	}

	$url = '';

	if ( is_singular() ) {
		$url = get_permalink();
	} elseif ( is_home() ) {
		$url = es_blog_url();
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$term = get_queried_object();
		$url  = $term && ! is_wp_error( $term ) ? get_term_link( $term ) : '';
	} elseif ( is_post_type_archive() ) {
		$url = (string) get_post_type_archive_link( get_query_var( 'post_type' ) );
	} elseif ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {
		$url = es_shop_url();
	}

	if ( is_paged() && $url ) {
		$url = (string) get_pagenum_link( max( 1, (int) get_query_var( 'paged' ) ) );
	}

	if ( $url && ! is_wp_error( $url ) ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
	}
}
add_action( 'wp_head', 'es_output_canonical', 3 );

/**
 * Meta description + robots directives.
 *
 * @return void
 */
function es_output_meta_tags() {
	if ( es_seo_plugin_active() ) {
		return;
	}

	if ( es_opt( 'seo_enable_meta', true ) ) {
		$description = es_meta_description();

		if ( $description ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( $description ) ) );
		}

		$keywords = (string) es_opt( 'seo_keywords', '' );
		if ( $keywords && ( is_front_page() || is_home() ) ) {
			printf( '<meta name="keywords" content="%s">' . "\n", esc_attr( $keywords ) );
		}
	}

	$robots = array();

	if ( is_search() && es_opt( 'seo_noindex_search', true ) ) {
		$robots[] = 'noindex';
		$robots[] = 'follow';
	}

	if ( is_404() ) {
		$robots[] = 'noindex';
		$robots[] = 'follow';
	}

	if ( $robots ) {
		printf( '<meta name="robots" content="%s">' . "\n", esc_attr( implode( ',', $robots ) ) );
	}

	if ( is_singular() && ! is_front_page() ) {
		printf( '<meta name="robots" content="%s">' . "\n", esc_attr( 'index,follow,max-image-preview:large' ) );
	}
}
add_action( 'wp_head', 'es_output_meta_tags', 4 );

/**
 * Open Graph and Twitter card tags.
 *
 * @return void
 */
function es_output_open_graph() {
	if ( es_seo_plugin_active() || ! es_opt( 'seo_enable_og', true ) ) {
		return;
	}

	$title = wp_get_document_title();
	$type  = 'website';
	$url   = home_url( '/' );
	$image = '';

	if ( is_singular() ) {
		$type  = is_singular( 'post' ) ? 'article' : 'website';
		$url   = (string) get_permalink();
		$image = (string) get_the_post_thumbnail_url( get_the_ID(), 'es-card-wide' );
	} elseif ( is_home() || is_archive() ) {
		$url = es_blog_url();
	}

	if ( ! $image ) {
		$image = es_image_url( (int) es_opt( 'seo_default_og_image', 0 ), 'es-card-wide' );
	}

	if ( ! $image ) {
		$image = es_image_url( (int) es_opt( 'logo_main', 0 ), 'full' );
	}

	$tags = array(
		'og:locale'       => function_exists( 'get_locale' ) ? get_locale() : 'fa_IR',
		'og:type'         => $type,
		'og:site_name'    => get_bloginfo( 'name', 'display' ),
		'og:title'        => $title,
		'og:description'  => wp_strip_all_tags( es_meta_description() ),
		'og:url'          => $url,
	);

	if ( $image ) {
		$tags['og:image'] = $image;
	}

	foreach ( $tags as $property => $content ) {
		if ( '' === (string) $content ) {
			continue;
		}
		printf( '<meta property="%1$s" content="%2$s">' . "\n", esc_attr( $property ), esc_attr( $content ) );
	}

	$twitter_handle = trim( (string) es_opt( 'seo_twitter_handle', '' ) );

	printf( '<meta name="twitter:card" content="%s">' . "\n", esc_attr( $image ? 'summary_large_image' : 'summary' ) );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( es_meta_description() ) ) );

	if ( $twitter_handle ) {
		printf( '<meta name="twitter:site" content="%s">' . "\n", esc_attr( '@' . ltrim( $twitter_handle, '@' ) ) );
	}

	if ( $image ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
	}
}
add_action( 'wp_head', 'es_output_open_graph', 5 );

/**
 * JSON-LD graph: organization/site, article, product handled by WooCommerce,
 * project creative work, breadcrumbs and FAQ blocks.
 *
 * @return void
 */
function es_output_json_ld() {
	if ( es_seo_plugin_active() || ! es_opt( 'seo_enable_schema', true ) ) {
		return;
	}

	$graph = array();

	$graph[] = es_schema_organization();
	$graph[] = es_schema_website();

	if ( is_singular( 'post' ) ) {
		$graph[] = es_schema_article();
	} elseif ( is_singular( 'project' ) ) {
		$graph[] = es_schema_project();
	}

	$breadcrumbs = es_schema_breadcrumbs();
	if ( $breadcrumbs ) {
		$graph[] = $breadcrumbs;
	}

	if ( is_singular( 'post' ) ) {
		$faq = es_schema_faq();
		if ( $faq ) {
			$graph[] = $faq;
		}
	}

	$graph = array_values( array_filter( $graph ) );

	if ( ! $graph ) {
		return;
	}

	echo '<script type="application/ld+json">';
	echo wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);
	echo "</script>\n";
}
add_action( 'wp_head', 'es_output_json_ld', 6 );

/**
 * Organization schema node.
 *
 * @return array
 */
function es_schema_organization() {
	$logo_id  = (int) es_opt( 'seo_org_logo', 0 );
	$logo_id  = $logo_id ? $logo_id : (int) es_opt( 'logo_main', 0 );
	$logo_url = es_image_url( $logo_id, 'full' );

	$node = array(
		'@type'       => sanitize_key( (string) es_opt( 'seo_org_type', 'Organization' ) ),
		'@id'         => home_url( '/#organization' ),
		'name'        => (string) es_opt( 'company_legal_name', get_bloginfo( 'name', 'display' ) ),
		'url'         => home_url( '/' ),
		'description' => wp_strip_all_tags( es_meta_description() ),
	);

	if ( $logo_url ) {
		$node['logo'] = array(
			'@type' => 'ImageObject',
			'url'   => $logo_url,
		);
	}

	$phone = (string) es_opt( 'phone_primary', '' );
	if ( $phone ) {
		$node['telephone'] = $phone;
	}

	$email = (string) es_opt( 'email_primary', '' );
	if ( $email ) {
		$node['email'] = $email;
	}

	$address = (string) es_opt( 'address_line', '' );
	if ( $address ) {
		$node['address'] = array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => wp_strip_all_tags( $address ),
			'addressLocality' => __( 'اصفهان', 'erfan-sanat' ),
			'addressCountry'  => 'IR',
		);

		$postal = (string) es_opt( 'postal_code', '' );
		if ( $postal ) {
			$node['address']['postalCode'] = $postal;
		}
	}

	$socials = array();
	foreach ( es_get_social_items() as $item ) {
		if ( ! empty( $item['url'] ) ) {
			$socials[] = esc_url_raw( $item['url'] );
		}
	}
	if ( $socials ) {
		$node['sameAs'] = $socials;
	}

	return $node;
}

/**
 * WebSite schema node (with SearchAction).
 *
 * @return array
 */
function es_schema_website() {
	return array(
		'@type'           => 'WebSite',
		'@id'             => home_url( '/#website' ),
		'url'             => home_url( '/' ),
		'name'            => get_bloginfo( 'name', 'display' ),
		'description'     => get_bloginfo( 'description', 'display' ),
		'inLanguage'      => 'fa-IR',
		'publisher'       => array( '@id' => home_url( '/#organization' ) ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => home_url( '/?s={search_term_string}' ),
			),
			'query-input' => 'required name=search_term_string',
		),
	);
}

/**
 * Article schema node for blog posts.
 *
 * @return array
 */
function es_schema_article() {
	$post_id   = get_the_ID();
	$image     = get_the_post_thumbnail_url( $post_id, 'es-card-wide' );
	$author_id = (int) get_post_field( 'post_author', $post_id );
	$reviewer  = (string) get_post_meta( $post_id, '_es_technical_reviewer', true );

	$node = array(
		'@type'            => 'TechArticle',
		'@id'              => get_permalink( $post_id ) . '#article',
		'headline'         => wp_strip_all_tags( get_the_title( $post_id ) ),
		'description'      => wp_strip_all_tags( es_meta_description() ),
		'datePublished'    => get_the_date( DATE_W3C, $post_id ),
		'dateModified'     => get_the_modified_date( DATE_W3C, $post_id ),
		'inLanguage'       => 'fa-IR',
		'mainEntityOfPage' => array( '@id' => get_permalink( $post_id ) ),
		'author'           => array(
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', $author_id ),
			'url'   => get_author_posts_url( $author_id ),
		),
		'publisher'        => array( '@id' => home_url( '/#organization' ) ),
	);

	if ( $image ) {
		$node['image'] = $image;
	}

	if ( $reviewer ) {
		$node['reviewedBy'] = array(
			'@type' => 'Person',
			'name'  => $reviewer,
		);
	}

	$node['timeRequired'] = 'PT' . max( 1, es_reading_time( $post_id ) ) . 'M';

	return $node;
}

/**
 * CreativeWork schema node for projects.
 *
 * @return array
 */
function es_schema_project() {
	$post_id = get_the_ID();
	$image   = get_the_post_thumbnail_url( $post_id, 'es-card-wide' );

	$node = array(
		'@type'       => 'CreativeWork',
		'@id'         => get_permalink( $post_id ) . '#project',
		'name'        => wp_strip_all_tags( get_the_title( $post_id ) ),
		'description' => wp_strip_all_tags( es_meta_description() ),
		'creator'     => array( '@id' => home_url( '/#organization' ) ),
		'dateCreated' => get_the_date( DATE_W3C, $post_id ),
		'inLanguage'  => 'fa-IR',
	);

	if ( $image ) {
		$node['image'] = $image;
	}

	$client = (string) get_post_meta( $post_id, '_es_project_client', true );
	if ( $client ) {
		$node['sponsor'] = array(
			'@type' => 'Organization',
			'name'  => $client,
		);
	}

	$locations = get_the_terms( $post_id, 'project_location' );
	if ( is_array( $locations ) && ! empty( $locations ) ) {
		$node['locationCreated'] = array(
			'@type'   => 'Place',
			'name'    => $locations[0]->name,
			'address' => array(
				'@type'           => 'PostalAddress',
				'addressLocality' => $locations[0]->name,
				'addressCountry'  => 'IR',
			),
		);
	}

	return $node;
}

/**
 * Breadcrumb schema node.
 *
 * @return array|null
 */
function es_schema_breadcrumbs() {
	if ( is_front_page() ) {
		return null;
	}

	$items = es_get_breadcrumb_items();

	if ( count( $items ) < 2 ) {
		return null;
	}

	$elements = array();

	foreach ( $items as $index => $item ) {
		$element = array(
			'@type'    => 'ListItem',
			'position' => $index + 1,
			'name'     => $item['label'],
		);

		if ( ! empty( $item['url'] ) ) {
			$element['item'] = $item['url'];
		}

		$elements[] = $element;
	}

	return array(
		'@type'           => 'BreadcrumbList',
		'@id'             => home_url( '/#breadcrumbs' ),
		'itemListElement' => $elements,
	);
}

/**
 * FAQ schema built from the per-post FAQ repeater meta.
 *
 * @return array|null
 */
function es_schema_faq() {
	$faq = get_post_meta( get_the_ID(), '_es_faq_schema_repeater', true );

	if ( ! is_array( $faq ) || empty( $faq ) ) {
		return null;
	}

	$questions = array();

	foreach ( $faq as $row ) {
		if ( empty( $row['question'] ) || empty( $row['answer'] ) ) {
			continue;
		}

		$questions[] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( $row['question'] ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_kses_post( $row['answer'] ),
			),
		);
	}

	if ( ! $questions ) {
		return null;
	}

	return array(
		'@type'      => 'FAQPage',
		'@id'        => get_permalink() . '#faq',
		'mainEntity' => $questions,
	);
}

/**
 * FAQ rows for a given post (shared by template and schema).
 *
 * @param int $post_id Post id.
 * @return array<int,array>
 */
function es_get_faq_rows( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$faq     = get_post_meta( $post_id, '_es_faq_schema_repeater', true );

	if ( ! is_array( $faq ) ) {
		return array();
	}

	return array_values(
		array_filter(
			$faq,
			static function ( $row ) {
				return is_array( $row ) && ! empty( $row['question'] ) && ! empty( $row['answer'] );
			}
		)
	);
}

/**
 * Pingback/oEmbed friendly: keep pagination semantics clean by removing the
 * duplicate "Page N" title parts handled by wp_get_document_title().
 *
 * @param string $title Document title.
 * @return string
 */
function es_filter_archive_titles( $title ) {
	if ( is_archive() && ! is_post_type_archive( 'project' ) ) {
		$title = str_replace( array( 'Category:', 'Tag:', 'Author:' ), '', $title );
	}

	return $title;
}
add_filter( 'get_the_archive_title', 'es_filter_archive_titles' );


