'use strict';

$(document).ready(function () {
  // Password toggle functionality
  $('.toggle-password').on('click', function () {
    const targetId = $(this).data('target');
    const targetInput = $('#' + targetId);
    const icon = $(this).find('i');

    if (targetInput.attr('type') === 'password') {
      targetInput.attr('type', 'text');
      icon.removeClass('ti-eye').addClass('ti-eye-off');
    } else {
      targetInput.attr('type', 'password');
      icon.removeClass('ti-eye-off').addClass('ti-eye');
    }
  });

  // Test connection functionality
  $('.test-connection').on('click', function () {
    const button = $(this);
    const provider = button.data('provider');
    const environment = button.data('environment');
    const originalText = button.html();

    button.prop('disabled', true);
    button.html('<i class="ti ti-loader ti-spin me-1"></i>Testing...');

    $.ajax({
      url: '/admin/payment-providers/test-connection',
      method: 'POST',
      data: {
        provider: provider,
        environment: environment,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function (response) {
        if (response.success) {
          // Show success notification
          if (typeof toastr !== 'undefined') {
            toastr.success(response.message, 'Connection Test');
          } else {
            alert('Success: ' + response.message);
          }
          button.removeClass('btn-outline-primary').addClass('btn-outline-success');
        } else {
          // Show error notification
          if (typeof toastr !== 'undefined') {
            toastr.error(response.message, 'Connection Test');
          } else {
            alert('Error: ' + response.message);
          }
          button.removeClass('btn-outline-primary').addClass('btn-outline-danger');
        }
      },
      error: function () {
        const errorMsg = 'Connection test failed';
        if (typeof toastr !== 'undefined') {
          toastr.error(errorMsg, 'Error');
        } else {
          alert('Error: ' + errorMsg);
        }
        button.removeClass('btn-outline-primary').addClass('btn-outline-danger');
      },
      complete: function () {
        button.prop('disabled', false);
        button.html(originalText);

        setTimeout(function () {
          button.removeClass('btn-outline-success btn-outline-danger').addClass('btn-outline-primary');
        }, 3000);
      }
    });
  });

  // Test all connections
  $('#test-all-connections').on('click', function () {
    $('.test-connection').each(function () {
      $(this).trigger('click');
    });
  });

  // Dynamic pricing calculation preview
  function updatePricingPreview() {
    const baseFee = parseFloat($('#base_monthly_fee').val()) || 0;
    const pricePerRental = parseFloat($('#price_per_rental').val()) || 0;
    const pricePerCategory = parseFloat($('#price_per_category').val()) || 0;
    const pricePerLocation = parseFloat($('#price_per_location').val()) || 0;

    // Example calculation (10 rentals, 3 categories, 2 locations)
    const rentalsCost = 10 * pricePerRental;
    const categoriesCost = 3 * pricePerCategory;
    const locationsCost = 2 * pricePerLocation;
    const total = baseFee + rentalsCost + categoriesCost + locationsCost;

    $('#calc-base').text('€' + baseFee.toFixed(2));
    $('#calc-rentals').text('€' + rentalsCost.toFixed(2));
    $('#calc-categories').text('€' + categoriesCost.toFixed(2));
    $('#calc-locations').text('€' + locationsCost.toFixed(2));
    $('#calc-total').text('€' + total.toFixed(2));
  }

  // Update pricing preview when inputs change
  $('#base_monthly_fee, #price_per_rental, #price_per_category, #price_per_location').on('input', updatePricingPreview);

  // Initial pricing preview calculation
  updatePricingPreview();

  // Tab URL hash navigation
  if (window.location.hash) {
    const hash = window.location.hash;
    if (hash === '#paypal' || hash === '#stripe' || hash === '#pricing') {
      $('.nav-link').removeClass('active');
      $('.tab-pane').removeClass('show active');
      $(`[data-bs-target="${hash}"]`).addClass('active');
      $(hash).addClass('show active');
    }
  }

  // Update URL when tab changes
  $('[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
    const target = $(e.target).attr('data-bs-target');
    window.location.hash = target;
  });

  // Form validation enhancements
  $('form').on('submit', function (e) {
    let hasErrors = false;

    // Validate Stripe keys format
    $('input[name*="stripe_publishable"]').each(function () {
      const value = $(this).val();
      if (value && !value.startsWith('pk_')) {
        $(this).addClass('is-invalid');
        hasErrors = true;
      } else {
        $(this).removeClass('is-invalid');
      }
    });

    $('input[name*="stripe_secret"]').each(function () {
      const value = $(this).val();
      if (value && !value.startsWith('sk_')) {
        $(this).addClass('is-invalid');
        hasErrors = true;
      } else {
        $(this).removeClass('is-invalid');
      }
    });

    $('input[name*="stripe_webhook_secret"]').each(function () {
      const value = $(this).val();
      if (value && !value.startsWith('whsec_')) {
        $(this).addClass('is-invalid');
        hasErrors = true;
      } else {
        $(this).removeClass('is-invalid');
      }
    });

    if (hasErrors) {
      e.preventDefault();
      if (typeof toastr !== 'undefined') {
        toastr.error('Please check the API key formats before saving.', 'Validation Error');
      } else {
        alert('Please check the API key formats before saving.');
      }
      return false;
    }
  });

  // Real-time validation feedback
  $('input[name*="stripe_publishable"]').on('blur', function () {
    const value = $(this).val();
    if (value && !value.startsWith('pk_')) {
      $(this).addClass('is-invalid');
    } else {
      $(this).removeClass('is-invalid');
    }
  });

  $('input[name*="stripe_secret"]').on('blur', function () {
    const value = $(this).val();
    if (value && !value.startsWith('sk_')) {
      $(this).addClass('is-invalid');
    } else {
      $(this).removeClass('is-invalid');
    }
  });

  $('input[name*="stripe_webhook_secret"]').on('blur', function () {
    const value = $(this).val();
    if (value && !value.startsWith('whsec_')) {
      $(this).addClass('is-invalid');
    } else {
      $(this).removeClass('is-invalid');
    }
  });

  // Environment toggle functionality
  $('.environment-toggle').on('change', function () {
    const toggle = $(this);
    const provider = toggle.data('provider');
    const isDevelopment = toggle.is(':checked');
    const environment = isDevelopment ? 'development' : 'production';
    const label = toggle.closest('.form-check').find('.form-check-label');
    const productionLabel = label.find('.environment-label-production');
    const developmentLabel = label.find('.environment-label-development');

    // Update label text
    if (isDevelopment) {
      productionLabel.addClass('d-none');
      developmentLabel.removeClass('d-none');
    } else {
      productionLabel.removeClass('d-none');
      developmentLabel.addClass('d-none');
    }

    // Get CSRF token
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    if (!csrfToken) {
      console.error('CSRF token not found');
      toggle.prop('checked', !isDevelopment);
      if (typeof toastr !== 'undefined') {
        toastr.error('CSRF token not found', 'Error');
      } else {
        alert('Error: CSRF token not found');
      }
      return;
    }

    // Send AJAX request to update environment
    $.ajax({
      url: '/admin/payment-providers/toggle-environment',
      method: 'POST',
      data: {
        provider: provider,
        environment: environment,
        _token: csrfToken
      },
      success: function (response) {
        console.log('Toggle response:', response);
        if (response.success) {
          // Show success notification
          if (typeof toastr !== 'undefined') {
            toastr.success(response.message, 'Environment Updated');
          } else {
            alert('Success: ' + response.message);
          }

          // Update the toggle state to reflect the change
          toggle.prop('checked', isDevelopment);
        } else {
          // Revert toggle state on error
          toggle.prop('checked', !isDevelopment);
          const errorMsg = response.message || 'Failed to update environment';
          if (typeof toastr !== 'undefined') {
            toastr.error(errorMsg, 'Error');
          } else {
            alert('Error: ' + errorMsg);
          }
        }
      },
      error: function (xhr, status, error) {
        console.error('Toggle error:', {xhr, status, error});
        // Revert toggle state on error
        toggle.prop('checked', !isDevelopment);
        
        let errorMsg = 'Failed to update environment';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        } else if (xhr.status === 419) {
          errorMsg = 'CSRF token mismatch. Please refresh the page.';
        } else if (xhr.status === 500) {
          errorMsg = 'Server error occurred. Please try again.';
        }
        
        if (typeof toastr !== 'undefined') {
          toastr.error(errorMsg, 'Error');
        } else {
          alert('Error: ' + errorMsg);
        }
      }
    });
  });

  // Initialize environment toggle labels on page load
  $('.environment-toggle').each(function () {
    const toggle = $(this);
    const isDevelopment = toggle.is(':checked');
    const label = toggle.closest('.form-check').find('.form-check-label');
    const productionLabel = label.find('.environment-label-production');
    const developmentLabel = label.find('.environment-label-development');

    if (isDevelopment) {
      productionLabel.addClass('d-none');
      developmentLabel.removeClass('d-none');
    } else {
      productionLabel.removeClass('d-none');
      developmentLabel.addClass('d-none');
    }
  });
});
