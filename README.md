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

And add resource to your admin:

```php
// Providers/NovaServiceProvider.php
protected function resources(): void
{
    parent::resources();
    Nova::resources([
        \NovaResourceDynamicExport\Nova\Resources\ExportStoredFile::class,
    ]);
}
```

`Please do not forget add policies for \NovaResourceDynamicExport\Models\ExportStoredFile model or your custom model`

## Usage

### General resources export action

```php
public function actions(NovaRequest $request): array
{
    return [
        \NovaResourceDynamicExport\Nova\Actions\ExportResourceAction::make()
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

### Custom specified export

Firstly create custom export class

```php
// Exports/PostsWithTagBreaking.php
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use NovaResourceDynamicExport\Export\CustomExport;
use NovaResourceDynamicExport\Tests\Fixtures\Models\Post;

class PostsWithTagBreaking extends CustomExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return Post::query()
            ->whereHas('tags', fn (Builder $q) => $q->where('name', 'Breaking'));
    }

    public function headings(): array
    {
        return [
            'Title',
            'content',
        ];
    }

    /**
     * @param Post $row
     */
    public function map($row): array
    {

        return [
            'title'   => $row->title,
            'content' => $row->content,
        ];
    }
}
```

Then add this class using any service provider:

```php
// Providers/NovaServiceProvider.php
public function boot(): void
{
    parent::boot();

    CustomResourcesExport::use(PostsWithTagBreaking::class);
}
```

THis is all, not in ExportStoredFile resource index you will see new action to run custom exports

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/)
