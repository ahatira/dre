import {existsSync, readdirSync, writeFileSync} from 'node:fs';
import {join} from 'node:path';

const sourceDir = join(process.cwd(), 'work/fonts/custom/bnp-sans');
const outputFile = join(process.cwd(), 'work/styles/scss/framework/_custom-fonts-generated.scss');

const variants = [
  {filename: 'bnp-sans-light', family: 'BNP Sans', weight: 300, style: 'normal'},
  {filename: 'bnp-sans-light-italic', family: 'BNP Sans', weight: 300, style: 'italic'},
  {filename: 'bnp-sans-regular', family: 'BNP Sans', weight: 400, style: 'normal'},
  {filename: 'bnp-sans-italic', family: 'BNP Sans', weight: 400, style: 'italic'},
  {filename: 'bnp-sans-semibold', family: 'BNP Sans', weight: 600, style: 'normal'},
  {filename: 'bnp-sans-semibold-italic', family: 'BNP Sans', weight: 600, style: 'italic'},
  {filename: 'bnp-sans-bold', family: 'BNP Sans', weight: 700, style: 'normal'},
  {filename: 'bnp-sans-bold-italic', family: 'BNP Sans', weight: 700, style: 'italic'},
  {filename: 'bnp-sans-extrabold', family: 'BNP Sans', weight: 800, style: 'normal'},

  // Condensed is a distinct family. Only light is available today; regular/bold
  // are prepared and will auto-activate as soon as files are added.
  {filename: 'bnp-sans-condensed-light', family: 'BNP Sans Condensed', weight: 300, style: 'normal'},
  {filename: 'bnp-sans-condensed-light-italic', family: 'BNP Sans Condensed', weight: 300, style: 'italic'},
  {filename: 'bnp-sans-condensed-regular', family: 'BNP Sans Condensed', weight: 400, style: 'normal'},
  {filename: 'bnp-sans-condensed-italic', family: 'BNP Sans Condensed', weight: 400, style: 'italic'},
  {filename: 'bnp-sans-condensed-bold', family: 'BNP Sans Condensed', weight: 700, style: 'normal'},
  {filename: 'bnp-sans-condensed-bold-italic', family: 'BNP Sans Condensed', weight: 700, style: 'italic'},
];

const existingFiles = existsSync(sourceDir) ? new Set(readdirSync(sourceDir)) : new Set();

const rules = variants.flatMap(({filename, family, weight, style}) => {
  const formats = ['woff2', 'woff'].filter((extension) => existingFiles.has(`${filename}.${extension}`));

  if (!formats.length) {
    return [];
  }

  const src = formats
    .map((extension) => `url('../../fonts/custom/bnp-sans/${filename}.${extension}') format('${extension}')`)
    .join(', ');

  return [
    '  @font-face {',
    `    font-family: '${family}';`,
    `    src: ${src};`,
    `    font-style: ${style};`,
    `    font-weight: ${weight};`,
    "    font-display: swap;",
    '  }',
  ];
});

const content = [
  '@mixin generated-custom-font-faces() {',
  ...(rules.length ? rules : ['  // No local BNP Sans files detected in work/fonts/custom/bnp-sans.']),
  '}',
  '',
].join('\n');

writeFileSync(outputFile, content, 'utf8');
console.log(`Generated ${outputFile}`);
