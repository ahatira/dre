(function (Drupal) {
  'use strict';

  const onceFn = window.once || null;

  const fallbackClassScales = {
    dpe: [
      { label: 'A', color: '#009444', range_max: 70 },
      { label: 'B', color: '#44b749', range_max: 110 },
      { label: 'C', color: '#8dc63f', range_max: 180 },
      { label: 'D', color: '#fff200', range_max: 250 },
      { label: 'E', color: '#fcb913', range_max: 330 },
      { label: 'F', color: '#f47a20', range_max: 420 },
      { label: 'G', color: '#ed1c24', range_max: 9999 },
    ],
    ges: [
      { label: 'A', color: '#EDE7F6', range_max: 5 },
      { label: 'B', color: '#D1C4E9', range_max: 10 },
      { label: 'C', color: '#B39DDB', range_max: 20 },
      { label: 'D', color: '#9575CD', range_max: 35 },
      { label: 'E', color: '#7E57C2', range_max: 55 },
      { label: 'F', color: '#673AB7', range_max: 80 },
      { label: 'G', color: '#4A148C', range_max: 9999 },
    ],
  };

  function parseClasses(rawValue) {
    if (!rawValue) {
      return [];
    }

    try {
      const classes = JSON.parse(rawValue);
      return Array.isArray(classes) ? classes : [];
    }
    catch (error) {
      return [];
    }
  }

  function extractNumericValue(value) {
    const match = String(value).trim().match(/-?[0-9]+([\.,][0-9]+)?/);
    if (!match) {
      return null;
    }

    return Number.parseFloat(match[0].replace(',', '.'));
  }

  function detectClass(value, classes) {
    const numericValue = extractNumericValue(value);
    if (numericValue === null || classes.length === 0) {
      return null;
    }

    for (const classItem of classes) {
      if (classItem.range_max !== null && numericValue <= classItem.range_max) {
        return classItem;
      }
    }

    return classes[classes.length - 1] || null;
  }

  function fallbackClassesForWidget(fieldName) {
    if (fieldName.includes('field_diagnostics_dpe')) {
      return fallbackClassScales.dpe;
    }

    if (fieldName.includes('field_diagnostics_ges')) {
      return fallbackClassScales.ges;
    }

    return [];
  }

  function buildStatusElement(wrapper) {
    const preview = wrapper.querySelector('[data-ps-diagnostic-class-preview]');
    if (!preview) {
      return null;
    }

    let status = wrapper.querySelector('[data-ps-diagnostic-status]');
    if (status) {
      return status;
    }

    status = document.createElement('div');
    status.className = 'ps-diagnostic-widget__status';
    status.setAttribute('data-ps-diagnostic-status', '1');
    status.setAttribute('aria-live', 'polite');

    const badge = document.createElement('span');
    badge.className = 'ps-diagnostic-widget__badge';
    badge.setAttribute('data-ps-diagnostic-badge', '1');

    const text = document.createElement('span');
    text.className = 'ps-diagnostic-widget__status-text';
    text.setAttribute('data-ps-diagnostic-status-text', '1');

    status.appendChild(badge);
    status.appendChild(text);
    preview.prepend(status);

    return status;
  }

  function setStatus(wrapper, message, activeClass) {
    const status = buildStatusElement(wrapper);
    if (!status) {
      return;
    }

    const badge = status.querySelector('[data-ps-diagnostic-badge]');
    const text = status.querySelector('[data-ps-diagnostic-status-text]');

    if (badge) {
      badge.textContent = activeClass && activeClass.label ? activeClass.label : Drupal.t('Auto');
      badge.style.setProperty('--ps-diagnostic-badge-color', activeClass && activeClass.color ? activeClass.color : '');
    }

    if (text) {
      text.textContent = message;
    }

    wrapper.classList.toggle('ps-diagnostic-widget--auto', Boolean(activeClass));
  }

  function injectSlider(wrapper) {
    const sliderWrap = wrapper.querySelector('[data-ps-diagnostic-slider-wrap]');
    if (!sliderWrap || sliderWrap.querySelector('[data-ps-diagnostic-class-slider]')) {
      return;
    }

    const max = sliderWrap.dataset.psDiagnosticSliderMax || '1';
    const initial = sliderWrap.dataset.psDiagnosticSliderInitial || '1';
    const slider = document.createElement('input');
    slider.type = 'range';
    slider.min = '1';
    slider.max = max;
    slider.value = initial;
    slider.step = '1';
    slider.disabled = true;
    slider.className = 'ps-diagnostic-widget__class-slider';
    slider.setAttribute('data-ps-diagnostic-class-slider', '1');
    slider.setAttribute('aria-label', Drupal.t('Class slider preview'));
    sliderWrap.appendChild(slider);
  }

  function updateScaleColors(wrapper) {
    wrapper.querySelectorAll('[data-ps-class-label]').forEach((chip) => {
      const color = chip.getAttribute('data-ps-class-color') || '';
      chip.style.setProperty('--ps-class-color', color || '#8b95a1');
    });
  }

  function highlightScaleClass(wrapper, activeLabel) {
    wrapper.querySelectorAll('[data-ps-class-label]').forEach((chip) => {
      const chipLabel = chip.getAttribute('data-ps-class-label') || '';
      const isActive = activeLabel !== '' && chipLabel.toLowerCase() === activeLabel.toLowerCase();
      chip.classList.toggle('is-active', isActive);
    });
  }

  function updateClassSlider(wrapper, activeLabel) {
    const slider = wrapper.querySelector('[data-ps-diagnostic-class-slider]');
    if (!slider) {
      return;
    }

    const chips = Array.from(wrapper.querySelectorAll('[data-ps-class-label]'));
    if (chips.length === 0) {
      return;
    }

    let index = 1;
    if (activeLabel !== '') {
      const activeIndex = chips.findIndex((chip) => {
        const label = chip.getAttribute('data-ps-class-label') || '';
        return label.toLowerCase() === activeLabel.toLowerCase();
      });
      if (activeIndex >= 0) {
        index = activeIndex + 1;
      }
    }

    slider.max = String(chips.length);
    slider.value = String(index);
  }

  function updateWidget(wrapper) {
    const classInput = wrapper.querySelector('select[name$="[class]"], input[name$="[class]"], textarea[name$="[class]"]');
    const valueInput = wrapper.querySelector('input[name$="[value]"]');
    const nonApplicable = wrapper.querySelector('input[name$="[non_applicable]"]');
    const noClassification = wrapper.querySelector('input[name$="[no_classification]"]');

    if (!classInput || !valueInput) {
      return;
    }

    const classes = parseClasses(wrapper.dataset.psDiagnosticClasses || '[]');
    const activeClasses = classes.length > 0 ? classes : fallbackClassesForWidget(valueInput.name || '');

    const currentValue = valueInput.value || '';
    const currentClass = classInput.value || '';
    const previousAutoClass = wrapper.dataset.psDiagnosticAutoClass || '';
    const detectedClass = detectClass(currentValue, activeClasses);
    const detectedLabel = detectedClass ? String(detectedClass.label || '') : '';

    updateScaleColors(wrapper);

    if (detectedClass) {
      wrapper.style.setProperty('--ps-diagnostic-accent', detectedClass.color || '#5c6670');
    }

    if ((nonApplicable && nonApplicable.checked) || (noClassification && noClassification.checked)) {
      wrapper.dataset.psDiagnosticAutoClass = '';
      highlightScaleClass(wrapper, '');
      if (nonApplicable && nonApplicable.checked) {
        updateClassSlider(wrapper, '');
        setStatus(wrapper, Drupal.t('This diagnostic is marked as non applicable.'), null);
      }
      else {
        updateClassSlider(wrapper, '');
        setStatus(wrapper, Drupal.t('This diagnostic is marked as having no classification.'), null);
      }
      return;
    }

    if (currentValue.trim() === '') {
      if (currentClass !== '' && currentClass === previousAutoClass) {
        classInput.value = '';
      }
      wrapper.dataset.psDiagnosticAutoClass = '';
      highlightScaleClass(wrapper, '');
      updateClassSlider(wrapper, '');
      setStatus(wrapper, Drupal.t('Enter a value to auto-detect the class.'), null);
      return;
    }

    if (detectedLabel !== '') {
      if (currentClass === '' || currentClass === previousAutoClass) {
        classInput.value = detectedLabel;
        wrapper.dataset.psDiagnosticAutoClass = detectedLabel;
        highlightScaleClass(wrapper, detectedLabel);
        updateClassSlider(wrapper, detectedLabel);
        setStatus(wrapper, Drupal.t('Class auto-detected: @class', {'@class': detectedLabel}), detectedClass);
        return;
      }

      wrapper.dataset.psDiagnosticAutoClass = detectedLabel;
      highlightScaleClass(wrapper, currentClass);
      updateClassSlider(wrapper, currentClass);
      setStatus(wrapper, Drupal.t('Manual class override: @class', {'@class': currentClass}), detectedClass);
      return;
    }

    wrapper.dataset.psDiagnosticAutoClass = '';
    highlightScaleClass(wrapper, '');
    updateClassSlider(wrapper, '');
    setStatus(wrapper, Drupal.t('No automatic class could be inferred from the current value.'), null);
  }

  Drupal.behaviors.psDiagnosticAdminWidget = {
    attach(context) {
      const widgets = onceFn
        ? onceFn('ps-diagnostic-admin-widget', '.field--widget-diagnostic-item-default', context)
        : context.querySelectorAll('.field--widget-diagnostic-item-default');

      Array.from(widgets).forEach((wrapper) => {
        const classInput = wrapper.querySelector('select[name$="[class]"], input[name$="[class]"], textarea[name$="[class]"]');
        const valueInput = wrapper.querySelector('input[name$="[value]"]');
        const nonApplicable = wrapper.querySelector('input[name$="[non_applicable]"]');
        const noClassification = wrapper.querySelector('input[name$="[no_classification]"]');

        injectSlider(wrapper);
        updateScaleColors(wrapper);
        buildStatusElement(wrapper);
        updateWidget(wrapper);

        if (valueInput) {
          valueInput.addEventListener('blur', () => updateWidget(wrapper));
          valueInput.addEventListener('change', () => updateWidget(wrapper));
        }

        if (classInput) {
          classInput.addEventListener('change', () => {
            if (classInput.value !== (wrapper.dataset.psDiagnosticAutoClass || '')) {
              wrapper.dataset.psDiagnosticAutoClass = wrapper.dataset.psDiagnosticAutoClass || '';
            }
            updateWidget(wrapper);
          });
        }

        if (nonApplicable) {
          nonApplicable.addEventListener('change', () => updateWidget(wrapper));
        }

        if (noClassification) {
          noClassification.addEventListener('change', () => updateWidget(wrapper));
        }
      });
    }
  };
})(Drupal);
