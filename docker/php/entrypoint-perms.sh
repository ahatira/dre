#!/bin/sh
# Script à placer dans le conteneur PHP pour corriger ownership et permissions après chaque démarrage
mkdir -p /var/www/html/web/sites/default/files/media-icons/generic
chown -R www-data:www-data /var/www/html/web/sites/default/
chmod -R 775 /var/www/html/web/sites/default/

chown -R www-data:www-data /var/www/html/vendor
chmod -R 775 /var/www/html/vendor
sudo chown -R www-data:www-data web/libraries
sudo chmod -R 775 web/libraries

exec "$@"
