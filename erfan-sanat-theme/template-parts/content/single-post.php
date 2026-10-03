<?php
/**
 * Single article content: header, table of contents, content, FAQ,
 * reviewer box and related posts.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_post_id  = get_the_ID();
$es_reviewer = (string) es_meta( '_es_technical_reviewer', $es_post_id, '' );
$es_file     = (int) es_meta( '_es_software_project_file', $es_post_id, 0 );
$es_faqs     = es_meta( '_es_faq_schema_repeater', $es_post_id, array() );
?>
<main id="main" class="es-main es-main--single es-main--post" role="main">

	<article <?php post_class( 'es-single es-single--post' ); ?>>

		<header class="es-single__header">
			<div class="es-container">
				<?php es_term_pills( $es_post_id, 'category', 2 ); ?>

				<h1 class="es-single__title"><?php the_title(); ?></h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="es-single__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>

				<ul class="es-single__meta">
					<li>
						<?php es_icon( 'user', 'es-icon', 16 ); ?>
						<span><?php echo esc_html( get_the_author() ); ?></span>
					</li>
					<li>
						<?php es_icon( 'calendar', 'es-icon', 16 ); ?>
						<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( es_num( get_the_date() ) ); ?></time>
					</li>
					<?php if ( es_opt( 'blog_show_reading_time', true ) ) : ?>
						<li>
							<?php es_icon( 'clock', 'es-icon', 16 ); ?>
							<span>
								<?php
								printf(
									/* translators: %s: minutes */
									esc_html__( '%s دقیقه مطالعه', 'erfan-sanat' ),
									esc_html( es_num( (string) es_reading_time( $es_post_id ) ) )
								);
								?>
							</span>
						</li>
					<?php endif; ?>
					<?php if ( $es_reviewer ) : ?>
						<li>
							<?php es_icon( 'certified', 'es-icon', 16 ); ?>
							<span><?php printf( /* translators: %s: reviewer name */ esc_html__( 'بازبینی فنی: %s', 'erfan-sanat' ), esc_html( $es_reviewer ) ); ?></span>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="es-single__cover">
				<?php es_post_thumbnail( $es_post_id, 'es-hero', 'es-single__cover-image' ); ?>
			</figure>
		<?php endif; ?>

		<div class="es-container es-single__layout">
			<div class="es-single__body">
				<?php if ( es_opt( 'blog_show_toc', true ) ) : ?>
					<?php get_template_part( 'template-parts/blog/toc' ); ?>
				<?php endif; ?>

				<div class="es-entry" data-es-entry>
					<?php the_content(); ?>
					<?php wp_link_pages( array( 'before' => '<nav class="es-entry__pages">', 'after' => '</nav>' ) ); ?>
				</div>

				<?php if ( $es_file ) : ?>
					<?php $es_file_url = wp_get_attachment_url( $es_file ); ?>
					<?php if ( $es_file_url ) : ?>
						<div class="es-download">
							<?php es_icon( 'download', 'es-icon', 22 ); ?>
							<div>
								<strong><?php esc_html_e( 'فایل پروژهٔ نرم‌افزاری', 'erfan-sanat' ); ?></strong>
								<p><?php esc_html_e( 'فایل کامل پروژه را برای اجرای محلی دانلود کنید.', 'erfan-sanat' ); ?></p>
							</div>
							<a class="es-btn es-btn--primary" href="<?php echo esc_url( $es_file_url ); ?>" download>
								<?php esc_html_e( 'دانلود فایل', 'erfan-sanat' ); ?>
							</a>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( is_array( $es_faqs ) && $es_faqs ) : ?>
					<?php get_template_part( 'template-parts/blog/faq' ); ?>
				<?php endif; ?>

				<?php es_term_pills( $es_post_id, 'post_tag', 6 ); ?>

				<?php get_template_part( 'template-parts/global/share' ); ?>
			</div>

			<aside class="es-single__aside">
				<?php get_template_part( 'template-parts/global/sidebar', 'blog' ); ?>
			</aside>
		</div>
	</article>

	<?php get_template_part( 'template-parts/blog/related' ); ?>
</main>
