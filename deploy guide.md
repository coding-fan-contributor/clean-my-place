#!/bin/sh
rm -rf clean-my-place
git clone https://github.com/coding-fan-contributor/clean-my-place.git
sudo cp -ruf clean-my-place/. /var/www/html
cd /var/www/html
php artisan optimize:clear
php artisan optimize