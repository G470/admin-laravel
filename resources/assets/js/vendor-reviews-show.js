/**
 * Vendor Reviews Show Page JavaScript
 */

'use strict';

(function () {
  // Initialize when DOM is ready
  document.addEventListener('DOMContentLoaded', function () {
    // Handle reply form submission
    const replyForm = document.querySelector('form[action="#"]');
    if (replyForm) {
      replyForm.addEventListener('submit', function (e) {
        e.preventDefault();
        
        const textarea = this.querySelector('#reply_comment');
        const comment = textarea.value.trim();
        
        if (!comment) {
          // Show validation error
          if (!textarea.classList.contains('is-invalid')) {
            textarea.classList.add('is-invalid');
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = 'Bitte geben Sie eine Antwort ein.';
            textarea.parentNode.appendChild(feedback);
          }
          return;
        }
        
        // Remove validation error
        textarea.classList.remove('is-invalid');
        const feedback = textarea.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
          feedback.remove();
        }
        
        // Here you would typically submit the form via AJAX
        // For now, we'll show a success message
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ti ti-loader-2 ti-spin me-1"></i>Wird gesendet...';
        
        // Simulate API call
        setTimeout(() => {
          // Show success message
          const alertDiv = document.createElement('div');
          alertDiv.className = 'alert alert-success alert-dismissible fade show';
          alertDiv.innerHTML = `
            <i class="ti ti-check-circle me-2"></i>
            Ihre Antwort wurde erfolgreich gespeichert.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          `;
          
          this.parentNode.insertBefore(alertDiv, this);
          
          // Reset form
          textarea.value = '';
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
          
          // Hide form after successful submission
          setTimeout(() => {
            this.style.display = 'none';
          }, 2000);
        }, 1500);
      });
    }
    
    // Handle cancel button
    const cancelBtn = document.querySelector('button[type="button"]:contains("Abbrechen")');
    if (cancelBtn) {
      cancelBtn.addEventListener('click', function () {
        const textarea = document.querySelector('#reply_comment');
        if (textarea) {
          textarea.value = '';
          textarea.classList.remove('is-invalid');
          const feedback = textarea.parentNode.querySelector('.invalid-feedback');
          if (feedback) {
            feedback.remove();
          }
        }
      });
    }
  });
})();
