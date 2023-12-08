<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodaySchedule extends Model
{
    use HasFactory;


    public function doctor()
    {
        return $this->belongsTo(Docter::class, 'docter_id', 'id');
    }
}
