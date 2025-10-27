/**
 * Mary UI Choices - Select Components
 * Migration from Select2.js to Mary UI Choices
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  const selectPicker = document.querySelectorAll('.selectpicker'),
    choices = document.querySelectorAll('.choices'),
    choicesIcons = document.querySelectorAll('.choices-icons');

  // Bootstrap Select (keep existing)
  // --------------------------------------------------------------------
  if (selectPicker.length) {
    // Keep existing Bootstrap Select functionality
    // This will be migrated separately if needed
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
          containerOuter: 'choices select2-replacement',
          containerInner: 'choices__inner',
          input: 'choices__input',
          inputCloned: 'choices__input--cloned',
          list: 'choices__list',
          listItems: 'choices__list--multiple',
          listSingle: 'choices__list--single',
          listDropdown: 'choices__list--dropdown',
          item: 'choices__item',
          itemSelectable: 'choices__item--selectable',
          itemDisabled: 'choices__item--disabled',
          itemChoice: 'choices__item--choice',
          placeholder: 'choices__placeholder',
          group: 'choices__group',
          groupHeading: 'choices__heading',
          label: 'choices__label',
          activeState: 'is-active',
          focusState: 'is-focused',
          openState: 'is-open',
          disabledState: 'is-disabled',
          flippedState: 'is-flipped',
          loadingState: 'is-loading',
          noResults: 'has-no-results',
          noChoices: 'has-no-choices'
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

  // Legacy Select2 Support (for backward compatibility)
  // --------------------------------------------------------------------
  const legacySelect2 = document.querySelectorAll('.select2');
  if (legacySelect2.length) {
    legacySelect2.forEach(function (element) {
      // Check if Choices is available, otherwise fallback to Select2
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
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
  module.exports = {
    initChoices: function () {
      // Re-initialize choices if needed
      document.dispatchEvent(new Event('DOMContentLoaded'));
    }
  };
}
