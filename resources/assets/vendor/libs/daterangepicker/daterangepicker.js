// Import moment.js
import moment from 'moment';

// Make moment available globally first
window.moment = moment;

// Import daterangepicker after moment is available
import 'daterangepicker';

// Export for ES6 modules
export { moment };
