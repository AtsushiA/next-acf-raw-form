<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$field_name = isset( $attributes['fieldName'] ) ? $attributes['fieldName'] : 'application_form';

if ( ! function_exists( 'get_field' ) ) {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<div ' . get_block_wrapper_attributes() . '>';
		echo '<p style="color: #d63638; padding: 10px; background: #fcf0f1; border: 1px solid #d63638;">';
		esc_html_e( 'ACF Raw Form Output Block: Advanced Custom Fields plugin is not active.', 'next-acf-raw-form' );
		echo '</p>';
		echo '</div>';
	}
	return;
}

$field_value = get_field( $field_name, false, false );

if ( empty( $field_value ) ) {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<div ' . get_block_wrapper_attributes() . '>';
		echo '<p style="color: #996800; padding: 10px; background: #fcf9e8; border: 1px solid #996800;">';
		printf(
			/* translators: %s: ACF field name */
			esc_html__( 'ACF Raw Form Output Block: The field "%s" is empty or does not exist.', 'next-acf-raw-form' ),
			esc_html( $field_name )
		);
		echo '</p>';
		echo '</div>';
	}
	return;
}

// Strip any <p> tags that may wrap <script>/<iframe> elements.
// These can appear when the ACF field type is WYSIWYG (TinyMCE applies wpautop on save).
$field_value = preg_replace( '/<p>\s*(<(?:script|iframe|noscript)[^>]*>)/si', '$1', $field_value );
$field_value = preg_replace( '/(<\/(?:script|iframe|noscript)>)\s*<\/p>/si', '$1', $field_value );
$field_value = preg_replace( '/<p>\s*<\/p>/si', '', $field_value );

$wrapper_attributes = get_block_wrapper_attributes();

echo '<div ' . $wrapper_attributes . '>';
// Output raw HTML without escaping or formatting - intentional for form/script embeds.
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $field_value;
echo '</div>';
