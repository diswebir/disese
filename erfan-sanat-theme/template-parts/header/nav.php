<?php
/**
 * Primary navigation.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<nav id="site-navigation" class="es-nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'erfan-sanat' ); ?>">
	<?php
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'menu_id'        => 'primary-menu',
				'menu_class'     => 'es-nav__list',
				'container'      => false,
				'depth'          => 3,
			)
		);
	} else {
		es_nav_menu_fallback(
			array(
				'menu_class' => 'es-nav__list',
			)
		);
	}
	?>
</nav>
