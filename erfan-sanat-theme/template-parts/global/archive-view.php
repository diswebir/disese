<?php
/**
 * Shared archive view (hero + layout + loop + sidebar).
 *
 * archive.php, index.php, home.php and search.php are thin wrappers around
 * this single partial so that listing markup is defined exactly once.
 *
 * @param array $args post_type, layout, columns, sidebar, eyebrow, title, desc, empty_title, empty_text, show_count.
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_type    = isset( $args['post_type'] ) ? (string) $args['post_type'] : 'post';
$es_layout  = isset( $args['layout'] ) ? (string) $args['layout'] : 'grid';
$es_columns = isset( $args['columns'] ) ? max( 1, min( 4, (int) $args['columns'] ) ) : 3;
$es_side    = ! empty( $args['sidebar'] );
$es_eyebrow = isset( $args['eyebrow'] ) ? (string) $args['eyebrow'] : __( 'آرشیو', 'erfan-sanat' );
$es_title   = isset( $args['title'] ) ? (string) $args['title'] : wp_strip_all_tags( (string) get_the_archive_title() );
$es_desc    = isset( $args['desc'] ) ? (string) $args['desc'] : (string) get_the_archive_description();
$es_count   = ! isset( $args['show_count'] ) || $args['show_count'];
$es_found   = isset( $GLOBALS['wp_query']->found_posts ) ? (int) $GLOBALS['wp_query']->found_posts : 0;
?>
<header class="es-archive-hero">
	<div class="es-container">
		<?php es_section_header( array( 'eyebrow' => $es_eyebrow, 'title' => $es_title, 'tag' => 'h1', 'align' => 'start' ) ); ?>

		<?php if ( $es_desc ) : ?>
			<div class="es-archive-hero__desc"><?php echo wp_kses_post( wpautop( $es_desc ) ); ?></div>
		<?php endif; ?>

		<?php if ( $es_count ) : ?>
			<p class="es-archive-hero__count">
				<?php
				printf(
					/* translators: %s: number of results */
					esc_html__( '%s مورد یافت شد.', 'erfan-sanat' ),
					esc_html( es_num( number_format_i18n( $es_found ) ) )
				);
				?>
			</p>
		<?php endif; ?>
	</div>
</header>

<div class="es-container es-archive">
	<div class="es-archive__layout <?php echo $es_side ? 'has-sidebar' : 'no-sidebar'; ?>">
		<div class="es-archive__body">
			<?php
			get_template_part(
				'template-parts/global/loop',
				null,
				array(
					'post_type'   => $es_type,
					'layout'      => $es_layout,
					'columns'     => $es_columns,
					'empty_title' => isset( $args['empty_title'] ) ? $args['empty_title'] : __( 'موردی یافت نشد.', 'erfan-sanat' ),
					'empty_text'  => isset( $args['empty_text'] ) ? $args['empty_text'] : '',
				)
			);
			?>
		</div>

		<?php
		if ( $es_side ) {
			$es_sidebar_context = 'blog';

			if ( 'project' === $es_type ) {
				$es_sidebar_context = 'project';
			} elseif ( 'product' === $es_type ) {
				$es_sidebar_context = 'shop';
			}

			get_template_part(
				'template-parts/global/sidebar',
				$es_sidebar_context,
				array( 'name' => $es_sidebar_context )
			);
		}
		?>
	</div>
</div>
