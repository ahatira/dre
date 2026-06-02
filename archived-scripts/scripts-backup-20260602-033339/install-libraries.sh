#!/bin/bash

# Script to copy JavaScript libraries from node_modules to web/libraries/
# Inspired by https://github.com/rlhawk/drupal-libraries-npm

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

echo "🧹 Cleaning web/libraries directory..."
rm -rf web/libraries/ace
mkdir -p web/libraries/ace

echo "📦 Copying Ace Editor library..."
if [ ! -d "node_modules/ace-builds" ]; then
    echo "❌ Error: ace-builds not found in node_modules. Run 'npm install' first."
    exit 1
fi

# Copy all necessary Ace Editor files
cp -v node_modules/ace-builds/src-min-noconflict/ace.js web/libraries/ace/
cp -v node_modules/ace-builds/src-min-noconflict/ext-*.js web/libraries/ace/ 2>/dev/null || true
cp -v node_modules/ace-builds/src-min-noconflict/mode-*.js web/libraries/ace/
cp -v node_modules/ace-builds/src-min-noconflict/theme-*.js web/libraries/ace/
cp -v node_modules/ace-builds/src-min-noconflict/worker-*.js web/libraries/ace/

# Copy snippets
mkdir -p web/libraries/ace/snippets
cp -v node_modules/ace-builds/src-min-noconflict/snippets/*.js web/libraries/ace/snippets/ 2>/dev/null || true

echo "✅ Ace Editor library installed successfully!"
echo "📂 Location: web/libraries/ace/"
echo "📊 Files copied:"
ls -lh web/libraries/ace/ | head -10
echo "   ... and more"
