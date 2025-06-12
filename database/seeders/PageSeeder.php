<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Enums\SectionType;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPageWithSections(
            'Home',
            'home',
            'home',
            [
                [ 'type' => SectionType::TEXT,  'content' => ['text' => 'Welcome to our website! Explore amazing content.'] ],
                [ 'type' => SectionType::IMAGE, 'content' => ['url' => 'https://picsum.photos/seed/home1/800/400'] ],
                [ 'type' => SectionType::TEXT,  'content' => ['text' => 'Scroll down to discover more.'] ],
            ]
        );

        $this->seedPageWithSections(
            'About',
            'about',
            'default',
            [
                [ 'type' => SectionType::TEXT,  'content' => ['text' => 'We are a team of passionate developers creating great software.'] ],
                [ 'type' => SectionType::IMAGE, 'content' => ['url' => 'https://picsum.photos/seed/about1/800/400'] ],
            ]
        );

        $this->seedPageWithSections(
            'FAQ',
            'faq',
            'default',
            [
                [ 'type' => SectionType::TEXT, 'content' => ['text' => 'Q: What is this site about? A: It’s a demo builder app.'] ],
                [ 'type' => SectionType::TEXT, 'content' => ['text' => 'Q: Can I edit sections? A: Yes, you can easily edit any section.'] ],
            ]
        );

        $this->seedPageWithSections(
            'Contact',
            'contact',
            'contact',
            [
                [ 'type' => SectionType::TEXT,  'content' => ['text' => 'You can reach us at contact@example.com'] ],
                [ 'type' => SectionType::IMAGE, 'content' => ['url' => 'https://picsum.photos/seed/contact1/800/400'] ],
            ]
        );
    }

    private function seedPageWithSections(string $title, string $slug, string $layout, array $sections)
    {
        $page = Page::updateOrCreate(
            ['slug' => $slug],
            ['title' => $title, 'layout' => $layout]
        );

        if ($page->sections()->count() === 0) {
            $page->sections()->createMany($sections);
        }
    }
}
