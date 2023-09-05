<?php

namespace NovaResourceDynamicExport\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use NovaResourceDynamicExport\Tests\Fixtures\Factories\PostFactory;

class Post extends Model
{
    use HasFactory;

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'post_tag',
            'post_id',
            'tag_id',
            'id',
            'id',
        );
    }

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }
}
