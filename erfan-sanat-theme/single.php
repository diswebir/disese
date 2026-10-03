<?php
/**
 * Single content template. Branches by post type so projects get their own
 * specification layout while articles keep the editorial layout.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$es_type = get_post_type();

	if ( 'project' === $es_type ) {
		get_template_part( 'template-parts/content/single-project' );
	} elseif ( 'product' === $es_type && es_woocommerce_active() ) {
		// Products are rendered by WooCommerce's own single template.
		wc_get_template_part( 'content', 'single-product' );
	} else {
		get_template_part( 'template-parts/content/single-post' );
	}

	// Comments (posts only, and only when they are open).
	if ( 'post' === $es_type && ( comments_open() || get_comments_number() ) ) {
		comments_template();
	}

	// Prev/next navigation inside the same post type.
	get_template_part( 'template-parts/content/single-nav' );
endwhile;

get_footer();
