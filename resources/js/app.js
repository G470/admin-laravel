import './bootstrap';
/*
  Add custom scripts here
*/
// jQuery importieren (muss vor allen anderen sein, die jQuery verwenden)
import '../assets/vendor/libs/jquery/jquery.js';
// Popper.js für Tooltips und Popovers
import '../assets/vendor/libs/popper/popper.js';
// Bootstrap Framework
import '../assets/vendor/js/bootstrap.js';

// Import Quill and Dropzone
import '../assets/vendor/libs/quill/quill.js';
import '../assets/vendor/libs/quill/katex.js';
import '../assets/vendor/libs/dropzone/dropzone.js';
// Import Select2 und Flatpickr
import '../assets/vendor/libs/select2/select2.js';
import '../assets/vendor/libs/flatpickr/flatpickr.js';

// Import Editor- und Select-Konfigurationen
import '../assets/js/forms-editors.js';
import '../assets/js/forms-selects.js';

import.meta.glob([
  '../assets/img/**',
  // '../assets/json/**',
  '../assets/vendor/fonts/**'
]);
