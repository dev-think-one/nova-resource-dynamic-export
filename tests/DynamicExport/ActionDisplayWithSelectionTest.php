<?php

namespace NovaResourceDynamicExport\Tests\DynamicExport;

use NovaResourceDynamicExport\Tests\Fixtures\Models\Post;
use NovaResourceDynamicExport\Tests\Fixtures\Models\Tag;
use NovaResourceDynamicExport\Tests\Fixtures\Models\User;
use NovaResourceDynamicExport\Tests\TestCase;

class ActionDisplayWithSelectionTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $this->actingAs($this->admin);
    }

    /** @test */
    public function get_action()
    {
        $post = Post::factory()
            ->has(Tag::factory()->count(5))
            ->create();

        $uriKey = \NovaResourceDynamicExport\Tests\Fixtures\Nova\Resources\Post::uriKey();

        $response = $this->get("nova-api/{$uriKey}/actions?" . http_build_query([
                'resourceId' => $post->getKey(),
                'display'    => 'detail',
            ]));

        $this->assertIsArray($response->json('actions'));
        $this->assertCount(3, $response->json('actions'));

        $this->assertEquals('filename', $response->json('actions.0.fields.0.attribute'));
        $this->assertEquals('writer_type', $response->json('actions.0.fields.1.attribute'));
        $this->assertEquals('columns', $response->json('actions.0.fields.2.attribute'));
        $this->assertEquals('What columns', $response->json('actions.0.fields.2.name'));
        $this->assertEquals('FooBar', $response->json('actions.0.fields.2.placeholder'));

        $this->assertIsArray($response->json('actions.0.fields.2.options'));
        $this->assertCount(5, $response->json('actions.0.fields.2.options'));

        $array = [
            'title'   => 'Title',
            'content' => 'Post full content',
            'image'   => 'Image',
            'status'  => 'Status',
            'tags'    => 'Tags list',
        ];

        for ($i = 0; $i < count($array); $i++) {
            $this->assertEquals(array_keys($array)[$i], $response->json("actions.0.fields.2.options.{$i}.name"));
            $this->assertEquals(array_values($array)[$i], $response->json("actions.0.fields.2.options.{$i}.label"));
        }
    }
}
