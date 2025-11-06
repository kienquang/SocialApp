<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report_user extends Model
{
    use HasFactory;
<<<<<<< HEAD
=======
    protected $fillable = [
        'reported_user_id',
        'reporter_id',
        'reason',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }
>>>>>>> origin/kienBranch
}
