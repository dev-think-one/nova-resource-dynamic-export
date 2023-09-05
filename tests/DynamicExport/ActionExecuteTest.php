<?php

namespace NovaResourceDynamicExport\Tests\DynamicExport;

use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Http\Requests\NovaRequest;
use NovaResourceDynamicExport\Models\ExportStoredFile;
use NovaResourceDynamicExport\Nova\Actions\ExportResourceAction;
use NovaResourceDynamicExport\Tests\Fixtures\Models\Post;
use NovaResourceDynamicExport\Tests\Fixtures\Models\Tag;
use NovaResourceDynamicExport\Tests\Fixtures\Models\User;
use NovaResourceDynamicExport\Tests\TestCase;

class ActionExecuteTest extends TestCase
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
            ->content('Example')
            ->image('foo/bar.png')
            ->create();

        $uriKey          = \NovaResourceDynamicExport\Tests\Fixtures\Nova\Resources\Post::uriKey();
        $contactResource = new \NovaResourceDynamicExport\Tests\Fixtures\Nova\Resources\Post($post);
        /** @var ExportResourceAction $resourceAction */
        $resourceAction = $contactResource->actions(app(NovaRequest::class))[0];

        $this->assertEquals(0, ExportStoredFile::query()->count());

        $response = $this->post("nova-api/{$uriKey}/action?action={$resourceAction->uriKey()}", [
            'resources'   => $post->getKey(),
            'filename'    => 'foo-bar',
            'writer_type' => 'Csv',
            'columns'     => json_encode([
                'title'   => true,
                'content' => false,
                'image'   => true,
                'status'  => false,
                'tags'    => true,
            ]),
        ]);

        $response->assertSuccessful();
        $response->assertJsonPath('message', 'Data exported to file.');

        $this->assertEquals(1, ExportStoredFile::query()->count());

        /** @var ExportStoredFile $file */
        $file = ExportStoredFile::query()->first();

        Storage::disk($file->disk)->assertExists($file->path);

        $content = Storage::disk($file->disk)->get($file->path);

        $this->assertStringContainsString($post->title, $content);
        $this->assertStringContainsString($post->image, $content);
        $this->assertStringContainsString($post->tags->pluck('name')->implode('|'), $content);
        $this->assertStringNotContainsString($post->content, $content);
        $this->assertStringNotContainsString($post->status, $content);
    }
}
