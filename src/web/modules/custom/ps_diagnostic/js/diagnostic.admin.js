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

  function fallbackClassesForWidget(fieldName, typeId) {
    if (typeId && fallbackClassScales[typeId]) {
      return fallbackClassScales[typeId];
    }

    if (fieldName.includes('field_diagnostics_dpe')) {
      return fallbackClassScales.dpe;
    }

    if (fieldName.includes('field_diagnostics_ges')) {
      return fallbackClassScales.ges;
    }

    return [];
  }

  function parseClassesByType(rawValue) {
    if (!rawValue) {
      return {};
    }

    try {
      const classesByType = JSON.parse(rawValue);
      return classesByType && typeof classesByType === 'object' ? classesByType : {};
    }
    catch (error) {
      return {};
    }
  }

  function getSelectedTypeId(wrapper) {
    const typeSelect = wrapper.querySelector('select[name$="[diagnostic_type]"]');
    if (typeSelect) {
      return typeSelect.value || '';
    }

    const typeInput = wrapper.querySelector('input[name$="[diagnostic_type]"]');
    return typeInput ? (typeInput.value || '') : (wrapper.dataset.psDiagnosticTypeId || '');
  }

  function resolveClassesForWidget(wrapper, valueInput) {
    const typeId = getSelectedTypeId(wrapper);
    const classesByType = parseClassesByType(wrapper.dataset.psDiagnosticClassesByType || '{}');
    if (typeId && Array.isArray(classesByType[typeId]) && classesByType[typeId].length > 0) {
      return classesByType[typeId];
    }

    const classes = parseClasses(wrapper.dataset.psDiagnosticClasses || '[]');
    if (classes.length > 0) {
      return classes;
    }

    return fallbackClassesForWidget(valueInput ? (valueInput.name || '') : '', typeId);
  }

  function formatRangeText(rangeMin, rangeMax) {
    if (rangeMax !== null && rangeMax !== undefined && rangeMax !== '') {
      return `${rangeMin}–${rangeMax}`;
    }

    return `≥${rangeMin}`;
  }

  function rebuildScalePreview(wrapper, classes, activeLabel) {
    const preview = wrapper.querySelector('[data-ps-diagnostic-class-preview]');
    if (!preview) {
      return;
    }

    const sliderWrap = preview.querySelector('[data-ps-diagnostic-slider-wrap]');
    const scale = preview.querySelector('[data-ps-diagnostic-scale]');
    if (!sliderWrap || !scale) {
      return;
    }

    if (!Array.isArray(classes) || classes.length === 0) {
      scale.innerHTML = `<span class="ps-diagnostic-widget__scale-empty">${Drupal.t('No class scale configured.')}</span>`;
      sliderWrap.dataset.psDiagnosticSliderInitial = '1';
      sliderWrap.dataset.psDiagnosticSliderMax = '1';
      sliderWrap.querySelector('[data-ps-diagnostic-class-slider]')?.remove();
      return;
    }

    let rangeMin = 0;
    let sliderValue = 1;
    let classIndex = 1;
    const chips = classes.map((classItem) => {
      const label = String(classItem.label || '');
      if (label === '') {
        return '';
      }

      const rangeMax = classItem.range_max;
      const rangeText = formatRangeText(rangeMin, rangeMax);
      if (rangeMax !== null && rangeMax !== undefined && rangeMax !== '') {
        rangeMin = Number.parseInt(String(rangeMax), 10) + 1;
      }

      const isActive = activeLabel !== '' && label.toLowerCase() === activeLabel.toLowerCase();
      if (isActive) {
        sliderValue = classIndex;
      }

      classIndex += 1;

      return `<span class="ps-diagnostic-widget__scale-chip${isActive ? ' is-active' : ''}" data-ps-class-label="${label}" data-ps-class-color="${classItem.color || ''}">`
        + `<strong class="ps-diagnostic-widget__chip-label">${label}</strong>`
        + `<small class="ps-diagnostic-widget__chip-range">${rangeText}</small>`
        + '</span>';
    }).join('');

    scale.innerHTML = chips;
    sliderWrap.dataset.psDiagnosticSliderInitial = String(sliderValue);
    sliderWrap.dataset.psDiagnosticSliderMax = String(Math.max(1, classes.length));
    sliderWrap.querySelector('[data-ps-diagnostic-class-slider]')?.remove();
    injectSlider(wrapper);
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
    const classInput = wrapper.querySelector('[data-ps-diagnostic-class-input], select[name$="[class]"], input[name$="[class]"], textarea[name$="[class]"]');
    const valueInput = wrapper.querySelector('input[name$="[value]"]');
    const nonApplicable = wrapper.querySelector('input[name$="[non_applicable]"]');
    const noClassification = wrapper.querySelector('input[name$="[no_classification]"]');

    if (!classInput || !valueInput) {
      return;
    }

    const activeClasses = resolveClassesForWidget(wrapper, valueInput);
    const typeId = getSelectedTypeId(wrapper);
    const scaleKey = `${typeId}:${activeClasses.map((classItem) => classItem.label).join('|')}`;
    if (wrapper.dataset.psDiagnosticScaleKey !== scaleKey) {
      rebuildScalePreview(wrapper, activeClasses, classInput.value || '');
      wrapper.dataset.psDiagnosticScaleKey = scaleKey;
    }

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
        ? onceFn('ps-diagnostic-admin-widget', '[data-ps-diagnostic-widget]', context)
        : context.querySelectorAll('[data-ps-diagnostic-widget]');

      Array.from(widgets).forEach((wrapper) => {
        const classInput = wrapper.querySelector('[data-ps-diagnostic-class-input], select[name$="[class]"], input[name$="[class]"], textarea[name$="[class]"]');
        const valueInput = wrapper.querySelector('input[name$="[value]"]');
        const typeSelect = wrapper.querySelector('select[name$="[diagnostic_type]"]');
        const nonApplicable = wrapper.querySelector('input[name$="[non_applicable]"]');
        const noClassification = wrapper.querySelector('input[name$="[no_classification]"]');

        injectSlider(wrapper);
        updateScaleColors(wrapper);
        buildStatusElement(wrapper);
        updateWidget(wrapper);

        if (valueInput) {
          valueInput.addEventListener('input', () => updateWidget(wrapper));
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

        if (typeSelect) {
          typeSelect.addEventListener('change', () => updateWidget(wrapper));
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
