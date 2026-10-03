<?php
/**
 * Template Name: آرشیو پروژه‌های نورپردازی
 * Template Post Type: page
 *
 * A dedicated projects landing page: filter by category and location plus the
 * full project grid, independent of the page content.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_paged    = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$es_per_page = max( 1, (int) es_opt( 'project_per_page', 9 ) );
$es_cat      = isset( $_GET['project_cat'] ) ? sanitize_title( wp_unslash( $_GET['project_cat'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only filter.
$es_place    = isset( $_GET['project_location'] ) ? sanitize_title( wp_unslash( $_GET['project_location'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only filter.

$es_args = array(
	'post_type'      => 'project',
	'posts_per_page' => $es_per_page,
	'paged'          => $es_paged,
);

$es_tax_query = array();

if ( $es_cat ) {
	$es_tax_query[] = array(
		'taxonomy' => 'project_cat',
		'field'    => 'slug',
		'terms'    => $es_cat,
	);
}

if ( $es_place ) {
	$es_tax_query[] = array(
		'taxonomy' => 'project_location',
		'field'    => 'slug',
		'terms'    => $es_place,
	);
}

if ( $es_tax_query ) {
	$es_args['tax_query'] = $es_tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
}

$es_projects = new WP_Query( $es_args );

// The filters feed the shared loop, so the global query is replaced on purpose.
$GLOBALS['wp_query'] = $es_projects; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
?>
<main id="main" class="es-main es-main--archive" role="main">

	<?php
	while ( have_posts() ) :
		the_post();

		if ( get_the_content() ) :
			?>
			<header class="es-archive-hero">
				<div class="es-container">
					<?php
					es_section_header(
						array(
							'eyebrow' => __( 'نمونه‌کارها', 'erfan-sanat' ),
							'title'   => get_the_title(),
							'tag'     => 'h1',
							'align'   => 'start',
						)
					);
					?>
					<div class="es-archive-hero__desc es-entry es-entry--compact"><?php the_content(); ?></div>
				</div>
			</header>
			<?php
		endif;
	endwhile;

	wp_reset_postdata();
	?>

	<div class="es-container es-archive">
		<div class="es-archive__layout has-sidebar">
			<div class="es-archive__body">
				<?php
				get_template_part(
					'template-parts/global/loop',
					null,
					array(
						'post_type'   => 'project',
						'layout'      => 'grid',
						'columns'     => max( 1, min( 4, (int) es_opt( 'project_archive_columns', 3 ) ) ),
						'empty_title' => __( 'پروژه‌ای با این فیلترها پیدا نشد.', 'erfan-sanat' ),
						'empty_text'  => __( 'فیلترها را تغییر دهید یا همهٔ پروژه‌ها را ببینید.', 'erfan-sanat' ),
					)
				);
				?>
			</div>

			<aside class="es-sidebar">
				<?php if ( es_opt( 'project_show_filters', true ) ) : ?>
					<section class="es-widget">
						<h2 class="es-widget__title"><?php esc_html_e( 'دسته‌بندی پروژه', 'erfan-sanat' ); ?></h2>
						<?php
						$es_cats = get_terms( array( 'taxonomy' => 'project_cat', 'hide_empty' => false ) );

						if ( ! is_wp_error( $es_cats ) && $es_cats ) {
							echo '<ul class="es-widget__list es-filter-list">';
							foreach ( $es_cats as $es_term ) {
								printf(
									'<li%1$s><a href="%2$s">%3$s <span class="es-count">(%4$s)</span></a></li>',
									$es_cat === $es_term->slug ? ' class="is-active"' : '',
									esc_url( add_query_arg( 'project_cat', $es_term->slug, get_permalink() ) ),
									esc_html( $es_term->name ),
									esc_html( es_num( number_format_i18n( $es_term->count ) ) )
								);
							}
							echo '</ul>';
						}
						?>
					</section>

					<section class="es-widget">
						<h2 class="es-widget__title"><?php esc_html_e( 'موقعیت پروژه', 'erfan-sanat' ); ?></h2>
						<?php
						$es_locations = get_terms( array( 'taxonomy' => 'project_location', 'hide_empty' => false ) );

						if ( ! is_wp_error( $es_locations ) && $es_locations ) {
							echo '<ul class="es-widget__list es-filter-list">';
							foreach ( $es_locations as $es_term ) {
								printf(
									'<li%1$s><a href="%2$s">%3$s <span class="es-count">(%4$s)</span></a></li>',
									$es_place === $es_term->slug ? ' class="is-active"' : '',
									esc_url( add_query_arg( 'project_location', $es_term->slug, get_permalink() ) ),
									esc_html( $es_term->name ),
									esc_html( es_num( number_format_i18n( $es_term->count ) ) )
								);
							}
							echo '</ul>';
						}
						?>
					</section>

					<?php if ( $es_cat || $es_place ) : ?>
						<a class="es-btn es-btn--ghost es-btn--block" href="<?php echo esc_url( (string) get_permalink() ); ?>">
							<?php esc_html_e( 'حذف فیلترها', 'erfan-sanat' ); ?>
						</a>
					<?php endif; ?>
				<?php endif; ?>
			</aside>
		</div>
	</div>
</main>

<?php
get_footer();
