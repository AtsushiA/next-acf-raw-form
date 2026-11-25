<?php
/**
 * Plugin Name:       NExT ACF Raw Form Output Block
 * Description:       A block that outputs raw ACF field content without escaping, perfect for embedding form codes
 * Version:           0.4.0
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Author:            NExT-Season with WordPress Telex
 * Author URI:        https://next-season.net
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       next-acf-raw-form
 *
 * @package NextAcfRawForm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function next_acf_raw_form_block_init() {
	register_block_type( __DIR__ . '/build/' );
}
add_action( 'init', 'next_acf_raw_form_block_init' );

/**
 * Check if ACF is active and show admin notice if not
 */
function next_acf_raw_form_check_dependencies() {
	if ( ! function_exists( 'get_field' ) ) {
		add_action( 'admin_notices', 'next_acf_raw_form_acf_missing_notice' );
	}
}
add_action( 'plugins_loaded', 'next_acf_raw_form_check_dependencies' );

/**
 * Prevent wpautop from wrapping <script> tags inside the block output.
 *
 * The wpautop filter runs at the_content priority 10, after do_blocks at priority 9.
 * It treats <script> as an inline element and inserts <p> tags around it,
 * breaking embedded form codes. We use a placeholder strategy:
 * 1. Replace the block output with a <div> placeholder before wpautop runs (priority 10).
 * 2. Restore the original block output after wpautop runs (priority 11).
 *
 * NOTE: This strategy only applies when the block renders inside the_content
 * (i.e. post content context). Blocks placed directly in block templates do not
 * go through wpautop, so no placeholder is needed there — and applying one
 * would cause the content to disappear because the restore hooks never fire.
 */
global $next_acf_raw_form_placeholder_blocks;
$next_acf_raw_form_placeholder_blocks = array();

add_filter( 'render_block_next/block-next-acf-raw-form', 'next_acf_raw_form_set_placeholder', 10 );
// Also handle the legacy block name for backward compatibility with existing posts.
add_filter( 'render_block_telex/block-telex-acf-raw-form', 'next_acf_raw_form_set_placeholder', 10 );

/**
 * Replaces the block output with a placeholder while the_content is running.
 *
 * @param string $block_content Rendered block content.
 * @return string Placeholder markup, or the original content outside the_content.
 */
function next_acf_raw_form_set_placeholder( $block_content ) {
	// Only apply the placeholder strategy while the_content filter is executing.
	// Blocks rendered directly in block templates are not processed by wpautop,
	// so the placeholder is unnecessary and would permanently hide the content.
	if ( ! doing_filter( 'the_content' ) ) {
		return $block_content;
	}
	global $next_acf_raw_form_placeholder_blocks;
	$key = 'next-acf-raw-form-ph-' . count( $next_acf_raw_form_placeholder_blocks );
	$next_acf_raw_form_placeholder_blocks[ $key ] = $block_content;
	return '<div class="' . esc_attr( $key ) . '"></div>';
}

add_filter( 'the_content', 'next_acf_raw_form_restore_placeholder', 11 );
// Fallback: restore placeholders after core/post-content block renders.
// This handles block themes where the_content may not fire in the expected context.
add_filter( 'render_block_core/post-content', 'next_acf_raw_form_restore_placeholder', 99 );

/**
 * Restores the original block output in place of the placeholders.
 *
 * @param string $content Post content containing placeholders.
 * @return string Content with the original block output restored.
 */
function next_acf_raw_form_restore_placeholder( $content ) {
	global $next_acf_raw_form_placeholder_blocks;
	if ( empty( $next_acf_raw_form_placeholder_blocks ) ) {
		return $content;
	}
	foreach ( $next_acf_raw_form_placeholder_blocks as $key => $block_content ) {
		$content = str_replace( '<div class="' . esc_attr( $key ) . '"></div>', $block_content, $content );
	}
	$next_acf_raw_form_placeholder_blocks = array();
	return $content;
}

/**
 * Display admin notice when ACF is not active
 */
function next_acf_raw_form_acf_missing_notice() {
	?>
	<div class="notice notice-warning">
		<p>
			<?php
			esc_html_e(
				'ACF Raw Form Output Block requires Advanced Custom Fields plugin to be installed and activated.',
				'next-acf-raw-form'
			);
			?>
		</p>
	</div>
	<?php
}
