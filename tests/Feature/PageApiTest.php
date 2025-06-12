<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Page;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PageApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_create_page_with_sections()
    {
        $payload = [
            'title' => 'New Page',
            'slug' => 'new-page',
            'layout' => 'default',
            'sections' => [
                ['type' => 'text', 'content' => ['text' => 'Section 1']],
                ['type' => 'image', 'content' => ['url' => 'img.jpg']],
            ],
        ];

        $response = $this->postJson('/api/pages', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure(['id', 'title', 'slug', 'layout', 'sections']);

        $this->assertDatabaseHas('pages', ['slug' => 'new-page']);
        $this->assertDatabaseCount('sections', 2);
    }

    public function test_can_update_page_and_sections()
    {
        $page = Page::factory()->create();

        $section = $page->sections()->create([
            'type' => 'text',
            'content' => ['text' => 'Old text'],
            'position' => 1,
        ]);

        $payload = [
            'title' => 'Updated Page',
            'slug' => 'updated-page',
            'layout' => 'custom',
            'sections' => [
                ['id' => $section->id, 'type' => 'text', 'content' => ['text' => 'Updated text']],
                ['type' => 'header', 'content' => ['text' => 'Header section']],
            ],
        ];

        $response = $this->putJson("/api/pages/{$page->id}", $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Updated Page']);

        $this->assertDatabaseHas('pages', ['id' => $page->id, 'title' => 'Updated Page']);
        $this->assertDatabaseCount('sections', 2);
    }
}
