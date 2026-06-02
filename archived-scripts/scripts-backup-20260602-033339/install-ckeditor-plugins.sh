#!/bin/bash

# Script to install CKEditor 5 plugins that are not available via npm
# These plugins are specific to the ckeditor_media_embed Drupal module

set -e

echo "=== Installing CKEditor 5 Plugins ==="
echo ""

# Change to web directory
cd "$(dirname "$0")/../web"

# Install media-embed plugin
echo "Installing media-embed plugin..."
../vendor/bin/drush ckeditor_media_embed:install -y

echo ""
echo "=== CKEditor plugins installation complete ==="
echo ""

# Display installed plugins
echo "Installed plugins:"
find libraries/ckeditor5/plugins -maxdepth 1 -type d | tail -n +2 | xargs -I {} basename {}
