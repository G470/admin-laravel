import PerfectScrollbar from 'perfect-scrollbar/dist/perfect-scrollbar.esm.js';

try {
  window.PerfectScrollbar = PerfectScrollbar;
} catch (e) {}

export { PerfectScrollbar };
