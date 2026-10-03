<?php
/**
 * Feature card (why-us / process).
 *
 * @param array $args title, desc, icon, index, total.
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_title = isset( $args['title'] ) ? (string) $args['title'] : '';
$es_desc  = isset( $args['desc'] ) ? (string) $args['desc'] : '';
$es_icon  = isset( $args['icon'] ) ? (string) $args['icon'] : 'certified';

if ( ! $es_title ) {
	return;
}
?>
<li class="es-card es-card--feature">
	<span class="es-card__icon" aria-hidden="true"><?php echo es_get_icon( $es_icon, 'es-icon', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
	<h3 class="es-card__title"><?php echo esc_html( $es_title ); ?></h3>

	<?php if ( $es_desc ) : ?>
		<p class="es-card__text"><?php echo esc_html( $es_desc ); ?></p>
	<?php endif; ?>
</li>
