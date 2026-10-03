<?php
/**
 * Service card.
 *
 * @param array $args title, desc, icon, image, index.
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_title = isset( $args['title'] ) ? (string) $args['title'] : '';
$es_desc  = isset( $args['desc'] ) ? (string) $args['desc'] : '';
$es_icon  = isset( $args['icon'] ) ? (string) $args['icon'] : 'spark';
$es_image = isset( $args['image'] ) ? (int) $args['image'] : 0;
$es_index = isset( $args['index'] ) ? (int) $args['index'] : 0;

if ( ! $es_title ) {
	return;
}
?>
<li class="es-card es-card--service">
	<span class="es-card__icon" aria-hidden="true"><?php echo es_get_icon( $es_icon, 'es-icon', 26 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>

	<?php if ( $es_index ) : ?>
		<span class="es-card__number" aria-hidden="true"><?php echo esc_html( es_index_label( $es_index ) ); ?></span>
	<?php endif; ?>

	<h3 class="es-card__title"><?php echo esc_html( $es_title ); ?></h3>

	<?php if ( $es_desc ) : ?>
		<p class="es-card__text"><?php echo esc_html( $es_desc ); ?></p>
	<?php endif; ?>

	<?php if ( $es_image ) : ?>
		<span class="es-card__thumb">
			<?php echo wp_get_attachment_image( $es_image, 'es-card', false, array( 'class' => 'es-card__image', 'loading' => 'lazy' ) ); ?>
		</span>
	<?php endif; ?>
</li>
