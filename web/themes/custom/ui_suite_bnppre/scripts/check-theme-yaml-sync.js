const fs = require('fs');
const path = require('path');
const yaml = require('js-yaml');

const root = path.resolve(__dirname, '..');

const files = {
  libraries: path.join(root, 'ui_suite_bnppre.libraries.yml'),
  info: path.join(root, 'ui_suite_bnppre.info.yml'),
  breakpoints: path.join(root, 'ui_suite_bnppre.breakpoints.yml'),
  skinsThemes: path.join(root, 'ui_suite_bnppre.ui_skins.themes.yml'),
  icons: path.join(root, 'ui_suite_bnppre.icons.yml'),
};

const errors = [];
const warnings = [];

function readYaml(filePath) {
  const content = fs.readFileSync(filePath, 'utf8');
  return yaml.load(content);
}

function hasFile(relativePath) {
  return fs.existsSync(path.join(root, relativePath));
}

function collectSvgFiles(dir, out = []) {
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      collectSvgFiles(fullPath, out);
      continue;
    }

    if (entry.isFile() && entry.name.toLowerCase().endsWith('.svg')) {
      out.push(fullPath);
    }
  }

  return out;
}

function validateLibraries(libraries) {
  if (!libraries.framework_js || !libraries.framework_js.js) {
    errors.push('Missing framework_js library declaration.');
    return;
  }

  const frameworkJsFiles = Object.keys(libraries.framework_js.js);
  if (!frameworkJsFiles.includes('assets/js/bootstrap/bootstrap.bundle.min.js')) {
    errors.push('framework_js must point to assets/js/bootstrap/bootstrap.bundle.min.js.');
  }

  for (const library of Object.values(libraries)) {
    if (!library || typeof library !== 'object') {
      continue;
    }

    if (library.js && typeof library.js === 'object') {
      for (const jsPath of Object.keys(library.js)) {
        if (!jsPath.startsWith('assets/')) {
          continue;
        }
        if (!hasFile(jsPath)) {
          errors.push(`Missing JS asset referenced in libraries.yml: ${jsPath}`);
        }
      }
    }

    if (library.css && typeof library.css === 'object') {
      for (const group of Object.values(library.css)) {
        if (!group || typeof group !== 'object') {
          continue;
        }
        for (const cssPath of Object.keys(group)) {
          if (!cssPath.startsWith('assets/')) {
            continue;
          }
          if (!hasFile(cssPath)) {
            errors.push(`Missing CSS asset referenced in libraries.yml: ${cssPath}`);
          }
        }
      }
    }
  }
}

function validateInfo(info) {
  const libraries = Array.isArray(info.libraries) ? info.libraries : [];
  if (!libraries.includes('ui_suite_bnppre/bootstrap')) {
    errors.push('ui_suite_bnppre/bootstrap should be included in info.yml libraries.');
  }

  const overrides = info['libraries-override'] || {};
  const activeLinkOverride =
    overrides['core/drupal.active-link'] &&
    overrides['core/drupal.active-link'].js &&
    overrides['core/drupal.active-link'].js['misc/active-link.js'];

  if (activeLinkOverride !== 'assets/js/misc/active-link.js') {
    errors.push('core/drupal.active-link override should target assets/js/misc/active-link.js.');
  } else if (!hasFile(activeLinkOverride)) {
    errors.push('Missing active-link override target file: assets/js/misc/active-link.js');
  }
}

function validateBreakpoints(breakpoints) {
  const keys = Object.keys(breakpoints || {});
  if (keys.length === 0) {
    errors.push('Breakpoints file is empty.');
    return;
  }

  const expectedMediaQueries = {
    'screen-xs-max': 'all and (max-width: 575px)',
    'screen-sm-min': 'all and (min-width: 576px)',
    'screen-sm-max': 'all and (max-width: 767px)',
    'screen-md-min': 'all and (min-width: 768px)',
    'screen-md-max': 'all and (max-width: 991px)',
    'screen-lg-min': 'all and (min-width: 992px)',
    'screen-lg-max': 'all and (max-width: 1199px)',
    'screen-xl-min': 'all and (min-width: 1200px)',
    'screen-xl-max': 'all and (max-width: 1399px)',
    'screen-xxl-min': 'all and (min-width: 1400px)',
  };

  for (const [suffix, mediaQuery] of Object.entries(expectedMediaQueries)) {
    const canonicalId = `ui_suite_bnppre.${suffix}`;

    if (!breakpoints[canonicalId]) {
      errors.push(`Missing breakpoint entry: ${canonicalId}`);
      continue;
    }

    if (breakpoints[canonicalId].mediaQuery !== mediaQuery) {
      errors.push(`Breakpoint ${canonicalId} has unexpected mediaQuery. Expected: ${mediaQuery}`);
    }
  }

  const legacyKeys = Object.keys(breakpoints).filter((k) => k.startsWith('ui_suite_bnppre.'));
  if (legacyKeys.length > 0) {
    errors.push(
      `Legacy breakpoint aliases must be removed: ${legacyKeys.join(', ')}`,
    );
  }
}

function validateSkinsThemes(themes) {
  const requiredThemes = ['dark', 'light'];
  for (const themeId of requiredThemes) {
    if (!themes[themeId]) {
      errors.push(`Missing ui_skins theme entry: ${themeId}`);
      continue;
    }

    if (themes[themeId].key !== 'data-bs-theme') {
      errors.push(`ui_skins theme ${themeId} should use key: data-bs-theme`);
    }
  }
}

function validateIconsMetadata(icons) {
  const bnppre = icons.bnppre;
  if (!bnppre || typeof bnppre !== 'object') {
    errors.push('Missing bnppre icon pack declaration in ui_suite_bnppre.icons.yml.');
    return;
  }

  const iconDir = path.join(root, 'assets', 'icons');
  if (!fs.existsSync(iconDir)) {
    errors.push('Missing assets/icons directory for bnppre icon pack.');
    return;
  }

  const svgFiles = collectSvgFiles(iconDir);
  const description = String(bnppre.description || '');
  const countMatch = description.match(/(\d+)\s+icons?/i);

  if (!countMatch) {
    warnings.push('bnppre description does not include an icon count.');
    return;
  }

  const declaredCount = Number.parseInt(countMatch[1], 10);
  if (declaredCount !== svgFiles.length) {
    errors.push(
      `bnppre description count mismatch: declared ${declaredCount}, found ${svgFiles.length} SVG files in assets/icons.`,
    );
  }
}

function run() {
  const libraries = readYaml(files.libraries);
  const info = readYaml(files.info);
  const breakpoints = readYaml(files.breakpoints);
  const skinsThemes = readYaml(files.skinsThemes);
  const icons = readYaml(files.icons);

  validateLibraries(libraries);
  validateInfo(info);
  validateBreakpoints(breakpoints);
  validateSkinsThemes(skinsThemes);
  validateIconsMetadata(icons);

  if (warnings.length > 0) {
    console.warn('\nWarnings:');
    for (const warning of warnings) {
      console.warn(`- ${warning}`);
    }
  }

  if (errors.length > 0) {
    console.error('\nTheme YAML sync check failed:');
    for (const error of errors) {
      console.error(`- ${error}`);
    }
    process.exit(1);
  }

  console.log('Theme YAML sync check passed.');
}

run();
