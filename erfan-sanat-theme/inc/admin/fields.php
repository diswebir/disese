<?php
/**
 * Generic, schema driven field renderer.
 *
 * One renderer paints every control used by the theme: the settings screen,
 * the section repeaters and the post/page/product meta boxes. Field markup,
 * escaping and the data attributes used by assets/js/admin.js exist here only.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Normalise a field definition with defaults.
 *
 * @param array $field Field definition.
 * @return array
 */
function es_prepare_field( array $field ) {
	return wp_parse_args(
		$field,
		array(
			'key'      => '',
			'type'     => 'text',
			'label'    => '',
			'desc'     => '',
			'default'  => '',
			'choices'  => array(),
			'fields'   => array(),
			'min'      => null,
			'max'      => null,
			'step'     => null,
			'sanitize' => '',
			'multiple' => false,
			'rows'     => 5,
		)
	);
}

/**
 * Render a single control.
 *
 * @param array  $field Field definition.
 * @param mixed  $value Current value.
 * @param array  $args  name, id, class, context (options|meta).
 * @return void
 */
function es_field_control( array $field, $value, array $args = array() ) {
	$field = es_prepare_field( $field );

	$name    = isset( $args['name'] ) ? (string) $args['name'] : $field['key'];
	$id      = isset( $args['id'] ) ? (string) $args['id'] : 'es-field-' . $field['key'];
	$context = isset( $args['context'] ) ? (string) $args['context'] : 'options';
	$extra   = isset( $args['class'] ) ? (string) $args['class'] : '';

	$attributes = array(
		'name'      => $name,
		'id'        => $id,
		'class'     => 'es-control es-control--' . $field['type'] . ( $extra ? ' ' . $extra : '' ),
		'data-type' => $field['type'],
	);

	if ( null !== $field['min'] ) {
		$attributes['min'] = $field['min'];
	}

	if ( null !== $field['max'] ) {
		$attributes['max'] = $field['max'];
	}

	$attr_string = '';

	foreach ( $attributes as $attr_key => $attr_value ) {
		$attr_string .= ' ' . $attr_key . '="' . esc_attr( (string) $attr_value ) . '"';
	}

	switch ( $field['type'] ) {
		case 'textarea':
			printf(
				'<textarea%1$s rows="%2$s" placeholder="%3$s">%4$s</textarea>',
				$attr_string, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_attr().
				esc_attr( (string) $field['rows'] ),
				esc_attr( (string) $field['desc'] ),
				esc_textarea( is_scalar( $value ) ? (string) $value : '' )
			);
			break;

		case 'editor':
			wp_editor(
				is_scalar( $value ) ? (string) $value : '',
				$id,
				array(
					'textarea_name' => $name,
					'textarea_rows' => max( 4, (int) $field['rows'] ),
					'media_buttons' => false,
					'teeny'         => false,
					'quicktags'     => true,
					'editor_class'  => 'es-control es-control--editor',
				)
			);
			break;

		case 'number':
		case 'range':
			printf(
				'<input type="%1$s" value="%2$s"%3$s%4$s>',
				'range' === $field['type'] ? 'range' : 'number',
				esc_attr( (string) ( is_scalar( $value ) ? $value : '' ) ),
				$attr_string, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				null !== $field['step'] ? ' step="' . esc_attr( (string) $field['step'] ) . '"' : ''
			);

			if ( 'range' === $field['type'] ) {
				printf( '<output class="es-range__output" for="%s">%s</output>', esc_attr( $id ), esc_html( (string) $value ) );
			}
			break;

		case 'toggle':
			printf(
				'<label class="es-switch"><input type="hidden" name="%1$s" value="0"><input type="checkbox" value="1"%2$s%3$s><span class="es-switch__track" aria-hidden="true"></span><span class="es-switch__label">%4$s</span></label>',
				esc_attr( $name ),
				checked( (bool) $value, true, false ),
				$attr_string, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				esc_html( $field['label'] )
			);
			break;

		case 'select':
			printf( '<select%1$s>', $attr_string ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( '' === $field['label'] || ! empty( $args['allow_empty'] ) ) {
				printf( '<option value="">%s</option>', esc_html__( '— انتخاب کنید —', 'erfan-sanat' ) );
			}

			foreach ( (array) $field['choices'] as $choice_value => $choice_label ) {
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( (string) $choice_value ),
					selected( (string) $value, (string) $choice_value, false ),
					esc_html( (string) $choice_label )
				);
			}

			echo '</select>';
			break;

		case 'multiselect':
			$current = is_array( $value ) ? array_map( 'strval', $value ) : array();
			printf( '<select multiple size="6"%1$s>', $attr_string ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			foreach ( (array) $field['choices'] as $choice_value => $choice_label ) {
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( (string) $choice_value ),
					in_array( (string) $choice_value, $current, true ) ? ' selected' : '',
					esc_html( (string) $choice_label )
				);
			}

			echo '</select>';
			printf( '<p class="es-control__hint">%s</p>', esc_html__( 'برای انتخاب چند مورد، کلید Ctrl (یا Cmd) را نگه دارید.', 'erfan-sanat' ) );
			break;

		case 'color':
			printf(
				'<input type="text" class="es-color-field" value="%1$s" data-default-color="%2$s"%3$s>',
				esc_attr( is_scalar( $value ) ? (string) $value : '' ),
				esc_attr( (string) $field['default'] ),
				$attr_string // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
			break;

		case 'icon':
			$choices = $field['choices'] ? (array) $field['choices'] : es_icon_choices();
			printf( '<div class="es-icon-picker" data-es-icon-picker>' );
			printf( '<input type="hidden" value="%1$s"%2$s>', esc_attr( (string) $value ), $attr_string ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			foreach ( $choices as $icon_key => $icon_label ) {
				printf(
					'<button type="button" class="es-icon-picker__item%1$s" data-icon="%2$s" title="%3$s" aria-pressed="%4$s">%5$s<span class="screen-reader-text">%3$s</span></button>',
					(string) $value === (string) $icon_key ? ' is-active' : '',
					esc_attr( (string) $icon_key ),
					esc_attr( (string) $icon_label ),
					(string) $value === (string) $icon_key ? 'true' : 'false',
					es_get_icon( (string) $icon_key, 'es-icon', 20 )
				);
			}

			echo '</div>';
			break;

		case 'font':
			$choices = isset( $field['choices'] ) ? (array) $field['choices'] : es_font_choices();
			printf( '<select%1$s>', $attr_string ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			foreach ( $choices as $font_key => $font_data ) {
				$label = is_array( $font_data ) && isset( $font_data['label'] ) ? $font_data['label'] : (string) $font_data;
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( (string) $font_key ),
					selected( (string) $value, (string) $font_key, false ),
					esc_html( $label )
				);
			}

			echo '</select>';
			break;

		case 'image':
			es_field_media( $field, (int) $value, $args );
			break;

		case 'gallery':
			es_field_gallery( $field, $value, $args );
			break;

		case 'repeater':
			es_field_repeater( $field, $value, $args );
			break;

		case 'url':
		case 'email':
		case 'text':
		case 'hidden':
		default:
			printf(
				'<input type="%1$s" value="%2$s"%3$s%4$s>',
				esc_attr( in_array( $field['type'], array( 'url', 'email', 'hidden' ), true ) ? $field['type'] : 'text' ),
				esc_attr( is_scalar( $value ) ? (string) $value : '' ),
				$attr_string, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'inline' === $context ? ' readonly' : ''
			);
			break;
	}
}

/**
 * Media (single attachment) control.
 *
 * @param array $field Field definition.
 * @param int   $value Attachment id.
 * @param array $args  Args.
 * @return void
 */
function es_field_media( array $field, $value, array $args = array() ) {
	$field    = es_prepare_field( $field );
	$name     = isset( $args['name'] ) ? (string) $args['name'] : $field['key'];
	$id       = isset( $args['id'] ) ? (string) $args['id'] : 'es-field-' . $field['key'];
	$preview  = $value ? wp_get_attachment_image_url( $value, 'medium' ) : '';
	$filename = $value ? basename( (string) get_attached_file( $value ) ) : '';
	?>
	<div class="es-media" data-es-media>
		<input type="hidden" class="es-media__input" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" data-es-media-input>

		<div class="es-media__preview<?php echo $preview ? ' has-image' : ''; ?>" data-es-media-preview>
			<?php if ( $preview ) : ?>
				<img src="<?php echo esc_url( $preview ); ?>" alt="">
			<?php else : ?>
				<span class="es-media__placeholder"><?php es_icon( 'image', 'es-icon', 28 ); ?></span>
			<?php endif; ?>
		</div>

		<p class="es-media__actions">
			<button type="button" class="button es-media__select" data-es-media-select><?php esc_html_e( 'انتخاب تصویر', 'erfan-sanat' ); ?></button>
			<button type="button" class="button-link es-media__remove" data-es-media-remove <?php echo $value ? '' : 'hidden'; ?>><?php esc_html_e( 'حذف', 'erfan-sanat' ); ?></button>
			<span class="es-media__name" data-es-media-name><?php echo esc_html( $filename ); ?></span>
		</p>
	</div>
	<?php
}

/**
 * Gallery (multiple attachments) control.
 *
 * @param array $field Field definition.
 * @param mixed $value Comma separated ids or array of ids.
 * @param array $args  Args.
 * @return void
 */
function es_field_gallery( array $field, $value, array $args = array() ) {
	$field = es_prepare_field( $field );
	$name  = isset( $args['name'] ) ? (string) $args['name'] : $field['key'];
	$id    = isset( $args['id'] ) ? (string) $args['id'] : 'es-field-' . $field['key'];

	$ids = array();

	if ( is_array( $value ) ) {
		$ids = array_filter( array_map( 'absint', $value ) );
	} elseif ( is_string( $value ) && '' !== $value ) {
		$ids = array_filter( array_map( 'absint', explode( ',', $value ) ) );
	}
	?>
	<div class="es-gallery" data-es-gallery>
		<input type="hidden" class="es-gallery__input" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>" data-es-gallery-input>

		<ul class="es-gallery__items" data-es-gallery-items>
			<?php foreach ( $ids as $es_attachment_id ) : ?>
				<?php $es_thumb = wp_get_attachment_image_url( $es_attachment_id, 'thumbnail' ); ?>
				<li class="es-gallery__item" data-id="<?php echo esc_attr( (string) $es_attachment_id ); ?>">
					<img src="<?php echo esc_url( (string) $es_thumb ); ?>" alt="">
					<button type="button" class="es-gallery__remove" data-es-gallery-remove aria-label="<?php esc_attr_e( 'حذف تصویر', 'erfan-sanat' ); ?>">&times;</button>
				</li>
			<?php endforeach; ?>
		</ul>

		<p class="es-gallery__actions">
			<button type="button" class="button" data-es-gallery-add><?php esc_html_e( 'افزودن تصویر به گالری', 'erfan-sanat' ); ?></button>
			<span class="es-control__hint"><?php esc_html_e( 'برای ترتیب دلخواه، تصاویر را بکشید و رها کنید.', 'erfan-sanat' ); ?></span>
		</p>
	</div>
	<?php
}

/**
 * Repeater control.
 *
 * @param array $field Field definition (must carry 'fields').
 * @param mixed $value Array of rows.
 * @param array $args  Args.
 * @return void
 */
function es_field_repeater( array $field, $value, array $args = array() ) {
	$field = es_prepare_field( $field );
	$name  = isset( $args['name'] ) ? (string) $args['name'] : $field['key'];
	$id    = isset( $args['id'] ) ? (string) $args['id'] : 'es-field-' . $field['key'];

	$rows = is_array( $value ) ? array_values( $value ) : array();
	?>
	<div class="es-repeater" data-es-repeater data-next-index="<?php echo esc_attr( (string) count( $rows ) ); ?>">
		<ul class="es-repeater__rows" data-es-repeater-rows>
			<?php
			foreach ( $rows as $es_index => $es_row ) {
				if ( ! is_array( $es_row ) ) {
					continue;
				}

				es_repeater_row( $field, $name, $es_index, $es_row );
			}
			?>
		</ul>

		<p class="es-repeater__actions">
			<button type="button" class="button button-secondary" data-es-repeater-add>
				<?php esc_html_e( 'افزودن ردیف', 'erfan-sanat' ); ?>
			</button>
			<span class="es-control__hint"><?php echo esc_html( (string) $field['desc'] ); ?></span>
		</p>

		<script type="text/html" data-es-repeater-template>
			<?php es_repeater_row( $field, $name, '__INDEX__', array(), true ); ?>
		</script>
	</div>
	<?php
}

/**
 * One repeater row.
 *
 * @param array        $field     Repeater field definition.
 * @param string       $name      Base input name (e.g. erfan_sanat_options[home_hero_stats]).
 * @param int|string   $index     Row index or placeholder.
 * @param array        $row       Row values.
 * @param bool         $is_template Whether the row is the JS template.
 * @return void
 */
function es_repeater_row( array $field, $name, $index, array $row = array(), $is_template = false ) {
	$subfields = isset( $field['fields'] ) ? (array) $field['fields'] : array();
	$title     = '';

	foreach ( $subfields as $subfield ) {
		if ( ! empty( $row[ $subfield['key'] ] ) && is_scalar( $row[ $subfield['key'] ] ) ) {
			$title = wp_strip_all_tags( (string) $row[ $subfield['key'] ] );
			break;
		}
	}

	if ( '' === $title ) {
		$title = isset( $field['label'] ) ? (string) $field['label'] : __( 'ردیف جدید', 'erfan-sanat' );
	}
	?>
	<li class="es-repeater__row" data-es-repeater-row>
		<div class="es-repeater__handle" data-es-repeater-handle>
			<span class="es-repeater__drag" aria-hidden="true">
				<span class="dashicons dashicons-menu-alt2"></span>
			</span>
			<span class="es-repeater__title" data-es-repeater-title><?php echo esc_html( $title ); ?></span>

			<span class="es-repeater__tools">
				<button type="button" class="es-repeater__toggle" data-es-repeater-toggle aria-expanded="false">
					<span class="screen-reader-text"><?php esc_html_e( 'نمایش/پنهان کردن ردیف', 'erfan-sanat' ); ?></span>
					<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
				</button>
				<button type="button" class="es-repeater__remove" data-es-repeater-remove aria-label="<?php esc_attr_e( 'حذف ردیف', 'erfan-sanat' ); ?>">
					<span class="dashicons dashicons-trash" aria-hidden="true"></span>
				</button>
			</span>
		</div>

		<div class="es-repeater__fields" <?php echo $is_template || empty( $title ) ? '' : 'hidden'; ?>>
			<?php
			foreach ( $subfields as $subfield ) {
				$sub_key   = $subfield['key'];
				$sub_value = array_key_exists( $sub_key, $row ) ? $row[ $sub_key ] : ( isset( $subfield['default'] ) ? $subfield['default'] : '' );

				es_field_row(
					$subfield,
					$sub_value,
					array(
						'name' => $name . '[' . $index . '][' . $sub_key . ']',
						'id'   => $name . '-' . $index . '-' . $sub_key,
					)
				);
			}
			?>
		</div>
	</li>
	<?php
}

/**
 * Field row: label + control + description.
 *
 * @param array $field Field definition.
 * @param mixed $value Current value.
 * @param array $args  Renderer args.
 * @param bool  $inline Whether the control sits inline with the label.
 * @return void
 */
function es_field_row( array $field, $value, array $args = array(), $inline = false ) {
	$field  = es_prepare_field( $field );
	$id     = isset( $args['id'] ) ? (string) $args['id'] : 'es-field-' . $field['key'];
	$needed = ! empty( $field['required'] );
	?>
	<div class="es-field es-field--<?php echo esc_attr( $field['type'] ); ?>" data-es-field="<?php echo esc_attr( $field['key'] ); ?>">
		<?php if ( 'toggle' !== $field['type'] && 'hidden' !== $field['type'] ) : ?>
			<label class="es-field__label" for="<?php echo esc_attr( $id ); ?>">
				<?php echo esc_html( $field['label'] ); ?>
				<?php if ( $needed ) : ?>
					<span class="es-required" aria-hidden="true">*</span>
				<?php endif; ?>
			</label>
		<?php endif; ?>

		<div class="es-field__control es-field__control--<?php echo esc_attr( $inline ? 'inline' : 'stacked' ); ?>">
			<?php es_field_control( $field, $value, $args ); ?>
		</div>

		<?php if ( ! empty( $field['desc'] ) && 'toggle' !== $field['type'] ) : ?>
			<p class="es-field__desc"><?php echo esc_html( (string) $field['desc'] ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}
