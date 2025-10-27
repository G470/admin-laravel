'use strict';

// Admin-specific JavaScript
document.addEventListener('DOMContentLoaded', function () {
  // Menu toggle for mobile
  const menuToggler = document.querySelector('.layout-menu-toggle');
  if (menuToggler) {
    menuToggler.addEventListener('click', function () {
      document.body.classList.toggle('layout-menu-expanded');
    });
  }

  // Initialize tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Initialize popovers
  const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
  popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
  });

  // Auto-update year in footer copyright
  const yearEl = document.querySelector('.footer-year');
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }

  // Admin-specific initializations can go here

  // Admin datatable initialization
  if (typeof $.fn.DataTable !== 'undefined') {
    $('.admin-datatable').DataTable({
      responsive: true,
      lengthMenu: [10, 25, 50, 100],
      pageLength: 25
    });
  }

  // Admin form validation
  const adminForms = document.querySelectorAll('.admin-form');
  if (adminForms.length > 0) {
    adminForms.forEach(form => {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      });
    });
  }
});
