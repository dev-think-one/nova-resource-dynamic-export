<?php

namespace NovaResourceDynamicExport\Tests\ExportStoredFile;

use NovaResourceDynamicExport\Models\ExportStoredFile;
use NovaResourceDynamicExport\Tests\Fixtures\Models\User;
use NovaResourceDynamicExport\Tests\TestCase;

class ListOnIndexTest extends TestCase
{
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        $this->actingAs($this->admin);
    }

    /** @test */
    public function get_list()
    {
        $files = ExportStoredFile::factory()
            ->count(3)
            ->create();

        $uriKey = \NovaResourceDynamicExport\Nova\Resources\ExportStoredFile::uriKey();

        $response = $this->get("nova-api/{$uriKey}");

        $this->assertIsArray($response->json('resources'));
        $this->assertCount(3, $response->json('resources'));

        $this->assertEquals($files->get(1)->name, $response->json('resources.1.fields.0.value'));
    }
}
