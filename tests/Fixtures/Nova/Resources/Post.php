<?php

namespace NovaResourceDynamicExport\Tests\Fixtures\Nova\Resources;

use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\BooleanGroup;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use NovaResourceDynamicExport\Nova\Actions\ExportResourceAction;

/**
 * @extends Resource<\NovaResourceDynamicExport\Tests\Fixtures\Models\Post>
 */
class Post extends Resource
{

    public static $model = \NovaResourceDynamicExport\Tests\Fixtures\Models\Post::class;

    public static $title = 'title';

    public function fields(NovaRequest $request): array
    {
        return [
            Text::make('Title', 'title'),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            ExportResourceAction::make()
                ->askForFilename()
                ->askForWriterType()
                ->askForColumns([
                    'title',
                    'content' => 'Post full content',
                    'image',
                    'status',
                    'tags' => 'Tags list',
                ], 'What columns', fn (BooleanGroup $field) => $field->placeholder('FooBar'))
                ->setPostReplaceFieldValuesWhenOnResource(function ($array, \NovaResourceDynamicExport\Tests\Fixtures\Models\Post $model, $only) {
                    if (in_array('tags', $only)) {
                        $array['tags'] = $model->tags->pluck('name')->implode('|');
                    }

                    return $array;
                }),
            ExportResourceAction::make()
                ->withUriKey('custom_uri')
                ->askForWriterType()
                ->onSuccess(function ($request, $response) {
                    return Action::message(__('Done :).'));
                }),
            ExportResourceAction::make()
                ->withUriKey('incorrect_disc_uri')
                ->askForWriterType()
                ->withDisk('bla-fake-bla')
                ->onFailure(function ($request, $response) {
                    return Action::message(__('Error Foo'));
                }),
        ];
    }
}
