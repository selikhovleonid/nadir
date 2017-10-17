# Nadir Framework

Yet another MVC PHP microframework.

## Конфигурирование веб-сервера

### Конфигурирование Apache2 (.htaccess или директива VirtualHost)

Инструкции для модуля mod_rewrite.

````
#...
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php
#...
````

### Конфигурирование Nginx

Инструкции для модуля ngx_http_rewrite_module.

````
server {
	#...
	location /  {
		index  index.php;
		if (!-e $request_filename) {
			rewrite ^(.*)$ /index.php last;
		}
	}
	#...
}
````