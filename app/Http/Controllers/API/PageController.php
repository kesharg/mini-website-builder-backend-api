<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Services\PageService;

class PageController extends Controller
{
     /**
     * @var PageService
     */
    protected $pageService;

    /**
     * Inject the PageService dependency.
     *
     * @param PageService $pageService
     */
    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * Display a listing of pages with their sections.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return $this->pageService->listPages();
    }

    /**
     * Store a newly created page along with sections.
     *
     * @param StorePageRequest $request
     * @return \App\Models\Page
     */
    public function store(StorePageRequest $request)
    {
        $validated = $request->validated();
        return $this->pageService->createPage($validated);
    }

    /**
     * Display the specified page with its sections.
     *
     * @param Page $page
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function show(Page $page)
    {
        return $page->load('sections');
    }

    /**
     * Update the specified page and synchronize its sections.
     *
     * @param UpdatePageRequest $request
     * @param Page $page
     * @return \App\Models\Page
     */
    public function update(UpdatePageRequest $request, Page $page)
    {
        $validated = $request->validated();
        return $this->pageService->updatePage($page, $validated);
    }

    /**
     * Remove the specified page from storage.
     *
     * @param Page $page
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        $page->delete();
        return response()->noContent();
    }

    /**
     * Reorder the sections of a page.
     *
     * @param Request $request
     * @param Page $page
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorderSections(Request $request, Page $page)
    {
        $orderedSections = $request->input('ordered_sections');
        $this->pageService->reorderSections($page, $orderedSections);

        return response()->json(['message' => 'Section order updated']);
    }

     /**
     * Get a list of available page layouts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLayouts()
    {
        return response()->json([
            'layouts' => ['home', 'contact', 'default']
        ]);
    }

    /**
     * Handle file uploads and return the accessible URL.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        if (!$request->hasFile('upload')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('upload');

        $path = $file->store('uploads', 'public');
        $url = asset('storage/' . $path);

        return response()->json([
            'url' => $url,
        ]);
    }
}
