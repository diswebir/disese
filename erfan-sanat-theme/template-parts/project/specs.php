<?php
/**
 * Project specification card (sidebar).
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_id      = get_the_ID();
$es_rows    = array();
$es_client  = (string) es_meta( '_es_project_client', $es_id, '' );
$es_date    = (string) es_meta( '_es_completion_date', $es_id, '' );
$es_pixels  = (int) es_meta( '_es_total_pixel_count', $es_id, 0 );
$es_power   = (float) es_meta( '_es_total_power_kw', $es_id, 0 );
$es_places  = get_the_terms( $es_id, 'project_location' );
$es_cats    = get_the_terms( $es_id, 'project_cat' );

if ( $es_client ) {
	$es_rows[ __( 'کارفرما', 'erfan-sanat' ) ] = $es_client;
}

if ( $es_date ) {
	$es_rows[ __( 'تاریخ اجرا', 'erfan-sanat' ) ] = $es_date;
}

if ( $es_pixels ) {
	$es_rows[ __( 'تعداد پیکسل', 'erfan-sanat' ) ] = es_num( number_format_i18n( $es_pixels ) );
}

if ( $es_power ) {
	$es_rows[ __( 'توان مصرفی', 'erfan-sanat' ) ] = es_num( number_format_i18n( $es_power, 1 ) ) . ' ' . __( 'کیلووات', 'erfan-sanat' );
}

if ( $es_places && ! is_wp_error( $es_places ) ) {
	$es_rows[ __( 'موقعیت', 'erfan-sanat' ) ] = implode( '، ', wp_list_pluck( $es_places, 'name' ) );
}

if ( $es_cats && ! is_wp_error( $es_cats ) ) {
	$es_rows[ __( 'دستهٔ پروژه', 'erfan-sanat' ) ] = implode( '، ', wp_list_pluck( $es_cats, 'name' ) );
}

if ( ! $es_rows || ! es_opt( 'project_show_specs', true ) ) {
	return;
}
?>
<section class="es-spec-card" aria-labelledby="es-project-specs-title">
	<h2 class="es-spec-card__title" id="es-project-specs-title"><?php esc_html_e( 'مشخصات پروژه', 'erfan-sanat' ); ?></h2>

	<ul class="es-spec-card__list">
		<?php foreach ( $es_rows as $es_label => $es_value ) : ?>
			<li>
				<span class="es-spec-card__label"><?php echo esc_html( $es_label ); ?></span>
				<span class="es-spec-card__value"><?php echo esc_html( $es_value ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>

	<a class="es-btn es-btn--primary es-btn--block" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
		<?php esc_html_e( 'اجرای پروژه‌ای مشابه', 'erfan-sanat' ); ?>
		<?php es_icon( 'arrow', 'es-icon', 16 ); ?>
	</a>
</section>
