# NL SESSION API | Lumen PHP Framework

## Install Composer
#### Composer - [Download](https://getcomposer.org/download/).

To quickly install Composer in the current directory, run the following script in your terminal. To automate the installation, use the [guide on installing Composer programmatically](https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md).

```php
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'e5325b19b381bfd88ce90a5ddb7823406b2a38cff6bb704b0acc289a09c8128d4a8ce2bbafcd1fcbdc38666422fe2806') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```

To use composer for anywhere use this command.
```
chmod a+x composer.phar
sudo mv composer.phar /usr/local/bin/composer.phar
```

## Install Dependencies
Run this command line to install all Project Dependecies
```
composer install
```

## Create Environment Variables 
Create a file named `.env`. Inside it add the environment variables that will be used. Ex:
```
PORT=8000
APP_NAME=Example
APP_DEBUG=true
...
```

## Inicialize Project
To Inicialize this Project run this command line:
```
composer start
```
This command is equivalent to:
```
php -S localhost:8000 -t public
```

<!--
[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework) 
[![Total Downloads](https://poser.pugx.org/laravel/lumen-framework/d/total.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/lumen-framework/v/stable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://poser.pugx.org/laravel/lumen-framework/license.svg)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

## Official Documentation

Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).

## Contributing

Thank you for considering contributing to Lumen! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Lumen, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
-->