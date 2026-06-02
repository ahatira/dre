import fs from 'fs';
import path from 'path';
import yaml from 'js-yaml';

const sourceDir = path.resolve('assets/icons/custom');
const outputDir = path.resolve('assets/icons');
const outputFile = path.join(outputDir, 'custom-icons.generated.yml');

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

const svgFiles = walk(sourceDir)
  .map((fullPath) => path.relative(sourceDir, fullPath).replace(/\\\\/g, '/'))
  .sort();

const data = {
  generated_at: new Date().toISOString(),
  source: 'work/icons',
  output: 'assets/icons/custom',
  count: svgFiles.length,
  icons: svgFiles,
};

fs.mkdirSync(outputDir, {recursive: true});
fs.writeFileSync(outputFile, yaml.dump(data, {lineWidth: 120}), 'utf8');

console.log(`Generated ${outputFile} with ${svgFiles.length} icon(s).`);
