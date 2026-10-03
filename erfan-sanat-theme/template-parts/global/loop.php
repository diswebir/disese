<?php
/**
 * Shared archive/search loop renderer.
 *
 * All archive templates (archive.php, index.php, home.php, search.php) render
 * their cards through this single template part so the markup, the card kinds
 * and the pagination exist in exactly one place.
 *
 * @param array $args post_type, layout, columns, empty_title, empty_text, search.
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_type    = isset( $args['post_type'] ) ? (string) $args['post_type'] : 'post';
$es_layout  = isset( $args['layout'] ) ? (string) $args['layout'] : 'grid';
$es_columns = isset( $args['columns'] ) ? max( 1, min( 4, (int) $args['columns'] ) ) : 3;
$es_kind    = 'product' === $es_type ? 'products' : ( 'project' === $es_type ? 'projects' : 'posts' );

if ( ! have_posts() ) {
	get_template_part(
		'template-parts/content/content-none',
		null,
		array(
			'title' => isset( $args['empty_title'] ) ? $args['empty_title'] : __( 'موردی یافت نشد.', 'erfan-sanat' ),
			'text'  => isset( $args['empty_text'] ) ? $args['empty_text'] : '',
		)
	);

	return;
}
?>
<div class="es-grid es-grid--<?php echo esc_attr( $es_kind ); ?> es-grid--<?php echo esc_attr( $es_layout ); ?> es-grid--cols-<?php echo esc_attr( (string) $es_columns ); ?>">
	<?php
	while ( have_posts() ) :
		the_post();

		if ( 'project' === $es_type ) {
			get_template_part( 'template-parts/cards/project-card' );
		} elseif ( 'product' === $es_type ) {
			get_template_part( 'template-parts/cards/product-card' );
		} else {
			get_template_part(
				'template-parts/cards/post-card',
				null,
				array( 'show_excerpt' => 'list' === $es_layout || es_opt( 'blog_excerpt_length', 24 ) > 0 )
			);
		}
	endwhile;
	?>
</div>

<?php
es_pagination();
