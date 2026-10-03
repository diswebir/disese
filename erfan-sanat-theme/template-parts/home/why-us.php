<?php
/**
 * Homepage: why us cards + production process steps.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_cards = es_opt( 'home_why_items', array() );
$es_steps = es_opt( 'home_process_steps', array() );
$es_image = (int) es_opt( 'home_process_image', 0 );

if ( ! es_opt( 'home_why_enable', true ) && ! $es_steps ) {
	return;
}
?>
<section class="es-section es-section--why" id="why-us">
	<div class="es-container">

		<?php if ( es_opt( 'home_why_enable', true ) ) : ?>
			<?php
			es_section_header(
				array(
					'eyebrow' => (string) es_opt( 'home_why_eyebrow', '' ),
					'title'   => (string) es_opt( 'home_why_title', '' ),
					'text'    => (string) es_opt( 'home_why_text', '' ),
					'align'   => 'center',
				)
			);
			?>

			<?php if ( is_array( $es_cards ) && $es_cards ) : ?>
				<ul class="es-grid es-grid--why es-grid--cols-<?php echo esc_attr( (string) min( 5, max( 2, count( $es_cards ) ) ) ); ?>">
					<?php foreach ( $es_cards as $es_card ) : ?>
						<?php
						if ( empty( $es_card['title'] ) ) {
							continue;
						}

						get_template_part(
							'template-parts/cards/feature-card',
							null,
							array(
								'icon'  => isset( $es_card['icon'] ) ? $es_card['icon'] : 'certified',
								'title' => $es_card['title'],
								'desc'  => isset( $es_card['desc'] ) ? $es_card['desc'] : '',
							)
						);
						?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		<?php endif; ?>
	</div>

	<?php if ( es_opt( 'home_process_enable', true ) && is_array( $es_steps ) && $es_steps ) : ?>
		<div class="es-process">
			<div class="es-process__media">
				<?php if ( $es_image ) : ?>
					<?php
					echo wp_get_attachment_image(
						$es_image,
						'es-hero',
						false,
						array( 'class' => 'es-process__image', 'loading' => 'lazy', 'alt' => '' )
					);
					?>
				<?php else : ?>
					<img class="es-process__image" src="<?php echo esc_url( ES_THEME_URI . 'assets/images/process-tunnel.svg' ); ?>" alt="" width="1600" height="900" loading="lazy">
				<?php endif; ?>
				<span class="es-process__overlay" aria-hidden="true"></span>
			</div>

			<div class="es-container es-process__inner">
				<h2 class="es-process__title"><?php echo esc_html( (string) es_opt( 'home_process_title', __( 'فرایند اجرای پروژه', 'erfan-sanat' ) ) ); ?></h2>

				<ol class="es-process__steps">
					<?php
					$es_i = 0;

					foreach ( $es_steps as $es_step ) :
						if ( empty( $es_step['title'] ) ) {
							continue;
						}

						$es_i++;
						?>
						<li class="es-process__step">
							<span class="es-process__step-index" aria-hidden="true"><?php echo esc_html( es_index_label( $es_i ) ); ?></span>
							<h3 class="es-process__step-title"><?php echo esc_html( $es_step['title'] ); ?></h3>
							<?php if ( ! empty( $es_step['desc'] ) ) : ?>
								<p class="es-process__step-text"><?php echo esc_html( $es_step['desc'] ); ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</div>
	<?php endif; ?>
</section>
