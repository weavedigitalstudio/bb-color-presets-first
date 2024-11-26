jQuery(document).ready(function($) {
	/**
	 * Observes DOM changes and forces the colour picker presets to be visible.
	 */
	function enforcePresetVisibility() {
		$('.fl-color-picker-ui').each(function() {
			const presetsWrap = $(this).find('.fl-color-picker-presets-list-wrap');

			if (presetsWrap.length && presetsWrap.css('display') === 'none') {
				// Make the presets list visible
				presetsWrap.css('display', 'block');

				// Ensure proper toggling classes are applied
				$(this)
					.find('.fl-color-picker-presets-open-label')
					.addClass('fl-color-picker-active');
				$(this)
					.find('.fl-color-picker-presets-close-label')
					.removeClass('fl-color-picker-active');
			}
		});
	}

	/**
	 * Observe DOM mutations to detect changes in the color picker.
	 */
	const observer = new MutationObserver(() => {
		enforcePresetVisibility();
	});

	// Attach observer to the document body
	observer.observe(document.body, {
		childList: true,
		subtree: true,
	});

	// Enforce visibility when the color picker button is clicked
	$(document).on('click', '.fl-color-picker-color', function() {
		enforcePresetVisibility();
	});
});
