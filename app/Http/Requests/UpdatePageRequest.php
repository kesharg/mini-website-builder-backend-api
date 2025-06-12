<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use App\Models\Page;

class UpdatePageRequest extends FormRequest
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
        $this->merge([
            'slug' => $this->generateUniqueSlug(
                $this->input('slug') ?? $this->input('title'),
                $this->route('page')?->id
            ),
        ]);
    }

    private function generateUniqueSlug(string $baseTitle, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($baseTitle);
        $slug = $baseSlug;
        $count = 1;

        while (
            Page::where('slug', $slug)
                ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}
