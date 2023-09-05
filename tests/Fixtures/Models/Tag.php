<?php

namespace NovaResourceDynamicExport\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use NovaResourceDynamicExport\Tests\Fixtures\Factories\TagFactory;

class Tag extends Model
{
    use HasFactory;

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(
            Post::class,
            'post_tag',
            'tag_id',
            'post_id',
            'id',
            'id',
        );
    }

    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }
}
