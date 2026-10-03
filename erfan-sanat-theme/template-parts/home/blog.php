<?php
/**
 * Homepage articles teaser (off by default, see the Homepage options tab).
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! es_opt( 'home_blog_enable', false ) ) {
	return;
}

$es_count = max( 1, (int) es_opt( 'home_blog_count', 3 ) );
$es_posts = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => $es_count,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<section class="es-section es-section--blog" id="blog">
	<div class="es-container">

		<?php
		es_section_header(
			array(
				'eyebrow' => __( 'مقالات فنی', 'erfan-sanat' ),
				'title'   => (string) es_opt( 'home_blog_title', __( 'مقالات فنی و آموزش‌ها', 'erfan-sanat' ) ),
				'align'   => 'center',
			)
		);
		?>

		<?php if ( $es_posts->have_posts() ) : ?>
			<div class="es-grid es-grid--posts es-grid--cols-3">
				<?php
				while ( $es_posts->have_posts() ) :
					$es_posts->the_post();
					get_template_part( 'template-parts/cards/post-card' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php else : ?>
			<?php
			es_empty_state(
				array(
					'title' => __( 'هنوز مقاله‌ای منتشر نشده است.', 'erfan-sanat' ),
					'icon'  => 'bulb',
				)
			);
			?>
		<?php endif; ?>
	</div>
</section>
