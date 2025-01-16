<?php
/**
 * Plugin Name: Beaver Builder Colour Picker Presets Override
 * Plugin URI:  https://github.com/weavedigital/bb-color-picker-override
 * Description: Simple single use plugin which uses js to override Beaver Builder's colour picker behaviour making your colour presets visible first by default instead of just the picker, streamlining your development workflow.
 * Version:     1.0.1
 * Author:     Weave Digital Studio, Gareth Bissland
 * Author URI:  https://weave.co.nz
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: bb-color-picker-presets
 * GitHub Plugin URI: weavedigital/bb-color-picker-override
 * Primary Branch:    main
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the custom script to modify Beaver Builder's colour picker behaviour.
 */
function bb_color_picker_override_enqueue_script() {
	wp_enqueue_script(
		'bb-color-picker-override', 
		plugin_dir_url( __FILE__ ) . 'js/override-color-picker.js',
		['jquery'], 
		null,
		true 
	);
}
add_action( 'wp_enqueue_scripts', 'bb_color_picker_override_enqueue_script' );