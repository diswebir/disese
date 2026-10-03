<?php
/**
 * Front page.
 *
 * Renders the modular homepage: every section is switchable from the theme
 * options and the dynamic ones (projects, products, articles) are fed by the
 * real content in the database.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_home_sections = array(
	'hero',
	'ticker',
	'projects',
	'about',
	'products',
	'custom-build',
	'services',
	'why-us',
	'blog',
	'cta',
);

/**
 * Filters the homepage section order.
 *
 * @param string[] $es_home_sections Template part names inside template-parts/home/.
 */
$es_home_sections = apply_filters( 'es_home_sections', $es_home_sections );
?>
<main id="main" class="es-main es-main--home" role="main">
	<?php
	foreach ( $es_home_sections as $es_section ) {
		get_template_part( 'template-parts/home/' . sanitize_file_name( $es_section ) );
	}
	?>
</main>
<?php
get_footer();
