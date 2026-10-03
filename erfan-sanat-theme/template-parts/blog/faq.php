<?php
/**
 * FAQ block (also emitted as FAQPage structured data by inc/seo.php).
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_faqs = es_get_faq_rows( get_the_ID() );

if ( ! $es_faqs ) {
	return;
}
?>
<section class="es-faq" aria-labelledby="es-faq-title">
	<h2 class="es-faq__title" id="es-faq-title"><?php esc_html_e( 'پرسش‌های متداول', 'erfan-sanat' ); ?></h2>

	<div class="es-faq__list">
		<?php foreach ( $es_faqs as $es_index => $es_faq ) : ?>
			<details class="es-faq__item" <?php echo 0 === $es_index ? 'open' : ''; ?>>
				<summary class="es-faq__question">
					<span><?php echo esc_html( $es_faq['question'] ); ?></span>
					<?php es_icon( 'chevron', 'es-icon', 18 ); ?>
				</summary>
				<div class="es-faq__answer"><?php echo wp_kses_post( wpautop( $es_faq['answer'] ) ); ?></div>
			</details>
		<?php endforeach; ?>
	</div>
</section>
