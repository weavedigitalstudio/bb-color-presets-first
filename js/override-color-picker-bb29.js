/**
 * Beaver Builder 2.9+ Color Picker Override
 * 
 * This script automatically selects the presets tab when the color picker opens.
 * Updated for BB 2.9's React-based color picker.
 */
(function($) {
    $(function() {
        // Wait for the DOM to be ready
        $(document).ready(function() {
            // Observer to detect when color picker appears in the DOM
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    // Look for color picker additions
                    if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                        for (var i = 0; i < mutation.addedNodes.length; i++) {
                            // Check if node is an element and has classList
                            if (mutation.addedNodes[i].nodeType === 1 && mutation.addedNodes[i].classList) {
                                // Look for the color picker dialog
                                if (mutation.addedNodes[i].classList.contains('fl-controls-color-picker-ui') || 
                                    mutation.addedNodes[i].querySelector('.fl-controls-color-picker-ui')) {
                                    
                                    // Find and click the presets tab
                                    setTimeout(function() {
                                        $('.fl-controls-picker-bottom-tabs button').each(function() {
                                            if ($(this).text().indexOf('Presets') >= 0 && !$(this).hasClass('is-selected')) {
                                                $(this).click();
                                            }
                                        });
                                    }, 10);
                                }
                            }
                        }
                    }
                });
            });

            // Start observing the document body for added nodes
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    });
})(jQuery);
