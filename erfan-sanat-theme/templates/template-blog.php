<?php
/**
 * Template Name: آرشیو مقالات
 * Template Post Type: page
 *
 * Articles landing page: category chips, the latest posts and a newsletter
 * style call to action.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="es-main es-main--blog" role="main">

	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<header class="es-archive-hero">
			<div class="es-container">
				<?php
				es_section_header(
					array(
						'eyebrow' => __( 'مقالات فنی', 'erfan-sanat' ),
						'title'   => get_the_title(),
						'tag'     => 'h1',
						'align'   => 'start',
					)
				);
				?>
				<?php if ( get_the_content() ) : ?>
					<div class="es-archive-hero__desc es-entry es-entry--compact"><?php the_content(); ?></div>
				<?php endif; ?>
			</div>
		</header>
		<?php
	endwhile;
	?>

	<div class="es-container">
		<?php
		$es_cats = get_categories( array( 'hide_empty' => false, 'number' => 12 ) );

		if ( $es_cats ) :
			?>
			<nav class="es-cat-nav" aria-label="<?php esc_attr_e( 'دسته‌بندی مقالات', 'erfan-sanat' ); ?>">
				<ul class="es-cat-nav__list">
					<?php foreach ( $es_cats as $es_cat ) : ?>
						<li>
							<a class="es-cat-nav__item" href="<?php echo esc_url( (string) get_category_link( $es_cat ) ); ?>">
								<span class="es-cat-nav__name"><?php echo esc_html( $es_cat->name ); ?></span>
								<span class="es-cat-nav__count"><?php echo esc_html( es_num( number_format_i18n( $es_cat->count ) ) ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>

		<?php
		$es_posts = new WP_Query(
			array(
				'post_type'      => 'post',
				'posts_per_page' => max( 1, (int) es_opt( 'blog_per_page', 9 ) ),
				'paged'          => max( 1, (int) get_query_var( 'paged' ) ),
			)
		);

		$GLOBALS['wp_query'] = $es_posts; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		get_template_part(
			'template-parts/global/loop',
			null,
			array(
				'post_type'   => 'post',
				'layout'      => 'list' === es_opt( 'blog_layout', 'grid' ) ? 'list' : 'grid',
				'columns'     => max( 1, min( 4, (int) es_opt( 'blog_columns', 3 ) ) ),
				'empty_title' => __( 'هنوز مقاله‌ای منتشر نشده است.', 'erfan-sanat' ),
			)
		);

		wp_reset_postdata();
		?>
	</div>
</main>

<?php
get_footer();
