<?php
/**
 * Share links for the current entry (no third-party scripts, plain links).
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

$es_url   = rawurlencode( (string) get_permalink() );
$es_title = rawurlencode( (string) get_the_title() );
?>
<div class="es-share">
	<span class="es-share__label"><?php esc_html_e( 'اشتراک‌گذاری:', 'erfan-sanat' ); ?></span>

	<ul class="es-share__list">
		<li>
			<a href="<?php echo esc_url( 'https://t.me/share/url?url=' . $es_url . '&text=' . $es_title ); ?>" target="_blank" rel="noopener nofollow" aria-label="<?php esc_attr_e( 'اشتراک در تلگرام', 'erfan-sanat' ); ?>">
				<?php es_icon( 'telegram', 'es-icon', 18 ); ?>
			</a>
		</li>
		<li>
			<a href="<?php echo esc_url( 'https://wa.me/?text=' . $es_title . '%20' . $es_url ); ?>" target="_blank" rel="noopener nofollow" aria-label="<?php esc_attr_e( 'اشتراک در واتس‌اپ', 'erfan-sanat' ); ?>">
				<?php es_icon( 'whatsapp', 'es-icon', 18 ); ?>
			</a>
		</li>
		<li>
			<a href="<?php echo esc_url( 'https://www.linkedin.com/sharing/share-offsite/?url=' . $es_url ); ?>" target="_blank" rel="noopener nofollow" aria-label="<?php esc_attr_e( 'اشتراک در لینکدین', 'erfan-sanat' ); ?>">
				<?php es_icon( 'linkedin', 'es-icon', 18 ); ?>
			</a>
		</li>
		<li>
			<button type="button" class="es-share__copy" data-es-copy="<?php echo esc_url( (string) get_permalink() ); ?>">
				<?php es_icon( 'link', 'es-icon', 18 ); ?>
				<span class="screen-reader-text"><?php esc_html_e( 'کپی نشانی صفحه', 'erfan-sanat' ); ?></span>
			</button>
		</li>
	</ul>
</div>
