<?php

namespace NovaResourceDynamicExport\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use NovaResourceDynamicExport\Tests\Fixtures\Models\Post;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{

    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title'  => $this->faker->unique()->word(),
            'status' => 'draft',
        ];
    }

    public function published(): static
    {
        return $this->state([
            'status' => 'published',
        ]);
    }

    public function content(?string $content = null): static
    {
        return $this->state([
            'content' => $content,
        ]);
    }

    public function image(?string $image = null): static
    {
        return $this->state([
            'image' => $image,
        ]);
    }
}
