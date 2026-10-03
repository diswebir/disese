<?php
/**
 * Previous/next navigation between posts of the same type.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_type = get_post_type();

$es_prev = get_previous_post();
$es_next = get_next_post();

// Projects navigate inside their own post type, articles inside posts.
if ( 'project' === $es_type ) {
	$es_prev = get_previous_post( true, '', 'project_cat' );
	$es_next = get_next_post( true, '', 'project_cat' );
}

if ( ! $es_prev && ! $es_next ) {
	return;
}
?>
<nav class="es-post-nav" aria-label="<?php esc_attr_e( 'پیمایش بین مطالب', 'erfan-sanat' ); ?>">
	<?php if ( $es_prev ) : ?>
		<a class="es-post-nav__item es-post-nav__item--prev" href="<?php echo esc_url( (string) get_permalink( $es_prev ) ); ?>">
			<span class="es-post-nav__label"><?php esc_html_e( 'مطلب قبلی', 'erfan-sanat' ); ?></span>
			<span class="es-post-nav__title"><?php echo esc_html( get_the_title( $es_prev ) ); ?></span>
		</a>
	<?php endif; ?>

	<?php if ( $es_next ) : ?>
		<a class="es-post-nav__item es-post-nav__item--next" href="<?php echo esc_url( (string) get_permalink( $es_next ) ); ?>">
			<span class="es-post-nav__label"><?php esc_html_e( 'مطلب بعدی', 'erfan-sanat' ); ?></span>
			<span class="es-post-nav__title"><?php echo esc_html( get_the_title( $es_next ) ); ?></span>
		</a>
	<?php endif; ?>
</nav>
