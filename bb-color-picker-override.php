<?php
/**
 * Plugin Name: Beaver Builder Colour Picker Presets Override
 * Plugin URI:  https://github.com/weavedigital/bb-color-picker-override
 * Description: Makes color presets visible first by default in Beaver Builder's color picker to streamline your development workflow. Updated for Beaver Builder 2.9+
 * Version:     2.0.0-beta
 * Author:     Weave Digital Studio, Gareth Bissland
 * Author URI:  https://weave.co.nz
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: bb-color-picker-presets
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue the custom script to modify Beaver Builder's colour picker behaviour.
 * Updated for BB 2.9+ React-based color picker.
 */
function bb_color_picker_override_enqueue_script() {
    // Only enqueue in builder
    if (!FLBuilderModel::is_builder_active()) {
        return;
    }
    
    wp_enqueue_script(
        'bb-color-picker-override', 
        plugin_dir_url(__FILE__) . 'js/override-color-picker-bb29.js',
        ['jquery'], 
        '2.0.0',
        true 
    );
}
add_action('wp_enqueue_scripts', 'bb_color_picker_override_enqueue_script');
