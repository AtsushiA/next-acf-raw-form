
=== ACF Raw Form Output Block ===

Contributors:      WordPress Telex
Tags:              block, acf, advanced custom fields, form, embed
Tested up to:      6.8
Stable tag:        0.4.0
License:           GPLv2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

A WordPress block that outputs raw ACF field content without escaping or styling, perfect for embedding form codes.

== Description ==

This block allows you to display content from Advanced Custom Fields (ACF) text fields without any HTML escaping or additional styling. This is particularly useful when you need to embed form codes or other HTML content that should be rendered exactly as stored in the ACF field.

**Features:**

* Output raw HTML content from ACF fields without escaping
* Configurable ACF field name through the block inspector
* Works with any ACF text field
* Perfect for embedding forms, iframes, and other HTML snippets
* Dynamic rendering ensures content is always up-to-date

**Usage:**

1. Create a text field in ACF (e.g., "application_form")
2. Add the "ACF Raw Form Output" block to your post or page
3. In the block settings, enter the ACF field name
4. The block will automatically display the raw content from that field

**Requirements:**

* Advanced Custom Fields (ACF) plugin must be installed and activated
* ACF field must be available in the current post context

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/next-acf-raw-form` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Make sure you have Advanced Custom Fields plugin installed and activated
4. Add the block to any post or page and configure the ACF field name in the block settings

== Frequently Asked Questions ==

= Do I need ACF plugin for this to work? =

Yes, this block requires the Advanced Custom Fields plugin to be installed and activated.

= What type of ACF fields does this support? =

This block works with ACF text fields. It's designed to output the raw content without any escaping or formatting.

= Is it safe to output raw HTML? =

This block intentionally outputs unescaped HTML, so only use it with trusted content. Make sure you control what content is stored in your ACF fields.

= Can I use this with repeater fields or other complex ACF fields? =

This block is designed for simple text fields. For complex field types, you may need custom development.

== Screenshots ==

1. Block settings in the editor showing the ACF field name input
2. Example of a form being rendered from an ACF field

== Changelog ==

= 0.4.0 =
* Fix: Placeholder strategy now only applies inside the_content context; blocks in block templates now render correctly without content disappearing

= 0.3.0 =
* Fix: Update CSS class name from telex to next prefix
* Fix: Add render_block_core/post-content fallback for placeholder restoration in block themes

= 0.2.0 =
* Fix: Prevent wpautop from inserting <p> tags around <script>/<iframe> tags in block output
* Fix: Renamed plugin from telex-acf-raw-form to next-acf-raw-form
* Backward compatibility: Legacy telex/block-telex-acf-raw-form block name still works

= 0.1.0 =
* Initial release
* Support for raw HTML output from ACF text fields
* Configurable field name through block inspector

