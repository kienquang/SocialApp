<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
     protected $fillable = ['sender_id', 'type', 'data'];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_notifications')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }
}
