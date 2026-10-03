<?php
/**
 * Generic archive template: blog, project archive, taxonomies and custom post
 * type archives. Layout (grid/list, columns, sidebar) comes from the options.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_post_type = 'post';

if ( is_post_type_archive() ) {
	$es_post_type = get_query_var( 'post_type' );
	if ( is_array( $es_post_type ) ) {
		$es_post_type = reset( $es_post_type );
	}
	$es_post_type = (string) $es_post_type;
} elseif ( is_tax() || is_category() || is_tag() ) {
	$es_object = get_queried_object();
	if ( $es_object && ! empty( $es_object->object_type ) && is_array( $es_object->object_type ) ) {
		$es_post_type = (string) reset( $es_object->object_type );
	}
} elseif ( get_post_type() ) {
	$es_post_type = (string) get_post_type();
}

$es_is_project = 'project' === $es_post_type;
$es_is_product = 'product' === $es_post_type;

if ( $es_is_project ) {
	$es_columns = max( 1, min( 4, (int) es_opt( 'project_archive_columns', 3 ) ) );
	$es_sidebar = es_sidebar_visible( 'project' );
	$es_title   = (string) es_opt( 'project_archive_title', __( 'پروژه‌های نورپردازی', 'erfan-sanat' ) );
	$es_desc    = (string) es_opt( 'project_archive_text', '' );
} elseif ( $es_is_product ) {
	$es_columns = max( 1, min( 4, (int) es_opt( 'product_archive_columns', 3 ) ) );
	$es_sidebar = es_sidebar_visible( 'shop' );
	$es_title   = (string) es_opt( 'wc_shop_title', __( 'فروشگاه تجهیزات نورپردازی', 'erfan-sanat' ) );
	$es_desc    = (string) es_opt( 'wc_shop_text', '' );
} else {
	$es_columns = max( 1, min( 4, (int) es_opt( 'blog_columns', 3 ) ) );
	$es_sidebar = es_sidebar_visible( 'blog' );
	$es_title   = wp_strip_all_tags( (string) get_the_archive_title() );
	$es_desc    = (string) get_the_archive_description();
}

$es_layout = ( ! $es_is_project && ! $es_is_product && 'list' === es_opt( 'blog_layout', 'grid' ) ) ? 'list' : 'grid';
?>
<main id="main" class="es-main es-main--archive" role="main">
	<?php
	get_template_part(
		'template-parts/global/archive-view',
		null,
		array(
			'post_type'   => $es_post_type,
			'layout'      => $es_layout,
			'columns'     => $es_columns,
			'sidebar'     => $es_sidebar,
			'eyebrow'     => $es_is_project ? __( 'نمونه‌کارها', 'erfan-sanat' ) : __( 'آرشیو', 'erfan-sanat' ),
			'title'       => $es_title,
			'desc'        => $es_desc,
			'empty_title' => $es_is_product
				? __( 'محصولی با این مشخصات پیدا نشد.', 'erfan-sanat' )
				: __( 'موردی در این بخش پیدا نشد.', 'erfan-sanat' ),
		)
	);
	?>
</main>

<?php
get_footer();
