<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report_comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'comment_id',
        'reporter_id',
        'reason',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
}
