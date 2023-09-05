<?php

namespace NovaResourceDynamicExport\Tests\CustomExport;

use NovaResourceDynamicExport\Models\ExportStoredFile;
use NovaResourceDynamicExport\Tests\Fixtures\Models\User;
use NovaResourceDynamicExport\Tests\TestCase;

class ListOfActionsTest extends TestCase
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
        ExportStoredFile::factory()
            ->count(3)
            ->create();

        $uriKey = \NovaResourceDynamicExport\Nova\Resources\ExportStoredFile::uriKey();

        $response = $this->get("nova-api/{$uriKey}/actions?" . http_build_query([
            ]));

        $this->assertIsArray($response->json('actions'));
        $this->assertCount(1, $response->json('actions'));

        $this->assertEquals('custom-exports', $response->json('actions.0.uriKey'));

        $this->assertEquals('export', $response->json('actions.0.fields.0.attribute'));
        $this->assertEquals('filename', $response->json('actions.0.fields.1.attribute'));
        $this->assertEquals('writer_type', $response->json('actions.0.fields.2.attribute'));


        $this->assertIsArray($response->json('actions.0.fields.0.options'));
        $this->assertCount(1, $response->json('actions.0.fields.0.options'));

        $array = [
            'PostsWithTagBreaking' => 'Posts With Tag Breaking',
        ];

        for ($i = 0; $i < count($array); $i++) {
            $this->assertEquals(array_keys($array)[$i], $response->json("actions.0.fields.0.options.{$i}.value"));
            $this->assertEquals(array_values($array)[$i], $response->json("actions.0.fields.0.options.{$i}.label"));
        }
    }
}
