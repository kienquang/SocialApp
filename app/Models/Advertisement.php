<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image_url',
        'link_url',
        'position',
        'status',
        'display_order',
    ];

    /**
     * Các thuộc tính (attributes) nên được ép kiểu (cast).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'display_order' => 'integer',
    ];
}
