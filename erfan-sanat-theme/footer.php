<?php
/**
 * Site footer: main columns, bottom bar, floating actions.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
	</div><!-- .es-content -->

	<footer id="colophon" class="es-footer">
		<?php
		get_template_part( 'template-parts/footer/footer-main' );
		get_template_part( 'template-parts/footer/footer-bottom' );
		?>
	</footer>
</div><!-- #page -->

<?php get_template_part( 'template-parts/footer/floating' ); ?>

<?php
if ( es_opt( 'enable_back_to_top', true ) ) :
	?>
	<button type="button" class="es-to-top" data-es-to-top hidden>
		<?php es_icon( 'arrow-up', 'es-icon', 20 ); ?>
		<span class="screen-reader-text"><?php esc_html_e( 'بازگشت به بالای صفحه', 'erfan-sanat' ); ?></span>
	</button>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
