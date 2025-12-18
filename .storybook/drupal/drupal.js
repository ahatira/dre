import './drupalSettings';

// Simple Drupal.behaviors usage for Storybook

window.Drupal = { behaviors: {}, theme: {} };

((Drupal, drupalSettings) => {
  Drupal.throwError = (error) => {
    setTimeout(() => {
      throw error;
    }, 0);
  };

  Drupal.attachBehaviors = (context, settings) => {
    context = context || document;
    settings = settings || drupalSettings;
    const behaviors = Drupal.behaviors;

    Object.keys(behaviors).forEach((i) => {
      if (typeof behaviors[i].attach === 'function') {
        try {
          behaviors[i].attach(context, settings);
        } catch (e) {
          Drupal.throwError(e);
        }
      }
    });
  };

  Drupal.theme = (func, ...args) => {
    const funcName = func.replace(/[^a-zA-Z0-9_]/g, '_');
    
    // Check if a theme override exists
    if (Drupal.theme[funcName] !== undefined) {
      return Drupal.theme[funcName](...args);
    }
    
    // Return empty string if function not found
    return '';
  };
})(Drupal, window.drupalSettings);
