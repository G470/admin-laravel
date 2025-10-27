/**
 * Template related functionality initialization
 */

// Ensure templateName is defined globally
window.templateName = window.templateName || 'inlando';

// Ensure assetsPath is defined for image switching
window.assetsPath = document.documentElement.getAttribute('data-assets-path') || '/assets/';

// Initialize core framework when document is ready
document.addEventListener('DOMContentLoaded', function () {
  if (window.Helpers) {
    // Initialize any template-specific functionality
    window.Helpers.initSidebarToggle();
    window.Helpers.initNavbarDropdownScrollbar();
    window.Helpers.initPasswordToggle();
    window.Helpers.initSpeechToText();

    // Update layout
    window.Helpers.setAutoUpdate(true);
    window.Helpers.update();
  }
});
