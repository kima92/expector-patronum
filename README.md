# ExpectorPatronum

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kima92/expector-patronum.svg?style=flat-square)](https://packagist.org/packages/kima92/expector-patronum)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/kima92/expector-patronum/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/kima92/expector-patronum/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/kima92/expector-patronum/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/kima92/expector-patronum/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kima92/expector-patronum.svg?style=flat-square)](https://packagist.org/packages/kima92/expector-patronum)

ExpectorPatronum is a Laravel-based system designed to manage and monitor task expectations and actual performances. It includes features like task scheduling, real-time monitoring, and integration with calendar interfaces for effective visualization and management.

## Support us

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Requirements
- PHP >= 8.2
- Laravel >= 9.0
- MySQL or a compatible database system

## Installation

You can install the package via composer:

```bash
composer require kima92/expector-patronum
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="expector-patronum-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="expector-patronum-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="expector-patronum-views"
```

## Usage

Configure a new Plan (1 time) via `/expector-patronum` path, or by code:
```php

$group = Group::query()->create(['name' => 'bla', 'color' => 'green']);
$expector = new Expector();

$plan = $expector->generatePlan('transmit 231', '0 20 * * *', $group, [['type' => StartedInTimeCheck::RULE_NAME]]);
```

Create next expectation days (this process is already scheduled every day to 20:00)
```php
$expector->generateNextExpectations(CarbonImmutable::create(2024), CarbonImmutable::create(2024, day: 2));
```

Run the task
```php
ExpectorPatronum::runTask('my Task', fn() => sleep(5));
```

### General configurations via AppServiceProvider::register

Authorization With:
```php
ExpectorPatronum::authWith(fn (Request $request) => !$this->app->environment('production') && $request->user())
```

Custom task identifier:
```php
ExpectorPatronum::setExpectationUuidResolver(fn () => Str::uuid()->toString())
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions to ExpectorPatronum are welcome. Please follow these steps to contribute:

1. Fork the repository.
2. Create a new branch for each feature or improvement.
3. Submit a pull request with a clear description of the changes.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [kima](https://github.com/kima92)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
