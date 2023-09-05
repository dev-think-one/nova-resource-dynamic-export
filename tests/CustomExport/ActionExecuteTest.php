<?php

namespace NovaResourceDynamicExport\Tests\CustomExport;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Notifications\NovaNotification;
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
    public function handle_action()
    {
        Tag::factory()
            ->has(
                Post::factory()
                    ->content('Example')
                    ->image('foo/bar.png')
                    ->count(5)
            )
            ->create();
        /** @var Tag $breakingTag */
        $breakingTag = Tag::factory()
            ->state([
                'name' => 'Breaking',
            ])
            ->has(
                Post::factory()
                    ->content('Example')
                    ->image('foo/bar.png')
                    ->count(7)
            )
            ->create();
        Tag::factory()
            ->has(
                Post::factory()
                    ->content('Example')
                    ->image('foo/bar.png')
                    ->count(13)
            )
            ->create();


        $exportStoredFile = ExportStoredFile::factory()
            ->create();

        $uriKey   = \NovaResourceDynamicExport\Nova\Resources\ExportStoredFile::uriKey();
        $resource = new  \NovaResourceDynamicExport\Nova\Resources\ExportStoredFile($exportStoredFile);
        /** @var ExportResourceAction $resourceAction */
        $resourceAction = $resource->actions(app(NovaRequest::class))[0];

        $this->assertEquals(1, ExportStoredFile::query()->count());

        Notification::assertNothingSent();

        $response = $this->post("nova-api/{$uriKey}/action?action={$resourceAction->uriKey()}", [
            'resources'   => $exportStoredFile->getKey(),
            'filename'    => 'foo-bar',
            'writer_type' => 'Csv',
            'export'      => 'PostsWithTagBreaking',
        ]);

        $response->assertSuccessful();
        $response->assertJsonPath('message', 'Request added to queue. Please wait a while to complete it.');

        $this->assertEquals(2, ExportStoredFile::query()->count());

        /** @var ExportStoredFile $file */
        $file = ExportStoredFile::query()->latest('id')->first();

        $this->assertStringContainsString('foo-bar.csv', $file->name);
        $this->assertStringNotContainsString('foo-bar', $file->path);

        Storage::disk($file->disk)->assertExists($file->path);

        $content = Storage::disk($file->disk)->get($file->path);

        /** @var Post $post */
        foreach ($breakingTag->posts as $post) {
            $this->assertStringContainsString($post->title, $content);
        }

        Notification::assertCount(1);

        Notification::assertSentTo($this->admin, NovaNotification::class);
    }


}
