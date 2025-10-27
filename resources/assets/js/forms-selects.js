/**
 * Mary UI Choices - Select Components
 * Migration from Select2.js to Mary UI Choices
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const selectPicker = document.querySelectorAll('.selectpicker'),
    choices = document.querySelectorAll('.choices'),
    choicesIcons = document.querySelectorAll('.choices-icons'),
    legacySelect2 = document.querySelectorAll('.select2'),
    legacySelect2Icons = document.querySelectorAll('.select2-icons');

  // Bootstrap Select (keep existing)
  // --------------------------------------------------------------------
  if (selectPicker.length && typeof $.fn.selectpicker !== 'undefined') {
    $(selectPicker).selectpicker();
  }

  // Mary UI Choices - Default
  // --------------------------------------------------------------------
  if (choices.length) {
    choices.forEach(function (element) {
      new Choices(element, {
        searchEnabled: true,
        itemSelectText: '',
        removeItemButton: true,
        placeholder: true,
        placeholderValue: 'Select value',
        classNames: {
          containerOuter: 'choices select2-replacement'
        }
      });
    });
  }

  // Mary UI Choices - With Icons
  // --------------------------------------------------------------------
  if (choicesIcons.length) {
    choicesIcons.forEach(function (element) {
      new Choices(element, {
        searchEnabled: true,
        itemSelectText: '',
        removeItemButton: true,
        placeholder: true,
        placeholderValue: 'Select value',
        classNames: {
          containerOuter: 'choices select2-replacement'
        },
        callbackOnCreateTemplates: function (template) {
          return {
            item: (classNames, data) => {
              const icon = data.element.dataset.icon || '';
              return template(`
                <div class="${classNames.item} ${data.highlighted ? classNames.highlightedState : classNames.itemSelectable}" data-item data-id="${data.id}" data-value="${data.value}" ${data.active ? 'aria-selected' : ''} ${data.disabled ? 'aria-disabled' : ''}>
                  ${icon ? `<i class="${icon} me-2"></i>` : ''}${data.label}
                </div>
              `);
            },
            choice: (classNames, data) => {
              const icon = data.element.dataset.icon || '';
              return template(`
                <div class="${classNames.item} ${classNames.itemChoice} ${data.disabled ? classNames.itemDisabled : classNames.itemSelectable}" data-choice ${data.disabled ? 'data-choice-disabled aria-disabled' : 'data-choice-selectable'} data-id="${data.id}" data-value="${data.value}" ${data.placeholder ? 'data-choice-placeholder' : ''}>
                  ${icon ? `<i class="${icon} me-2"></i>` : ''}${data.label}
                </div>
              `);
            }
          };
        }
      });
    });
  }

  // Legacy Select2 Migration - Default
  // --------------------------------------------------------------------
  if (legacySelect2.length) {
    legacySelect2.forEach(function (element) {
      if (typeof Choices !== 'undefined') {
        new Choices(element, {
          searchEnabled: true,
          itemSelectText: '',
          removeItemButton: true,
          placeholder: true,
          placeholderValue: 'Select value',
          classNames: {
            containerOuter: 'choices select2-replacement'
          }
        });
      } else {
        // Fallback to Select2 if Choices is not available
        console.warn('Choices not available, falling back to Select2');
        if (typeof $ !== 'undefined' && $.fn.select2) {
          $(element)
            .wrap('<div class="position-relative"></div>')
            .select2({
              placeholder: 'Select value',
              dropdownParent: $(element).parent()
            });
        }
      }
    });
  }

  // Legacy Select2 Migration - With Icons
  // --------------------------------------------------------------------
  if (legacySelect2Icons.length) {
    legacySelect2Icons.forEach(function (element) {
      if (typeof Choices !== 'undefined') {
        new Choices(element, {
          searchEnabled: true,
          itemSelectText: '',
          removeItemButton: true,
          placeholder: true,
          placeholderValue: 'Select value',
          classNames: {
            containerOuter: 'choices select2-replacement'
          },
          callbackOnCreateTemplates: function (template) {
            return {
              item: (classNames, data) => {
                const icon = data.element.dataset.icon || '';
                return template(`
                  <div class="${classNames.item} ${data.highlighted ? classNames.highlightedState : classNames.itemSelectable}" data-item data-id="${data.id}" data-value="${data.value}" ${data.active ? 'aria-selected' : ''} ${data.disabled ? 'aria-disabled' : ''}>
                    ${icon ? `<i class="${icon} me-2"></i>` : ''}${data.label}
                  </div>
                `);
              },
              choice: (classNames, data) => {
                const icon = data.element.dataset.icon || '';
                return template(`
                  <div class="${classNames.item} ${classNames.itemChoice} ${data.disabled ? classNames.itemDisabled : classNames.itemSelectable}" data-choice ${data.disabled ? 'data-choice-disabled aria-disabled' : 'data-choice-selectable'} data-id="${data.id}" data-value="${data.value}" ${data.placeholder ? 'data-choice-placeholder' : ''}>
                    ${icon ? `<i class="${icon} me-2"></i>` : ''}${data.label}
                  </div>
                `);
              }
            };
          }
        });
      } else {
        // Fallback to Select2 if Choices is not available
        console.warn('Choices not available, falling back to Select2');
        if (typeof $ !== 'undefined' && $.fn.select2) {
          function renderIcons(option) {
            if (!option.id) {
              return option.text;
            }
            var $icon = "<i class='" + $(option.element).data('icon') + " me-2'></i>" + option.text;
            return $icon;
          }
          $(element)
            .wrap('<div class="position-relative"></div>')
            .select2({
              dropdownParent: $(element).parent(),
              templateResult: renderIcons,
              templateSelection: renderIcons,
              escapeMarkup: function (es) {
                return es;
              }
            });
        }
      }
    });
  }
});
