<?php
/**
 * Blog posts page (the static "posts page" set in Reading settings).
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_page_id = (int) get_option( 'page_for_posts' );
$es_title   = $es_page_id ? get_the_title( $es_page_id ) : __( 'مقالات فنی', 'erfan-sanat' );
$es_desc    = $es_page_id ? get_post_field( 'post_content', $es_page_id ) : '';
$es_title   = $es_title ? $es_title : __( 'مقالات فنی', 'erfan-sanat' );
?>
<main id="main" class="es-main es-main--blog" role="main">
	<?php
	get_template_part(
		'template-parts/global/archive-view',
		null,
		array(
			'post_type' => 'post',
			'layout'    => 'list' === es_opt( 'blog_layout', 'grid' ) ? 'list' : 'grid',
			'columns'   => max( 1, min( 4, (int) es_opt( 'blog_columns', 3 ) ) ),
			'sidebar'   => (bool) es_opt( 'blog_sidebar', true ),
			'eyebrow'   => __( 'مقالات فنی و آموزش', 'erfan-sanat' ),
			'title'     => $es_title,
			'desc'      => wp_strip_all_tags( (string) $es_desc ),
		)
	);
	?>
</main>
<?php
get_footer();
