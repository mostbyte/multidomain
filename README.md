[![Laravel](https://img.shields.io/badge/Laravel-9.x-orange.svg?style=flat-square)](http://laravel.com)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://tldrlegal.com/license/mit-license)

# Multi Domain for Laravel
An extension for using Laravel in a multi subdomains setting

## Documentation

### Version Compatibility

| Laravel | Package |
|:--------|:--------|
| 9.x     | 1.x     |

### Installation
To get the latest version of `Mostbyte Multidomain`, simply require the project using [Composer](https://getcomposer.org)

```bash
  composer require mostbyte/multidomain
```
Instead, you may of course manually update your requirement block and run `composer update` if you so choose:
```json
{
  "require": {
    "mostbyte/multidomain": "^1.0"
  }
}
```

### Usage
There is a helper `mostbyteDomainManager`, that returns `DomainManager` and you can use all methods which created in it, for example:
```php
    $subDomain = mostbyteDomainManager()->getSubDomain();
```

| Warnings                                                                                                                     |
|:-----------------------------------------------------------------------------------------------------------------------------|
| Before using your app with this library, you need to create schemas with name, that equal with subdomain and run migrations! |
