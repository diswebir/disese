<?php
/**
 * Table of contents built from the headings of the current article.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_headings = es_collect_headings( get_the_content() );

if ( count( $es_headings ) < 2 ) {
	return;
}
?>
<nav class="es-toc" aria-labelledby="es-toc-title" data-es-toc>
	<h2 class="es-toc__title" id="es-toc-title"><?php esc_html_e( 'فهرست مطالب', 'erfan-sanat' ); ?></h2>

	<ol class="es-toc__list">
		<?php foreach ( $es_headings as $es_heading ) : ?>
			<li class="es-toc__item es-toc__item--<?php echo esc_attr( $es_heading['level'] ); ?>">
				<a href="#<?php echo esc_attr( $es_heading['id'] ); ?>"><?php echo esc_html( $es_heading['text'] ); ?></a>
			</li>
		<?php endforeach; ?>
	</ol>
</nav>
