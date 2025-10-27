/**
 * App Calendar - Mary UI Choices Migration
 * Migration from Select2.js to Mary UI Choices for calendar event management
 */

'use strict';

(async function () {
  let boards;
  const addEventSidebar = document.querySelector('#addEventSidebar'),
    addEventForm = document.querySelector('#addEventForm'),
    eventTitle = document.querySelector('#eventTitle'),
    eventLabel = document.querySelector('#eventLabel'),
    eventGuests = document.querySelector('#eventGuests'),
    eventColor = document.querySelector('#eventColor'),
    eventStartDate = document.querySelector('#eventStartDate'),
    eventEndDate = document.querySelector('#eventEndDate'),
    eventUrl = document.querySelector('#eventUrl'),
    eventLocation = document.querySelector('#eventLocation'),
    eventDescription = document.querySelector('#eventDescription'),
    eventSubmitBtn = document.querySelector('#eventSubmitBtn'),
    filterInput = [].slice.call(document.querySelectorAll('.input-filter')),
    inlineCalendar = document.querySelector('.inline-calendar');

  let eventToUpdate,
    currentEvents = events, // Assign app-calendar-events.js file events (assume events from API) to currentEvents (browser store/object) to manage and update calender events
    isFormValid = false,
    inlineCalInstance;

  // Init event Offcanvas
  const bsAddEventSidebar = new bootstrap.Offcanvas(addEventSidebar);

  // Mary UI Choices - Event Label (migrated from Select2)
  if (eventLabel) {
    new Choices(eventLabel, {
      searchEnabled: false,
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
            const labelColor = data.element.dataset.label;
            return template(`
              <div class="${classNames.item} ${data.highlighted ? classNames.highlightedState : classNames.itemSelectable}" data-item data-id="${data.id}" data-value="${data.value}" ${data.active ? 'aria-selected' : ''} ${data.disabled ? 'aria-disabled' : ''}>
                <span class="badge badge-dot bg-${labelColor} me-2"></span>${data.label}
              </div>
            `);
          },
          choice: (classNames, data) => {
            const labelColor = data.element.dataset.label;
            return template(`
              <div class="${classNames.item} ${classNames.itemChoice} ${data.disabled ? classNames.itemDisabled : classNames.itemSelectable}" data-choice ${data.disabled ? 'data-choice-disabled aria-disabled' : 'data-choice-selectable'} data-id="${data.id}" data-value="${data.value}" ${data.placeholder ? 'data-choice-placeholder' : ''}>
                <span class="badge badge-dot bg-${labelColor} me-2"></span>${data.label}
              </div>
            `);
          }
        };
      }
    });
  }

  // Mary UI Choices - Event Guests (migrated from Select2)
  if (eventGuests) {
    new Choices(eventGuests, {
      searchEnabled: true,
      itemSelectText: '',
      removeItemButton: true,
      placeholder: true,
      placeholderValue: 'Select guests',
      classNames: {
        containerOuter: 'choices select2-replacement'
      }
    });
  }

  // Date picker initialization
  if (eventStartDate) {
    eventStartDate.flatpickr({
      monthSelectorType: 'static',
      altInput: true,
      altFormat: 'j F, Y',
      dateFormat: 'Y-m-d',
      onChange: function (selectedDates, dateStr, instance) {
        // Update end date min value
        if (eventEndDate && eventEndDate._flatpickr) {
          eventEndDate._flatpickr.set('minDate', selectedDates[0]);
        }
      }
    });
  }

  if (eventEndDate) {
    eventEndDate.flatpickr({
      monthSelectorType: 'static',
      altInput: true,
      altFormat: 'j F, Y',
      dateFormat: 'Y-m-d'
    });
  }

  // Color picker initialization
  if (eventColor) {
    const colorPicker = new Pickr({
      el: eventColor,
      theme: 'classic',
      default: '#696cff',
      swatches: [
        'rgba(244, 67, 54, 1)',
        'rgba(233, 30, 99, 0.95)',
        'rgba(156, 39, 176, 0.9)',
        'rgba(103, 58, 183, 0.85)',
        'rgba(63, 81, 181, 0.8)',
        'rgba(33, 150, 243, 0.75)',
        'rgba(3, 169, 244, 0.7)',
        'rgba(0, 188, 212, 0.7)',
        'rgba(0, 150, 136, 0.75)',
        'rgba(76, 175, 80, 0.8)',
        'rgba(139, 195, 74, 0.85)',
        'rgba(205, 220, 57, 0.9)',
        'rgba(255, 235, 59, 0.95)',
        'rgba(255, 193, 7, 1)'
      ],
      components: {
        preview: true,
        opacity: true,
        hue: true,
        interaction: {
          hex: true,
          rgba: true,
          hsla: false,
          hsva: false,
          cmyk: false,
          input: true,
          clear: false,
          save: true
        }
      }
    });

    colorPicker.on('save', color => {
      eventColor.value = color.toHEXA().toString();
      colorPicker.hide();
    });
  }

  // Form validation
  if (addEventForm) {
    addEventForm.addEventListener('submit', function (e) {
      e.preventDefault();

      // Basic validation
      if (!eventTitle.value || !eventLabel.value) {
        isFormValid = false;
        // Show error message
        return;
      }

      isFormValid = true;

      // Handle form submission
      if (isFormValid) {
        const eventData = {
          title: eventTitle.value,
          label: eventLabel.value,
          guests: eventGuests.value,
          color: eventColor.value,
          startDate: eventStartDate.value,
          endDate: eventEndDate.value,
          url: eventUrl.value,
          location: eventLocation.value,
          description: eventDescription.value
        };

        // Add event to calendar
        addEventToCalendar(eventData);

        // Close sidebar
        bsAddEventSidebar.hide();

        // Reset form
        addEventForm.reset();
      }
    });
  }

  // Add event to calendar function
  function addEventToCalendar(eventData) {
    // Implementation for adding event to calendar
    console.log('Adding event:', eventData);

    // Here you would typically:
    // 1. Add event to currentEvents array
    // 2. Update calendar display
    // 3. Send to server via AJAX
    // 4. Handle success/error responses
  }

  // Filter functionality
  if (filterInput.length) {
    filterInput.forEach(function (input) {
      input.addEventListener('input', function (e) {
        const filterValue = e.target.value.toLowerCase();

        // Filter events based on input
        // Implementation depends on your calendar library
        console.log('Filtering events by:', filterValue);
      });
    });
  }

  // Inline calendar initialization
  if (inlineCalendar) {
    inlineCalInstance = inlineCalendar.flatpickr({
      inline: true,
      monthSelectorType: 'static',
      onChange: function (selectedDates, dateStr, instance) {
        // Handle inline calendar date selection
        console.log('Selected date:', dateStr);
      }
    });
  }

  // Export functions for external use
  window.calendarApp = {
    addEvent: addEventToCalendar,
    getCurrentEvents: () => currentEvents,
    updateEvent: (eventId, eventData) => {
      // Implementation for updating events
      console.log('Updating event:', eventId, eventData);
    },
    deleteEvent: eventId => {
      // Implementation for deleting events
      console.log('Deleting event:', eventId);
    }
  };
})();
