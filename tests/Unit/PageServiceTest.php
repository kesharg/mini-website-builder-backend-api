<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PageService;
use App\Models\Page;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PageServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PageService $pageService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pageService = new PageService();
    }

    public function test_create_page_with_sections_saves_correctly()
    {
        $data = [
            'title' => 'Test Page',
            'slug' => 'test-page',
            'layout' => 'default',
            'sections' => [
                ['type' => 'text', 'content' => ['text' => 'Hello']],
                ['type' => 'image', 'content' => ['url' => 'image.jpg']],
            ],
        ];

        $page = $this->pageService->createPage($data);

        $this->assertDatabaseHas('pages', ['id' => $page->id, 'title' => 'Test Page']);
        $this->assertCount(2, $page->sections);
        $this->assertEquals(1, $page->sections[0]->position);
        $this->assertEquals(2, $page->sections[1]->position);
    }

    public function test_update_page_with_sections_syncs_sections()
    {
        $page = Page::factory()->create();

        // Create initial sections
        $page->sections()->createMany([
            ['type' => 'text', 'content' => ['text' => 'Old'], 'position' => 1],
            ['type' => 'image', 'content' => ['url' => 'old.jpg'], 'position' => 2],
        ]);

        // Refresh section relations
        $textSection = $page->sections()->where('type', 'text')->first();
        $imageSection = $page->sections()->where('type', 'image')->first();

        $updateData = [
            'title' => 'Updated Page',
            'slug' => 'updated-page',
            'layout' => 'custom',
            'sections' => [
                ['id' => $textSection->id, 'type' => 'text', 'content' => ['text' => 'Updated']],
                ['type' => 'html', 'content' => ['text' => '<p>Hello</p>']],
            ],
        ];

        $updatedPage = $this->pageService->updatePage($page, $updateData);

        $this->assertEquals('Updated Page', $updatedPage->title);
        $this->assertCount(2, $updatedPage->sections);

        // Ensure the old image section was deleted
        $this->assertDatabaseMissing('sections', ['id' => $imageSection->id]);
    }
}
