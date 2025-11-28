import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Read icons.css
const iconsCSS = fs.readFileSync(
  path.join(__dirname, '../source/props/icons.css'),
  'utf8'
);

// Extract all icon class names
const iconRegex = /\.icon-([a-z0-9-]+):before/g;
const icons = [];
let match;

while ((match = iconRegex.exec(iconsCSS)) !== null) {
  icons.push(`icon-${match[1]}`);
}

// Separate regular icons and POI icons
const regularIcons = icons.filter(icon => !icon.startsWith('icon-poi-'));
const poiIcons = icons.filter(icon => icon.startsWith('icon-poi-'));

console.log(`Found ${regularIcons.length} regular icons`);
console.log(`Found ${poiIcons.length} POI icons`);
console.log(`Total: ${icons.length} icons\n`);

// Output as JSON for use in stories
const output = {
  regular: regularIcons,
  poi: poiIcons,
  all: icons
};

fs.writeFileSync(
  path.join(__dirname, '../source/patterns/documentation/icons-list.json'),
  JSON.stringify(output, null, 2)
);

console.log('Icons list saved to source/patterns/documentation/icons-list.json');
