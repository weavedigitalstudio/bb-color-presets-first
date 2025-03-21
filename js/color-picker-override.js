/**
 * Enhanced BB Color Picker Override
 * Shows color presets by default and ensures they stay visible
 */
(function($) {
    "use strict";

    /**
     * Function to make the presets visible when a color picker opens
     */
    function showPresets() {
        // Target both older and newer versions of the color picker
        $('.fl-color-picker-ui, .fl-controls-color-picker-ui').each(function() {
            // Attempt to find the presets wrap in different versions
            const presetsWrap = $(this).find('.fl-color-picker-presets-list-wrap');
            
            if (presetsWrap.length && presetsWrap.css('display') === 'none') {
                // Make presets visible
                presetsWrap.css('display', 'block');
                
                // Update toggle classes
                $(this)
                    .find('.fl-color-picker-presets-open-label')
                    .addClass('fl-color-picker-active');
                $(this)
                    .find('.fl-color-picker-presets-close-label')
                    .removeClass('fl-color-picker-active');
            }
        });
        
        // Also handle the modern React-based color picker
        $('.fl-controls-picker-bottom-tabs button').each(function() {
            // If the button contains "Presets", click it if it's not already selected
            if ($(this).text().indexOf('Presets') >= 0 && !$(this).hasClass('is-selected')) {
                $(this).trigger('click');
            }
        });
    }
    
    // Setup DOM mutation observer to detect when color pickers are added
    if (window.MutationObserver) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                // Look for added nodes that might contain color pickers
                if (mutation.addedNodes && mutation.addedNodes.length) {
                    for (let i = 0; i < mutation.addedNodes.length; i++) {
                        const node = mutation.addedNodes[i];
                        
                        // If we found a color picker or it might contain one
                        if ($(node).hasClass('fl-color-picker-ui') || 
                            $(node).hasClass('fl-controls-color-picker-ui') ||
                            $(node).find('.fl-color-picker-ui, .fl-controls-color-picker-ui').length) {
                            // Delay slightly to ensure the UI is fully rendered
                            setTimeout(showPresets, 50);
                        }
                    }
                }
            });
        });
        
        // Start observing the document for color picker insertions
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // Handle direct clicks on color picker buttons
    $(document).on('click', '.fl-color-picker-color', function() {
        // Wait a moment for the picker UI to be fully rendered
        setTimeout(showPresets, 50);
    });
    
    // Also handle modern React color picker opening
    $(document).on('click', '.fl-controls-color-swatch', function() {
        // Wait a moment for the picker UI to be fully rendered
        setTimeout(showPresets, 50);
    });

    // Initialize for any existing color pickers when the page loads
    $(function() {
        setTimeout(showPresets, 100);
    });

})(jQuery);
