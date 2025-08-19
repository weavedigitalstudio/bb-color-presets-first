<?php
/**
 * Plugin Name: BB Color Presets First
 * Plugin URI:  https://github.com/weavedigitalstudio/bb-color-presets-first
 * Description: Speeds up site building by making Beaver Builder's colour presets tab the default in both classic (<2.9) and the new React-based color pickers (BB 2.9+).
 * Version:     1.1.2
 * Author:     Weave Digital Studio, Gareth Bissland
 * Author URI:  https://weave.co.nz
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: bb-color-picker-presets
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Initialize GitHub updater if we're in admin
 */
function bb_color_presets_init_github_updater() {
    if (is_admin() && file_exists(plugin_dir_path(__FILE__) . 'includes/github-updater.php')) {
        require_once plugin_dir_path(__FILE__) . 'includes/github-updater.php';
        BBCPF_GitHub_Updater::init(__FILE__);
    }
}
add_action('init', 'bb_color_presets_init_github_updater');

/**
 * Detect Beaver Builder version to determine which script to load
 * 
 * @return bool True if BB version is 2.9 or higher
 */
function bb_color_picker_is_new_react_version() {
    if ( ! class_exists( 'FLBuilderModel' ) ) {
        return false;
    }
    
    // Check if BB_PLUGIN_VERSION is defined
    if ( defined( 'FL_BUILDER_VERSION' ) ) {
        return version_compare( FL_BUILDER_VERSION, '2.9', '>=' );
    }
    
    return false;
}

/**
 * Enqueue the classic script for older Beaver Builder versions
 * This targets the pre-2.9 color picker
 */
function bb_color_picker_override_enqueue_classic_script() {
    // Skip if we're using the React version
    if ( bb_color_picker_is_new_react_version() ) {
        return;
    }
    
    // Only load when builder is active
    if ( ! class_exists( 'FLBuilderModel' ) || ! FLBuilderModel::is_builder_active() ) {
        return;
    }
    
    wp_enqueue_script(
        'bb-color-picker-override', 
        plugin_dir_url( __FILE__ ) . 'js/override-color-picker.js',
        ['jquery'], 
        '1.1.2',
        true 
    );
}
add_action( 'wp_enqueue_scripts', 'bb_color_picker_override_enqueue_classic_script' );

/**
 * Add script to activate presets tab in React-based color picker (BB 2.9+)
 * This uses the inline script approach that's proven to work
 */
function bb_color_picker_activate_react_presets() {
    // Skip if we're using the classic version
    if ( ! bb_color_picker_is_new_react_version() ) {
        return;
    }
    
    // Only load when builder is active
    if ( ! class_exists( 'FLBuilderModel' ) || ! FLBuilderModel::is_builder_active() ) {
        return;
    }
    
    ?>
    <script>
    (function($) {
        // Function to click the presets tab in a color picker
        function clickPresetsTab(container) {
            if (!container) return;
            
            // Find the tabs at the bottom
            var $tabs = $(container).find('.fl-controls-picker-bottom-tabs');
            if (!$tabs.length) return;
            
            // The tabs are buttons - the last one should be presets
            var $buttons = $tabs.find('.fl-control');
            if (!$buttons.length) return;
            
            // Get the last button (presets tab)
            var $presetsTab = $buttons.last();
            
            // Only click if not already selected
            if (!$presetsTab.hasClass('is-selected')) {
                //console.log('BB Color Picker: Activating presets tab');
                $presetsTab.trigger('click');
            }
        }
        
        // Watch for color pickers opening
        // Method 1: Watch for dialog elements being added
        $(document).on('click', '.fl-controls-dialog-button', function() {
            // Give the dialog time to fully render
            setTimeout(function() {
                // Find the dialog
                $('.fl-controls-dialog').each(function() {
                    clickPresetsTab(this);
                });
            }, 50);
        });
        
        // Method 2: Direct MutationObserver approach
        $(function() {
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes && mutation.addedNodes.length) {
                        for (var i = 0; i < mutation.addedNodes.length; i++) {
                            var node = mutation.addedNodes[i];
                            
                            // Check if this is a dialog
                            if ($(node).hasClass('fl-controls-dialog')) {
                                // Multiple timeouts to ensure we catch it
                                setTimeout(function() { clickPresetsTab(node); }, 10);
                                setTimeout(function() { clickPresetsTab(node); }, 50);
                                setTimeout(function() { clickPresetsTab(node); }, 150);
                            }
                        }
                    }
                });
            });
            
            // Start observing
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
            
            // Also look for any existing color pickers
            $('.fl-controls-dialog').each(function() {
                clickPresetsTab(this);
            });
            
            // Handle iframe content too
            $(document).on('fl-builder.preview-rendered', function() {
                if ($('#fl-builder-iframe').length) {
                    var iframe = document.getElementById('fl-builder-iframe');
                    if (iframe && iframe.contentDocument) {
                        // Set up observer in iframe
                        var iframeObserver = new MutationObserver(function(mutations) {
                            mutations.forEach(function(mutation) {
                                if (mutation.addedNodes && mutation.addedNodes.length) {
                                    for (var i = 0; i < mutation.addedNodes.length; i++) {
                                        var node = mutation.addedNodes[i];
                                        if ($(node).hasClass('fl-controls-dialog')) {
                                            setTimeout(function() { clickPresetsTab(node); }, 10);
                                            setTimeout(function() { clickPresetsTab(node); }, 50);
                                            setTimeout(function() { clickPresetsTab(node); }, 150);
                                        }
                                    }
                                }
                            });
                        });
                        
                        iframeObserver.observe(iframe.contentDocument.body, {
                            childList: true,
                            subtree: true
                        });
                    }
                }
            });
        });
        
    })(jQuery);
    </script>
    <?php
}

// Add to both footer locations with high priority
add_action( 'wp_footer', 'bb_color_picker_activate_react_presets', 999 );
add_action( 'admin_footer', 'bb_color_picker_activate_react_presets', 999 );

/**
 * Enqueue CSS to hide color palette add button in Beaver Builder
 * 
 * This feature prevents users from adding custom colors and forces them to use only
 * the predefined color palettes (e.g., Global Colors in Beaver Builder or GeneratePress).
 * 
 * If you want to allow users to add colors via the color picker instead of
 * requiring them to use the BB Global color settings panel, comment out this
 * function or remove the action hooks at the bottom.
 */
function bb_color_presets_restrict_color_palette() {
    // Only continue if Beaver Builder exists and is active
    if (!class_exists('FLBuilderModel') || !FLBuilderModel::is_builder_active()) {
        return;
    }

    // CSS to hide undesired elements
    $css = '
        /* Hide the legacy color picker add/remove buttons */
        .fl-color-picker-ui .fl-color-picker-preset-add,
        .fl-color-picker-ui .fl-color-picker-presets-list .fl-color-picker-preset-remove,
        
        /* Hide the "Saved Colors" section completely */
        .fl-controls-swatch-group.fl-appearance-swatches,
        
        /* Hide the add color button in the toolbar */
        .fl-color-picker-toolbar > div:last-child > button {
            display: none !important;
        }
    ';
    
    // Add directly to avoid any stylesheet loading issues
    echo '<style id="bb-color-presets-restrict">' . $css . '</style>';
}

// Use wp_footer for frontend builder and admin_footer for admin panel
add_action('wp_footer', 'bb_color_presets_restrict_color_palette', 100);
add_action('admin_footer', 'bb_color_presets_restrict_color_palette', 100);
