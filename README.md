# Lumen PHP Framework

### Documentación oficial

La documentación para Lumen puede encontrar en el [sitio web de Lumen](http://lumen.laravel.com/docs).

### Licencia

Lumen es un software de código abierto con licencia bajo [licencia MIT](http://opensource.org/licenses/MIT)

### Comandos

#### Migraciones

Crear una nueva migración:

```bash
php artisan make:migration create_users_table --table=users
```

Para limpiar la base de datos y migrar las tablas:

```bash
php artisan migrate:refresh --seed
```

Para migrar las tablas sobre lo que ya existe:

```bash
php artisan migrate
```

#### Modelos

Para crear un nuevo modelo:

```bash
php artisan make:model MyModelo
```

#### OAuth

Este comando creará las claves de cifrado necesarias para generar tokens de acceso seguro. Además, el comando creará clientes de "acceso personal" y "concesión de contraseña" que se usarán para generar tokens de acceso.

```bash
php artisan passport:install
```

Comando de la consola para purgar tokens caducados.


```bash
php artisan passport:purge
```

Para recargar el auto load.

```bash
composer dump-autoload
```


## Perros del Agua back-end

Para ejecutar servidor de pruevas:

```bash
php -S localhost:8000 -t public
```

Migergar los modelos de la base de datos:

```bash
php artisan migrate
```

Para actualizar dependencias:

```bash
composer update
```

## Librerias

```bash
composer require illuminate/notifications
composer require illuminate/mail
composer require guzzlehttp/guzzle
composer require illuminate/support
composer require laravelcollective/html
composer require intervention/image
composer require vluzrmos/lumen-cors
composer require brozot/laravel-fcm
composer require nesbot/carbon
composer require laravel/passport
composer require dusterio/lumen-passport
```

## Credenciales

Ssh hostinger

```bash
ssh u736574120@31.170.166.166 -p 65002
```

Ftp hostinger

* IP FTP: 31.170.166.184
* Puerto FTP: 21
* Usuario FTP: u736574120

```bash
git clone ssh://u736574120@quesidogo.com:65002/home/u736574120/develop/big_thinks_back/
```