[![Laravel](https://img.shields.io/badge/Laravel-9.x-orange.svg?style=flat-square)](http://laravel.com)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg?style=flat-square)](http://laravel.com)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-yellow.svg?style=flat-square)](http://laravel.com)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)

# Multi Domain for Laravel
An extension for using Laravel in a multi subdomains setting

## Documentation

### Version Compatibility

| Laravel | Package              |
|:--------|:---------------------|
| 9.x     | 1.x-2.x (deprecated) |
| 10.x    | 3.x-4.x              |
| 11.x    | 5.x                  |

### Installation

To get the latest version of `Mostbyte Multidomain`, simply require the project using [Composer](https://getcomposer.org)

```bash
composer require mostbyte/multidomain
```

Instead, you may of course manually update your requirement block and run `composer update` if you so choose:

```json
{
  "require": {
    "mostbyte/multidomain": "^5.0"
  }
}
```

### Publishing config files

```bash
php artisan vendor:publish --provider="Mostbyte\Multidomain\MultidomainServiceProvider"
```

### Usage
There is a helper `mostbyteDomainManager`, that returns `DomainManager` and you can use all methods which created in it, for example:

```php
$subDomain = mostbyteDomainManager()->getSubDomain();
```

### Console Commands Documentation
1) First of all you need to create new schema with command below
```bash
php artisan mostbyte:schema {schema}
```

2) Then you can run migration with following command and with all flags which exists in default Laravel ```migrate``` command

```bash
php mostbyte:migrate {schema}
                {--force : Force the operation to run when in production}
                {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
                {--pretend : Dump the SQL queries that would be run}
                {--seed : Indicates if the seed task should be re-run}
                {--step : Force the migrations to be run so they can be rolled back individually}
                {--all : Run migrations for all schemas}
```

3) Or there is the command for refreshing database
```bash
php mostbyte:fresh {schema}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--seed : Indicates if the seed task should be re-run}
        {--step : Force the migrations to be run so they can be rolled back individually}
```

4) If you want to ***DELETE*** the schema ***with all data in***, run this command
```bash
php artisan mostbyte:rollback {schema}
```
