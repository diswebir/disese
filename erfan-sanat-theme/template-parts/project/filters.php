<?php
/**
 * Project filters (category + location) shared by the projects landing page,
 * the project archive and the project single sidebar.
 *
 * @param array $args {
 *     @type string $base     Base URL the filters link to (default: current permalink).
 *     @type string $cat      Active project_cat slug.
 *     @type string $location Active project_location slug.
 *     @type string $title    Section heading for "clear filters" anchor id.
 * }
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_base = isset( $args['base'] ) && $args['base'] ? (string) $args['base'] : (string) get_permalink();

if ( ! $es_base ) {
	$es_base = home_url( '/' );
}

$es_active_cat = isset( $args['cat'] ) ? (string) $args['cat'] : '';

if ( '' === $es_active_cat ) {
	$es_active_cat = isset( $_GET['project_cat'] ) ? sanitize_title( wp_unslash( $_GET['project_cat'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only filter.
}

$es_active_location = isset( $args['location'] ) ? (string) $args['location'] : '';

if ( '' === $es_active_location ) {
	$es_active_location = isset( $_GET['project_location'] ) ? sanitize_title( wp_unslash( $_GET['project_location'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only filter.
}

$es_filter_groups = array(
	array(
		'taxonomy' => 'project_cat',
		'query'    => 'project_cat',
		'title'    => __( 'دسته‌بندی پروژه', 'erfan-sanat' ),
		'active'   => $es_active_cat,
	),
	array(
		'taxonomy' => 'project_location',
		'query'    => 'project_location',
		'title'    => __( 'موقعیت پروژه', 'erfan-sanat' ),
		'active'   => $es_active_location,
	),
);
?>
<div class="es-project-filters">
	<?php
	foreach ( $es_filter_groups as $es_group ) :
		$es_terms = get_terms(
			array(
				'taxonomy'   => $es_group['taxonomy'],
				'hide_empty' => false,
				'orderby'    => 'count',
				'order'      => 'DESC',
			)
		);

		if ( is_wp_error( $es_terms ) || ! $es_terms ) {
			continue;
		}
		?>
		<section class="es-widget es-widget--filter">
			<h2 class="es-widget__title"><?php echo esc_html( $es_group['title'] ); ?></h2>

			<ul class="es-widget__list es-filter-list">
				<?php foreach ( $es_terms as $es_term ) : ?>
					<li<?php echo $es_group['active'] === $es_term->slug ? ' class="is-active"' : ''; ?>>
						<a href="<?php echo esc_url( add_query_arg( $es_group['query'], $es_term->slug, $es_base ) ); ?>">
							<?php echo esc_html( $es_term->name ); ?>
							<span class="es-count"><?php echo esc_html( es_num( number_format_i18n( $es_term->count ) ) ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endforeach; ?>

	<?php if ( $es_active_cat || $es_active_location ) : ?>
		<a class="es-btn es-btn--ghost es-btn--block es-filter-list__reset" href="<?php echo esc_url( $es_base ); ?>">
			<?php esc_html_e( 'حذف فیلترها', 'erfan-sanat' ); ?>
		</a>
	<?php endif; ?>
</div>
