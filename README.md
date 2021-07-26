# API Client for Validating Tax Number on GIB.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tarfin-labs/vkn-validation.svg?style=flat-square)](https://packagist.org/packages/tarfin-labs/vkn-validation)
[![Total Downloads](https://img.shields.io/packagist/dt/tarfin-labs/vkn-validation.svg?style=flat-square)](https://packagist.org/packages/tarfin-labs/vkn-validation)
![GitHub Actions](https://github.com/tarfin-labs/vkn-validation/actions/workflows/main.yml/badge.svg)

## Introduction
With this package you can get tax offices by city plates and validate tax numbers on GIB (Gelir İdaresi Başkanlığı).

> This package requires PHP `7.4` or higher.

## Installation

You can install the package via composer:

```bash
composer require tarfin-labs/vkn-validation
```

## Usage
#### Listing tax offices by city plate:
```php
use TarfinLabs\VknValidation\Validation;
use TarfinLabs\VknValidation\Exceptions\NotFoundException;

try {
    $offices = Validation::init()->getTaxOfficesByCityPlate(34);
} catch (NotFoundException $e) {
    echo $e->getMessage();
}
```

Output:
```
[
    [
        "code" => "034XXX",
        "name" => "TAX OFFICE NAME 1",
    ],
    [
        "code" => "034XXY",
        "name" => "TAX OFFICE NAME 2
    ],
]
```

#### Validating a tax number:

```php
use TarfinLabs\VknValidation\Validation;

try {
    $response = Validation::init()->validate(123123123, '034455');
    
    $response->isValid(); // boolean
    $response->getStatus(); // "1"
    $response->getTckn(); // ""
    $response->getStatusText(); // "FAAL"
    $response->getTaxNumber(); // "123123123"
    $response->getTaxOfficeNumber(); // "034455"
    $response->getCompanyTitle(); // "ACME INC."
} catch (\Throwable $e) {
    echo $e->getMessage();
}
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email development@tarfin.com instead of using the issue tracker.

## Credits

- [Turan Karatuğ](https://github.com/tkaratug)
- [Faruk Can](https://github.com/frkcn)
- [Yunus Emre Deligöz](https://github.com/deligoez)
- [Hakan Özdemir](https://github.com/hozdemir)
- [Caner Ergez](https://github.com/CanerErgez)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
