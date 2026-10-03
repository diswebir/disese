<?php
/**
 * Fallback template.
 *
 * WordPress falls back to index.php when no more specific template matches.
 * It renders the shared archive view so the listing markup stays identical.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_columns = is_active_sidebar( 'es-blog-sidebar' ) || es_opt( 'blog_sidebar', true )
	? max( 1, min( 4, (int) es_opt( 'blog_columns', 3 ) ) )
	: 3;
?>
<main id="main" class="es-main es-main--index" role="main">
	<?php
	get_template_part(
		'template-parts/global/archive-view',
		null,
		array(
			'post_type' => get_post_type() ? (string) get_post_type() : 'post',
			'layout'    => 'list' === es_opt( 'blog_layout', 'grid' ) ? 'list' : 'grid',
			'columns'   => $es_columns,
			'sidebar'   => (bool) es_opt( 'blog_sidebar', true ),
			'eyebrow'   => __( 'آرشیو', 'erfan-sanat' ),
			'title'     => wp_strip_all_tags( (string) get_the_archive_title() ),
			'desc'      => (string) get_the_archive_description(),
		)
	);
	?>
</main>
<?php
get_footer();
