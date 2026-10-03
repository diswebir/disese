<?php
/**
 * Comments template.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="es-comments">

	<?php if ( have_comments() ) : ?>
		<h2 class="es-comments__title">
			<?php
			$es_count = get_comments_number();

			printf(
				/* translators: %s: comment count */
				esc_html( _n( '%s دیدگاه', '%s دیدگاه', $es_count, 'erfan-sanat' ) ),
				esc_html( es_num( number_format_i18n( $es_count ) ) )
			);
			?>
		</h2>

		<ol class="es-comments__list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 56,
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => __( 'دیدگاه‌های قبلی', 'erfan-sanat' ),
				'next_text' => __( 'دیدگاه‌های بعدی', 'erfan-sanat' ),
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="es-comments__closed"><?php esc_html_e( 'دیدگاه‌ها برای این مطلب بسته شده است.', 'erfan-sanat' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'          => __( 'دیدگاه شما', 'erfan-sanat' ),
			'title_reply_before'   => '<h2 id="reply-title" class="es-comments__form-title">',
			'title_reply_after'    => '</h2>',
			'comment_notes_before' => '<p class="es-comments__notes">' . esc_html__( 'نشانی ایمیل شما منتشر نخواهد شد.', 'erfan-sanat' ) . '</p>',
			'label_submit'         => __( 'ارسال دیدگاه', 'erfan-sanat' ),
			'class_submit'         => 'es-btn es-btn--primary',
			'class_form'           => 'es-form es-comment-form',
		)
	);
	?>
</section>
