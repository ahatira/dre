import fs from 'fs';
import path from 'path';
import {optimize} from 'svgo';

const sourceDir = path.resolve('work/icons');
const outputDir = path.resolve('assets/icons/custom');

const walk = (dir) => {
  if (!fs.existsSync(dir)) {
    return [];
  }

  const entries = fs.readdirSync(dir, {withFileTypes: true});
  const files = [];

  for (const entry of entries) {
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      files.push(...walk(full));
      continue;
    }

    if (entry.isFile() && full.endsWith('.svg')) {
      files.push(full);
    }
  }

  return files;
};

const svgFiles = walk(sourceDir);
if (svgFiles.length === 0) {
  console.log('No SVG found in work/icons, skipping svgo step.');
  process.exit(0);
}

for (const sourceFile of svgFiles) {
  const relativePath = path.relative(sourceDir, sourceFile);
  const targetFile = path.join(outputDir, relativePath);
  const targetDir = path.dirname(targetFile);
  fs.mkdirSync(targetDir, {recursive: true});

  const raw = fs.readFileSync(sourceFile, 'utf8');
  const optimized = optimize(raw, {
    path: sourceFile,
    multipass: true,
  });

  if ('data' in optimized) {
    fs.writeFileSync(targetFile, optimized.data, 'utf8');
  }
}

console.log(`Optimized ${svgFiles.length} SVG icon(s) into assets/icons/custom.`);
