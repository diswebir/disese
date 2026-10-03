<?php
/**
 * Related articles.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_count = max( 1, (int) es_opt( 'blog_related_count', 3 ) );

if ( ! $es_count ) {
	return;
}

$es_query = es_related_query( get_the_ID(), 'post', 'category', $es_count );

if ( ! $es_query->have_posts() ) {
	return;
}
?>
<section class="es-section es-section--related" aria-labelledby="es-related-posts-title">
	<div class="es-container">
		<h2 class="es-section__title" id="es-related-posts-title"><?php esc_html_e( 'مقالات مرتبط', 'erfan-sanat' ); ?></h2>

		<div class="es-grid es-grid--posts es-grid--cols-<?php echo esc_attr( (string) min( 3, $es_count ) ); ?>">
			<?php
			while ( $es_query->have_posts() ) :
				$es_query->the_post();
				get_template_part( 'template-parts/cards/post-card', null, array( 'show_excerpt' => false ) );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
