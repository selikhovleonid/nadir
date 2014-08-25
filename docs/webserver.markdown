Конфигурирование веб-сервера
============================

Конфигурирование Apache2 (.htaccess или директива VirtualHost)
--------------------------------------------------------------
````
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php
````

Конфигурирование Nginx
----------------------
````
if (!-e $request_filename) {
  rewrite ^(.*) /index.php last;
}
````