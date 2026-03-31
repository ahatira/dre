const fs = require('fs');
const path = require('path');

const themeRoot = path.resolve(__dirname, '..');
const projectRoot = path.resolve(themeRoot, '..', '..', '..', '..');

const sourceIcons = path.join(themeRoot, 'node_modules', 'bootstrap-icons', 'icons');
const destIcons = path.join(projectRoot, 'web', 'libraries', 'bootstrap-icons', 'icons');

// Create destination directory if it doesn't exist
fs.mkdirSync(destIcons, { recursive: true });

// Copy all SVG icons
fs.cpSync(sourceIcons, destIcons, { recursive: true });

console.log(`Bootstrap Icons copied to web/libraries/bootstrap-icons/icons (${fs.readdirSync(destIcons).length} files).`);
