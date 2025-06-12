<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use App\Models\Page;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'layout' => 'required|string',
            'slug' => 'nullable|string',
            'sections' => 'nullable|array',
            'sections.*.id' => 'nullable|integer|exists:sections,id',
            'sections.*.type' => 'required|string',
            'sections.*.content' => 'nullable|array',
        ];
    }

    protected function prepareForValidation(): void
    {
        $baseTitle = $this->input('slug') ?? $this->input('title') ?? 'page';
        $this->merge([
            'slug' => $this->generateUniqueSlug($baseTitle),
        ]);
    }

    private function generateUniqueSlug(string $baseTitle): string
    {
        $baseSlug = Str::slug($baseTitle);
        $slug = $baseSlug;
        $count = 1;

        while (Page::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
