<?php

namespace NovaResourceDynamicExport\Nova\Resources;

use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use NovaResourceDynamicExport\CustomResourcesExport;
use NovaResourceDynamicExport\Nova\Actions\CustomFileExports;
use ThinkStudio\HtmlField\Html;

/**
 * @extends Resource<\NovaResourceDynamicExport\Models\ExportStoredFile>
 */
class ExportStoredFile extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\NovaResourceDynamicExport\Models\ExportStoredFile>
     */
    public static $model = \NovaResourceDynamicExport\Models\ExportStoredFile::class;

    public static $title = 'name';

    public static $group = 'Export';

    public static $search = [
        'name',
    ];

    public static function label()
    {
        return __('Exported Files');
    }

    public function fields(NovaRequest $request)
    {
        return [
            ID::make(__('ID'), 'id')
                ->hideFromIndex()
                ->sortable(),

            Text::make(__('File Name'), 'name')
                ->sortable()
                ->showOnDetail()
                ->showOnIndex(),

            Text::make(__('Type'), 'disk')
                ->exceptOnForms(),

            DateTime::make(__('Created At'), 'created_at')
                ->sortable(),

            Html::make(__('Download Link'), function () {
                return view('nova-html-field::blocks.link', [
                    'href' => $this->download_link,
                ])->render();
            })
                ->clickable()
                ->hideWhenCreating()
                ->showOnIndex()
                ->showOnPreview(),
        ];
    }

    public function actions(NovaRequest $request)
    {
        $actions = [];

        if (!empty($exportsList = CustomResourcesExport::options())) {
            $actions[] = CustomFileExports::make()
                ->exportsList($exportsList)
                ->askForFilename()
                ->askForWriterType();
        }

        return $actions;
    }
}
