/**
 *  Pages Authentication
 */

'use strict';

document.addEventListener('DOMContentLoaded', function (e) {
  (function () {
    const formAuthentication = document.querySelector('#formAuthentication');
    
    // Form validation - only initialize if FormValidation is available and form exists
    // If FormValidation is not available, the form will still submit normally
    if (formAuthentication && typeof FormValidation !== 'undefined') {
      // Build fields object dynamically based on what fields exist in the form
      const fields = {};
      
      // Email field validation
      if (formAuthentication.querySelector('[name="email"]')) {
        fields.email = {
          validators: {
            notEmpty: {
              message: 'Bitte gib deine E-Mail-Adresse ein'
            },
            emailAddress: {
              message: 'Bitte gib eine gültige E-Mail-Adresse ein'
            }
          }
        };
      }
      
      // Email-username field (for forms that use email or username)
      if (formAuthentication.querySelector('[name="email-username"]')) {
        fields['email-username'] = {
          validators: {
            notEmpty: {
              message: 'Bitte gib deine E-Mail-Adresse oder deinen Benutzernamen ein'
            },
            stringLength: {
              min: 6,
              message: 'Der Benutzername muss mehr als 6 Zeichen lang sein'
            }
          }
        };
      }
      
      // Username field
      if (formAuthentication.querySelector('[name="username"]')) {
        fields.username = {
          validators: {
            notEmpty: {
              message: 'Bitte gib deinen Benutzernamen ein'
            },
            stringLength: {
              min: 6,
              message: 'Der Benutzername muss mehr als 6 Zeichen lang sein'
            }
          }
        };
      }
      
      // Password field validation
      if (formAuthentication.querySelector('[name="password"]')) {
        fields.password = {
          validators: {
            notEmpty: {
              message: 'Bitte gib dein Passwort ein'
            },
            stringLength: {
              min: 6,
              message: 'Das Passwort muss mindestens 6 Zeichen lang sein'
            }
          }
        };
      }
      
      // Confirm password field (for registration forms)
      if (formAuthentication.querySelector('[name="confirm-password"]')) {
        fields['confirm-password'] = {
          validators: {
            notEmpty: {
              message: 'Bitte bestätige dein Passwort'
            },
            identical: {
              compare: function () {
                return formAuthentication.querySelector('[name="password"]').value;
              },
              message: 'Das Passwort und die Bestätigung stimmen nicht überein'
            },
            stringLength: {
              min: 6,
              message: 'Das Passwort muss mindestens 6 Zeichen lang sein'
            }
          }
        };
      }
      
      // Terms checkbox (for registration forms)
      if (formAuthentication.querySelector('[name="terms"]')) {
        fields.terms = {
          validators: {
            notEmpty: {
              message: 'Bitte stimme den AGB zu'
            }
          }
        };
      }
      
      // Only initialize validation if we have fields to validate
      if (Object.keys(fields).length > 0) {
        const fv = FormValidation.formValidation(formAuthentication, {
          fields: fields,
          plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap5: new FormValidation.plugins.Bootstrap5({
              eleValidClass: '',
              rowSelector: '.form-control-validation'
            }),
            submitButton: new FormValidation.plugins.SubmitButton(),
            defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
            autoFocus: new FormValidation.plugins.AutoFocus()
          },
          init: instance => {
            instance.on('plugins.message.placed', function (e) {
              if (e.element.parentElement.classList.contains('input-group')) {
                e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
              }
            });
          }
        });
      }
    }

    //  Two Steps Verification
    const numeralMask = document.querySelectorAll('.numeral-mask');

    // Verification masking
    if (numeralMask.length) {
      numeralMask.forEach(e => {
        new Cleave(e, {
          numeral: true
        });
      });
    }
  })();
});
