<?php
/**
 * Empty state for archives, search and filtered loops.
 *
 * @param array $args title, text.
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

es_empty_state(
	array(
		'title'       => isset( $args['title'] ) ? (string) $args['title'] : __( 'موردی یافت نشد.', 'erfan-sanat' ),
		'text'        => isset( $args['text'] ) ? (string) $args['text'] : __( 'عبارت دیگری را جست‌وجو کنید یا از دسته‌بندی‌ها استفاده کنید.', 'erfan-sanat' ),
		'icon'        => 'search',
		'button_text' => __( 'بازگشت به صفحهٔ اصلی', 'erfan-sanat' ),
		'button_url'  => home_url( '/' ),
	)
);
?>
<div class="es-archive__search"><?php get_search_form(); ?></div>
