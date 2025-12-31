# Dockerfile para la aplicación Clínica Dental
# Usando PHP-FPM + Nginx para mejor rendimiento
FROM php:8.2-fpm

# Instalar Nginx y extensiones de PHP necesarias para MySQL
RUN apt-get update && apt-get install -y \
    nginx \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copiar configuración de Nginx
COPY nginx.conf /etc/nginx/sites-available/default

# Copiar el código de la aplicación
COPY . /var/www/html/

# Copiar script de inicio
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html

# Exponer puerto 80
EXPOSE 80

# Ejecutar PHP-FPM y Nginx
CMD ["/start.sh"]
