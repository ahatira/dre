const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const sourceDir = path.join(root, 'node_modules', 'bootstrap', 'dist', 'js');
const destDir = path.join(root, 'assets', 'js', 'bootstrap');

const filesToCopy = [
  'bootstrap.min.js',
  'bootstrap.bundle.min.js',
];

fs.mkdirSync(destDir, { recursive: true });

for (const fileName of filesToCopy) {
  fs.copyFileSync(path.join(sourceDir, fileName), path.join(destDir, fileName));
}

console.log('Bootstrap JS copied to assets/js/bootstrap.');
