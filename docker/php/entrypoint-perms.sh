#!/bin/sh
# Script à placer dans le conteneur PHP pour corriger ownership et permissions après chaque démarrage
chown -R www-data:www-data /var/www/html/web/sites/default/files
chmod -R 775 /var/www/html/web/sites/default/files
exec "$@"
