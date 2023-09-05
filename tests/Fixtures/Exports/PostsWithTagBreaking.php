<?php

namespace NovaResourceDynamicExport\Tests\Fixtures\Exports;

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
     *
     * @return array
     */
    public function map($row): array
    {

        return [
            'title'   => $row->title,
            'content' => $row->content,
        ];
    }
}
