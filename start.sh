#!/bin/bash
# Script de inicio para PHP-FPM + Nginx

# Iniciar PHP-FPM en background
php-fpm -D

# Iniciar Nginx en foreground (mantiene el contenedor vivo)
nginx -g "daemon off;"
