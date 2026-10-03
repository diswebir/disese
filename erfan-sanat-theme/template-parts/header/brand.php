<?php
/**
 * Brand block: custom logo, or a self-contained inline mark built from the
 * brand options (so the theme never depends on an uploaded asset).
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_logo_id = (int) es_opt( 'logo_main', 0 );
$es_home    = home_url( '/' );

// The real site logo, when the administrator uploaded one.
if ( has_custom_logo() ) {
	echo '<div class="es-brand es-brand--logo">';
	the_custom_logo();
	echo '</div>';

	return;
}
?>
<a class="es-brand" href="<?php echo esc_url( $es_home ); ?>" rel="home">
	<span class="es-brand__mark" aria-hidden="true">
		<?php
		if ( $es_logo_id ) {
			echo wp_get_attachment_image( $es_logo_id, 'es-thumb', false, array( 'class' => 'es-brand__image', 'alt' => '' ) );
		} else {
			echo es_get_icon( 'spark', 'es-brand__icon', 30 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
	</span>

	<span class="es-brand__text">
		<span class="es-brand__name"><?php echo esc_html( (string) es_opt( 'brand_name_fa', get_bloginfo( 'name' ) ) ); ?></span>
		<span class="es-brand__tagline">
			<?php echo esc_html( (string) es_opt( 'site_tagline_fa', get_bloginfo( 'description' ) ) ); ?>
		</span>
	</span>
</a>
