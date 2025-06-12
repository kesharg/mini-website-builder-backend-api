<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Section;
use Illuminate\Support\Facades\DB;

class PageService
{
    /**
     * Get a list of all pages with their sections, ordered by latest first.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listPages()
    {
        return Page::with('sections')->latest()->get();
    }

    /**
     * Create a new page along with its sections in a database transaction.
     *
     * @param array $validated The validated request data.
     * @return \App\Models\Page The created page with ordered sections.
     */
    public function createPage(array $validated)
    {
        return DB::transaction(function () use ($validated) {
            $page = Page::create([
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'layout' => $validated['layout'],
            ]);

            if (!empty($validated['sections'])) {
                $position = 1;
                foreach ($validated['sections'] as $position => $section) {
                                    $position++;

                    $page->sections()->create([
                        'type' => $section['type'],
                        'content' => $section['content'] ?? [],
                        'position' => $position,
                    ]);
                    $position++;

                }
            }

            return $page->load(['sections' => fn($q) => $q->orderBy('position')]);
        });
    }

    /**
     * Update an existing page and synchronize its sections.
     *
     * @param \App\Models\Page $page The page to update.
     * @param array $validated The validated request data.
     * @return \App\Models\Page The updated page with ordered sections.
     */
    public function updatePage(Page $page, array $validated)
{
    $page->update([
        'title' => $validated['title'],
        'slug' => $validated['slug'],
        'layout' => $validated['layout'],
    ]);

    $incomingSections = $validated['sections'] ?? [];

$existingIds = $page->sections()->pluck('id')->map(fn($id) => (int) $id)->toArray(); // Force integer match
$incomingIds = collect($incomingSections)->pluck('id')->filter()->map(fn($id) => (int) $id)->toArray();


    // Delete removed sections
    $deleted = array_diff($existingIds, $incomingIds);
    if (!empty($deleted)) {
        Section::destroy($deleted);
    }

    // Initialize position counter starting from 1
    $position = 1;

    foreach ($incomingSections as $sectionData) {
        if (!empty($sectionData['id'])) {
            // Update existing section with incremented position
            $section = Section::find($sectionData['id']);
            $section->update([
                'type' => $sectionData['type'],
                'content' => $sectionData['content'] ?? [],
                'position' => $position,
            ]);
        } else {
            // Create new section with incremented position
            $page->sections()->create([
                'type' => $sectionData['type'],
                'content' => $sectionData['content'] ?? [],
                'position' => $position,
            ]);
        }

        $position++; // Increment position for next section
    }

    return $page->load(['sections' => fn($q) => $q->orderBy('position')]);
}


    /**
     * Reorder the positions of the sections for a specific page.
     *
     * @param \App\Models\Page $page The page whose sections are to be reordered.
     * @param array $orderedSections An array of sections with new positions (id and position).
     * @return void
     */
    public function reorderSections(Page $page, array $orderedSections)
    {
        foreach ($orderedSections as $item) {
            Section::where('id', $item['id'])
                ->where('page_id', $page->id)
                ->update(['position' => $item['position']]);
        }
    }
}
