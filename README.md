# Laravel nova resources dynamic export export

![Packagist License](https://img.shields.io/packagist/l/think.studio/nova-resource-dynamic-export?color=%234dc71f)
[![Packagist Version](https://img.shields.io/packagist/v/think.studio/nova-resource-dynamic-export)](https://packagist.org/packages/think.studio/nova-resource-dynamic-export)
[![Total Downloads](https://img.shields.io/packagist/dt/think.studio/nova-resource-dynamic-export)](https://packagist.org/packages/think.studio/nova-resource-dynamic-export)
[![Build Status](https://scrutinizer-ci.com/g/dev-think-one/nova-resource-dynamic-export/badges/build.png?b=main)](https://scrutinizer-ci.com/g/dev-think-one/nova-resource-dynamic-export/build-status/main)
[![Code Coverage](https://scrutinizer-ci.com/g/dev-think-one/nova-resource-dynamic-export/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/dev-think-one/nova-resource-dynamic-export/?branch=main)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dev-think-one/nova-resource-dynamic-export/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/dev-think-one/nova-resource-dynamic-export/?branch=main)

Functionality to dynamically export resources.

| Nova | Package |
|------|---------|
| V4   | V1      |

## Installation

You can install the package via composer:

```bash
composer require think.studio/nova-resource-dynamic-export

# optional publish configs
php artisan vendor:publish --provider="NovaResourceDynamicExport\ServiceProvider" --tag="config"
```

Update filesystem configuration if you will used default storage disk.

```php
// config/filesystems.php
'exports'                            => [
    'driver' => 'local',
    'root'   => storage_path('app/exports'),
],
```

## Usage

### General export action

```php
public function actions(NovaRequest $request): array
{
    return [
        ExportResourceAction::make()
            ->askForFilename()
            ->askForWriterType()
            ->askForColumns([
                'id',
                'title' => 'Fund title',
                'publication_status',
                'description',
                'color_code',
                'selected_report',
            ])
            ->setPostReplaceFieldValuesWhenOnResource(function ($array, \App\Models\Fund $model, $only) {
                if (in_array('selected_report', $only)) {
                    $array['selected_report'] = $model->selectedReport->report_date?->format('Y-m-d');
                }
                return $array;
            }),
    ];
}
```

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/)
