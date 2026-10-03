<?php
/**
 * Project card.
 *
 * @param array $args index (1-based), featured, show_terms.
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_index      = isset( $args['index'] ) ? (int) $args['index'] : 0;
$es_show_terms = isset( $args['show_terms'] ) ? (bool) $args['show_terms'] : true;
$es_pixels     = (int) es_meta( '_es_total_pixel_count', get_the_ID(), 0 );
$es_power      = (float) es_meta( '_es_total_power_kw', get_the_ID(), 0 );
$es_client     = (string) es_meta( '_es_project_client', get_the_ID(), '' );
$es_places     = get_the_terms( get_the_ID(), 'project_location' );
?>
<article <?php post_class( 'es-card es-card--project' ); ?>>

	<a class="es-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php es_post_thumbnail( get_the_ID(), 'es-card-wide', 'es-card__image' ); ?>

		<?php if ( $es_index ) : ?>
			<span class="es-card__index"><?php echo esc_html( es_index_label( $es_index ) ); ?></span>
		<?php endif; ?>

		<span class="es-card__glow" aria-hidden="true"></span>
	</a>

	<div class="es-card__body">
		<?php if ( $es_places && ! is_wp_error( $es_places ) ) : ?>
			<p class="es-card__eyebrow">
				<?php es_icon( 'pin', 'es-icon', 14 ); ?>
				<span><?php echo esc_html( implode( '، ', wp_list_pluck( $es_places, 'name' ) ) ); ?></span>
			</p>
		<?php endif; ?>

		<h3 class="es-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<?php if ( get_the_excerpt() ) : ?>
			<p class="es-card__text"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, '…' ) ); ?></p>
		<?php endif; ?>

		<?php
		$es_stats = array();

		if ( $es_pixels ) {
			$es_stats[] = sprintf(
				/* translators: %s: pixel count */
				__( '%s پیکسل', 'erfan-sanat' ),
				es_num( number_format_i18n( $es_pixels ) )
			);
		}

		if ( $es_power ) {
			$es_stats[] = sprintf(
				/* translators: %s: kilowatts */
				__( '%s کیلووات', 'erfan-sanat' ),
				es_num( number_format_i18n( $es_power, 1 ) )
			);
		}

		if ( $es_client ) {
			$es_stats[] = $es_client;
		}

		if ( $es_stats ) :
			?>
			<ul class="es-card__stats">
				<?php foreach ( $es_stats as $es_stat ) : ?>
					<li><?php echo esc_html( $es_stat ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( $es_show_terms ) : ?>
			<?php es_term_pills( get_the_ID(), 'project_cat', 2 ); ?>
		<?php endif; ?>

		<span class="es-card__link">
			<?php esc_html_e( 'مشاهدهٔ پروژه', 'erfan-sanat' ); ?>
			<?php es_icon( 'arrow', 'es-icon', 16 ); ?>
		</span>
	</div>
</article>
