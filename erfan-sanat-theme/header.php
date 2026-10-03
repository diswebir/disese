<?php
/**
 * Site header: topbar, masthead (brand + navigation + actions), mobile drawer.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_header_style = (string) es_opt( 'header_style', 'dark' );
$es_transparent  = 'transparent' === $es_header_style && is_front_page();
$es_sticky       = (bool) es_opt( 'header_sticky', true );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="<?php echo esc_attr( es_theme_direction() ); ?>" class="es-html">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/header/skip-link' ); ?>

<div id="page" class="es-site">

	<?php get_template_part( 'template-parts/header/topbar' ); ?>

	<header
		id="masthead"
		class="es-header<?php echo $es_transparent ? ' es-header--transparent' : ''; ?>"
		data-es-header
		<?php echo $es_sticky ? ' data-es-sticky' : ''; ?>
	>
		<div class="es-container es-header__inner">
			<?php
			get_template_part( 'template-parts/header/brand' );
			get_template_part( 'template-parts/header/nav' );
			get_template_part( 'template-parts/header/actions' );
			?>
		</div>
	</header>

	<?php get_template_part( 'template-parts/header/mobile-menu' ); ?>

	<?php if ( es_opt( 'enable_scroll_progress', true ) && is_singular() ) : ?>
		<div class="es-progress" data-es-progress aria-hidden="true"><span class="es-progress__bar" data-es-progress-bar></span></div>
	<?php endif; ?>

	<div class="es-content" id="es-content">
		<?php if ( es_opt( 'enable_breadcrumbs', true ) && ! is_front_page() ) : ?>
			<div class="es-container">
				<?php get_template_part( 'template-parts/global/breadcrumbs' ); ?>
			</div>
		<?php endif; ?>
