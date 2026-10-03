<?php
/**
 * Single project content: hero, gallery, specification table, description,
 * gallery lightbox and related projects.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_post_id  = get_the_ID();
$es_gallery  = es_meta( '_es_before_after_gallery', $es_post_id, array() );
$es_gallery  = is_array( $es_gallery ) ? array_filter( array_map( 'absint', $es_gallery ) ) : array();
$es_client   = (string) es_meta( '_es_project_client', $es_post_id, '' );
$es_date     = (string) es_meta( '_es_completion_date', $es_post_id, '' );
$es_pixels   = (int) es_meta( '_es_total_pixel_count', $es_post_id, 0 );
$es_power    = (float) es_meta( '_es_total_power_kw', $es_post_id, 0 );
$es_video    = (string) es_meta( '_es_project_drone_video', $es_post_id, '' );
$es_coords   = (string) es_meta( '_es_project_map_coords', $es_post_id, '' );
$es_cats     = get_the_terms( $es_post_id, 'project_cat' );
$es_places   = get_the_terms( $es_post_id, 'project_location' );
?>
<main id="main" class="es-main es-main--single es-main--project" role="main">

	<article <?php post_class( 'es-single es-single--project' ); ?>>

		<?php $es_has_cover = has_post_thumbnail( $es_post_id ); ?>

		<header class="es-single__hero<?php echo $es_has_cover ? ' es-single__hero--media' : ' es-single__hero--plain'; ?>">
			<?php if ( $es_has_cover ) : ?>
				<div class="es-single__hero-media">
					<?php es_post_thumbnail( $es_post_id, 'es-hero', 'es-single__hero-image' ); ?>
					<span class="es-single__hero-overlay" aria-hidden="true"></span>
				</div>
			<?php endif; ?>

			<div class="es-container es-single__hero-inner">
				<?php if ( $es_cats && ! is_wp_error( $es_cats ) ) : ?>
					<ul class="es-single__eyebrow">
						<?php foreach ( array_slice( $es_cats, 0, 2 ) as $es_term ) : ?>
							<li><a href="<?php echo esc_url( (string) get_term_link( $es_term ) ); ?>"><?php echo esc_html( $es_term->name ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<h1 class="es-single__title"><?php the_title(); ?></h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="es-single__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>

				<ul class="es-single__meta">
					<?php if ( $es_client ) : ?>
						<li>
							<?php es_icon( 'user', 'es-icon', 16 ); ?>
							<span><?php echo esc_html( $es_client ); ?></span>
						</li>
					<?php endif; ?>

					<?php if ( $es_date ) : ?>
						<li>
							<?php es_icon( 'calendar', 'es-icon', 16 ); ?>
							<span><?php echo esc_html( $es_date ); ?></span>
						</li>
					<?php endif; ?>

					<?php if ( $es_places && ! is_wp_error( $es_places ) ) : ?>
						<li>
							<?php es_icon( 'pin', 'es-icon', 16 ); ?>
							<span>
								<?php
								echo esc_html(
									implode(
										'، ',
										wp_list_pluck( $es_places, 'name' )
									)
								);
								?>
							</span>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</header>

		<div class="es-container es-single__layout">
			<div class="es-single__body">
				<?php if ( $es_pixels || $es_power ) : ?>
					<ul class="es-single__stats">
						<?php if ( $es_pixels ) : ?>
							<li>
								<span class="es-single__stat-value"><?php echo esc_html( es_num( number_format_i18n( $es_pixels ) ) ); ?></span>
								<span class="es-single__stat-label"><?php esc_html_e( 'پیکسل نور', 'erfan-sanat' ); ?></span>
							</li>
						<?php endif; ?>

						<?php if ( $es_power ) : ?>
							<li>
								<span class="es-single__stat-value"><?php echo esc_html( es_num( number_format_i18n( $es_power, 1 ) ) ); ?></span>
								<span class="es-single__stat-label"><?php esc_html_e( 'کیلووات توان', 'erfan-sanat' ); ?></span>
							</li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>

				<div class="es-entry">
					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<nav class="es-entry__pages">', 'after' => '</nav>' ) ); ?>
				</div>

				<?php if ( $es_gallery ) : ?>
					<section class="es-project-gallery" aria-labelledby="es-project-gallery-title">
						<h2 class="es-section__title es-section__title--sm" id="es-project-gallery-title"><?php esc_html_e( 'گالری پروژه', 'erfan-sanat' ); ?></h2>

						<ul class="es-project-gallery__list" data-es-lightbox>
							<?php foreach ( $es_gallery as $es_image_id ) : ?>
								<?php
								$es_full  = wp_get_attachment_image_url( $es_image_id, 'full' );
								$es_thumb = wp_get_attachment_image( $es_image_id, 'es-card', false, array( 'class' => 'es-project-gallery__image', 'loading' => 'lazy' ) );
								?>
								<li>
									<a class="es-project-gallery__item" href="<?php echo esc_url( (string) $es_full ); ?>" data-es-lightbox-item>
										<?php echo $es_thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image output. ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

				<?php if ( $es_coords || $es_video ) : ?>
					<section class="es-project-extra" aria-labelledby="es-project-extra-title">
						<h2 class="es-section__title es-section__title--sm" id="es-project-extra-title"><?php esc_html_e( 'موقعیت و ویدیو', 'erfan-sanat' ); ?></h2>

						<?php if ( $es_video ) : ?>
							<p>
								<a class="es-btn es-btn--outline" href="<?php echo esc_url( $es_video ); ?>" target="_blank" rel="noopener nofollow">
									<?php es_icon( 'play', 'es-icon', 18 ); ?>
									<span><?php esc_html_e( 'تماشای ویدیوی پروژه', 'erfan-sanat' ); ?></span>
								</a>
							</p>
						<?php endif; ?>

						<?php if ( $es_coords ) : ?>
							<p class="es-project-extra__coords">
								<?php es_icon( 'pin', 'es-icon', 16 ); ?>
								<span><?php esc_html_e( 'مختصات:', 'erfan-sanat' ); ?> <span dir="ltr"><?php echo esc_html( $es_coords ); ?></span></span>
							</p>
						<?php endif; ?>
					</section>
				<?php endif; ?>

				<?php get_template_part( 'template-parts/global/share' ); ?>
			</div>

			<aside class="es-single__aside">
				<?php get_template_part( 'template-parts/project/specs' ); ?>
				<?php if ( es_opt( 'project_show_filters', true ) ) : ?>
					<?php
					get_template_part(
						'template-parts/project/filters',
						null,
						array( 'base' => (string) get_post_type_archive_link( 'project' ) )
					);
					?>
				<?php endif; ?>
			</aside>
		</div>
	</article>

	<?php get_template_part( 'template-parts/project/related' ); ?>
</main>
