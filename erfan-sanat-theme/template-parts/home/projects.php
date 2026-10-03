<?php
/**
 * Homepage featured projects.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! es_opt( 'home_projects_enable', true ) ) {
	return;
}

$es_count  = max( 1, (int) es_opt( 'home_projects_count', 3 ) );
$es_source = sanitize_key( (string) es_opt( 'home_projects_source', 'recent' ) );

$es_args = array(
	'post_type'           => 'project',
	'posts_per_page'      => $es_count,
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
	'post_status'         => 'publish',
);

if ( 'featured' === $es_source ) {
	$es_args['meta_query'] = array(
		array(
			'key'   => '_es_featured',
			'value' => '1',
		),
	);
}

$es_projects = new WP_Query( $es_args );

if ( ! $es_projects->have_posts() && 'featured' === $es_source ) {
	unset( $es_args['meta_query'] );
	$es_projects = new WP_Query( $es_args );
}
?>
<section class="es-section es-section--projects" id="projects">
	<div class="es-container">

		<?php
		es_section_header(
			array(
				'eyebrow' => (string) es_opt( 'home_projects_eyebrow', '' ),
				'title'   => (string) es_opt( 'home_projects_title', '' ),
				'text'    => (string) es_opt( 'home_projects_text', '' ),
				'align'   => 'center',
				'id'      => 'projects-title',
			)
		);
		?>

		<?php if ( $es_projects->have_posts() ) : ?>
			<div class="es-grid es-grid--projects es-grid--cols-<?php echo esc_attr( (string) min( 3, $es_count ) ); ?>">
				<?php
				$es_index = 0;

				while ( $es_projects->have_posts() ) :
					$es_projects->the_post();
					$es_index++;
					get_template_part( 'template-parts/cards/project-card', null, array( 'index' => $es_index ) );
				endwhile;
				?>
			</div>

			<p class="es-section__footer">
				<a class="es-btn es-btn--outline es-btn--lg" href="<?php echo esc_url( es_projects_url() ); ?>">
					<span><?php echo esc_html( (string) es_opt( 'home_projects_link_text', __( 'مشاهدهٔ آرشیو کامل پروژه‌ها', 'erfan-sanat' ) ) ); ?></span>
					<?php es_icon( 'arrow', 'es-icon', 18 ); ?>
				</a>
			</p>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<?php
			es_empty_state(
				array(
					'title'       => __( 'هنوز پروژه‌ای منتشر نشده است.', 'erfan-sanat' ),
					'text'        => __( 'برای نمایش این بخش، اولین پروژهٔ نورپردازی خود را از پیشخوان وردپرس اضافه کنید.', 'erfan-sanat' ),
					'icon'        => 'tunnel',
					'button_text' => current_user_can( 'edit_posts' ) ? __( 'افزودن پروژه', 'erfan-sanat' ) : '',
					'button_url'  => current_user_can( 'edit_posts' ) ? admin_url( 'post-new.php?post_type=project' ) : '',
				)
			);
			?>
		<?php endif; ?>
	</div>
</section>
