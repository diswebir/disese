<?php
/**
 * Related projects.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_count = max( 1, (int) es_opt( 'project_related_count', 3 ) );
$es_query = es_related_query( get_the_ID(), 'project', 'project_cat', $es_count );

if ( ! $es_query->have_posts() ) {
	return;
}
?>
<section class="es-section es-section--related" aria-labelledby="es-related-projects-title">
	<div class="es-container">
		<h2 class="es-section__title" id="es-related-projects-title"><?php esc_html_e( 'پروژه‌های مرتبط', 'erfan-sanat' ); ?></h2>

		<div class="es-grid es-grid--projects es-grid--cols-<?php echo esc_attr( (string) min( 3, $es_count ) ); ?>">
			<?php
			while ( $es_query->have_posts() ) :
				$es_query->the_post();
				get_template_part( 'template-parts/cards/project-card', null, array( 'show_terms' => false ) );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
