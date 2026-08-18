FROM php:8.2-apache
RUN a2enmod rewrite
COPY src/ /var/www/html/
# secret files used by command-injection / LFI / SSRF challenges
RUN echo 'VOLT{store_command_injection}' > /flag_cmdi.txt \
 && echo 'VOLT{store_lfi_path_traversal}' > /var/secret_lfi.txt \
 && echo 'VOLT{corp_lfi_traversal}' > /var/corp_secret.txt \
 && mkdir -p /var/www/html/data /var/www/html/store/uploads \
 && chown -R www-data:www-data /var/www/html/data /var/www/html/store/uploads \
 && chmod -R 0777 /var/www/html/data /var/www/html/store/uploads
EXPOSE 80
