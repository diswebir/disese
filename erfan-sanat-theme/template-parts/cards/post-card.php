<?php
/**
 * Article card.
 *
 * @param array $args show_excerpt, length.
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_show_excerpt = isset( $args['show_excerpt'] ) ? (bool) $args['show_excerpt'] : true;
$es_length       = isset( $args['length'] ) ? (int) $args['length'] : (int) es_opt( 'blog_excerpt_length', 24 );
$es_cats         = get_the_category();
?>
<article <?php post_class( 'es-card es-card--post' ); ?>>

	<a class="es-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php es_post_thumbnail( get_the_ID(), 'es-card-wide', 'es-card__image' ); ?>
	</a>

	<div class="es-card__body">
		<?php if ( $es_cats ) : ?>
			<p class="es-card__eyebrow">
				<?php es_icon( 'folder', 'es-icon', 14 ); ?>
				<span><?php echo esc_html( $es_cats[0]->name ); ?></span>
			</p>
		<?php endif; ?>

		<h3 class="es-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<?php if ( $es_show_excerpt && get_the_excerpt() ) : ?>
			<p class="es-card__text"><?php echo esc_html( wp_trim_words( get_the_excerpt(), max( 8, $es_length ), '…' ) ); ?></p>
		<?php endif; ?>

		<ul class="es-card__meta">
			<li>
				<?php es_icon( 'calendar', 'es-icon', 15 ); ?>
				<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( es_num( get_the_date() ) ); ?></time>
			</li>

			<?php if ( es_opt( 'blog_show_reading_time', true ) ) : ?>
				<li>
					<?php es_icon( 'clock', 'es-icon', 15 ); ?>
					<span>
						<?php
						printf(
							/* translators: %s: minutes */
							esc_html__( '%s دقیقه مطالعه', 'erfan-sanat' ),
							esc_html( es_num( (string) es_reading_time( get_the_ID() ) ) )
						);
						?>
					</span>
				</li>
			<?php endif; ?>
		</ul>

		<span class="es-card__link">
			<?php esc_html_e( 'ادامهٔ مطلب', 'erfan-sanat' ); ?>
			<?php es_icon( 'arrow', 'es-icon', 16 ); ?>
		</span>
	</div>
</article>
