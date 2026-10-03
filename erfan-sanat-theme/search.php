<?php
/**
 * Search results.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_query_text = get_search_query();
?>
<main id="main" class="es-main es-main--search" role="main">
	<?php
	get_template_part(
		'template-parts/global/archive-view',
		null,
		array(
			'post_type'   => 'post',
			'layout'      => 'list',
			'columns'     => 3,
			'sidebar'     => false,
			'eyebrow'     => __( 'نتایج جست‌وجو', 'erfan-sanat' ),
			'title'       => sprintf( /* translators: %s: search term */ __( 'نتایج برای: %s', 'erfan-sanat' ), $es_query_text ),
			'desc'        => '',
			'show_count'  => true,
			'empty_title' => __( 'چیزی با این عبارت پیدا نشد.', 'erfan-sanat' ),
			'empty_text'  => __( 'املای عبارت را بررسی کنید یا از دسته‌بندی‌ها و جست‌وجوی محصولات استفاده کنید.', 'erfan-sanat' ),
		)
	);
	?>
</main>
<?php
get_footer();
