<?php
/**
 * Template Name: تمام‌عرض (بدون ستون کناری)
 * Template Post Type: page
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<main id="main" class="es-main es-main--page es-main--fullwidth" role="main">
		<article <?php post_class( 'es-single es-single--page es-single--fullwidth' ); ?>>
			<header class="es-single__header">
				<div class="es-container">
					<h1 class="es-single__title"><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) : ?>
						<p class="es-single__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
				</div>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="es-single__cover es-single__cover--wide">
					<?php es_post_thumbnail( get_the_ID(), 'es-hero', 'es-single__cover-image' ); ?>
				</figure>
			<?php endif; ?>

			<div class="es-container es-single__body es-entry es-entry--wide">
				<?php the_content(); ?>
				<?php wp_link_pages( array( 'before' => '<nav class="es-entry__pages">', 'after' => '</nav>' ) ); ?>
			</div>
		</article>
	</main>
	<?php
endwhile;

get_footer();
