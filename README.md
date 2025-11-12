# Laravel Multidomain – Multi-tenancy package for Laravel applications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mostbyte/multidomain.svg?style=flat-square)](https://packagist.org/packages/mostbyte/multidomain)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mostbyte/multidomain/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mostbyte/multidomain/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mostbyte/multidomain/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mostbyte/multidomain/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mostbyte/multidomain.svg?style=flat-square)](https://packagist.org/packages/mostbyte/multidomain)

This package provides full-featured multi-tenancy support for Laravel applications. It allows you to create and manage isolated database schemas or connections per tenant, simplifying the development of SaaS and modular systems.

## Installation

You can install the package via composer:

```bash
composer require mostbyte/multidomain
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="multidomain-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="multidomain-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="multidomain-views"
```

## Usage

```php
use Mostbyte\Multidomain\Facades\Multidomain;

Multidomain::setTenant('tenant_1');
// Your tenant-specific logic here
```

### Example Workflow

1. Create a new schema for a tenant:
   ```bash
   php artisan schema:migrate schema
   ```
2. Run migrations for that tenant:
   ```bash
   php artisan schema:migrate migrate
   ```
3. All tenant-specific models and queries will automatically be scoped to the active schema.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mostbyte Team](https://github.com/mostbyte)
- [Jasur Dustmurodov](https://github.com/dorsone)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
