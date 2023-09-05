<?php

namespace NovaResourceDynamicExport\Tests\ExportStoredFile;

use Illuminate\Support\Facades\Storage;
use NovaResourceDynamicExport\Models\ExportStoredFile;
use NovaResourceDynamicExport\Tests\Fixtures\Models\User;
use NovaResourceDynamicExport\Tests\TestCase;

class DownloadExportStoredFileTest extends TestCase
{
    protected string $disk = 'exports';
    protected string $path = 'foo/test.txt';
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::disk($this->disk)->put($this->path, 'fake');

        $this->admin = User::factory()->create();


    }

    /** @test */
    public function download()
    {
        $dbExport = ExportStoredFile::factory()->create([
            'disk' => $this->disk,
            'path' => $this->path,
            'name' => 'my.file.csv',
        ]);

        $response = $this->actingAs($this->admin)
            ->get($dbExport->downloadLink);

        $response->assertStreamedContent('fake');
        $this->assertStringContainsString('my.file.csv', $response->headers->get('content-disposition'));
    }

    /** @test */
    public function delete_file_no_delete_model()
    {
        $dbExport = ExportStoredFile::factory()->create([
            'disk' => $this->disk,
            'path' => $this->path,
            'name' => 'my.file.csv',
        ]);

        Storage::disk($this->disk)->assertExists($this->path);

        $dbExport->delete();

        Storage::disk($this->disk)->assertMissing($this->path);

    }
}
