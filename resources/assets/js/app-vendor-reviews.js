/**
 * App Vendor Reviews
 */
'use strict';

// DOM elements
const reviewsRatingFilter = document.querySelector('#FilterTransaction'),
  reviewsStatusFilter = document.querySelector('#FilterStatus'),
  reviewsSearchInput = document.querySelector('.reviews-search input');

// Initialize when document is loaded
document.addEventListener('DOMContentLoaded', function () {
  // Set up filter change handlers
  if (reviewsRatingFilter) {
    reviewsRatingFilter.addEventListener('change', function (e) {
      const ratingValue = e.target.value;
      // Using Livewire dispatch to filter by rating
      // This will be connected to the Livewire component once it's fully implemented
      window.Livewire.dispatch('filterByRating', { rating: ratingValue });
    });
  }

  if (reviewsStatusFilter) {
    reviewsStatusFilter.addEventListener('change', function (e) {
      const statusValue = e.target.value;
      // Using Livewire dispatch to filter by status
      // This will be connected to the Livewire component once it's fully implemented
      window.Livewire.dispatch('filterByStatus', { status: statusValue });
    });
  }

  // Set up search input handler with debounce
  if (reviewsSearchInput) {
    let searchTimeout;
    reviewsSearchInput.addEventListener('input', function (e) {
      const searchValue = e.target.value;

      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function () {
        // Using Livewire dispatch to search
        // This will be connected to the Livewire component once it's fully implemented
        window.Livewire.dispatch('search', { query: searchValue });
      }, 500);
    });
  }

  // Initialize tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
