<?php

namespace App\Models;

use App\Enums\SectionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
        use HasFactory;
   protected $fillable = [
        'page_id',
        'type',
        'content',
        'position',

    ];

protected $casts = [
    'type' => SectionType::class,
    'content' => 'array',
];

    public function page()
{
    return $this->belongsTo(Page::class);
}

}
