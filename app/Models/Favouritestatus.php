<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favouritestatus extends Model
{
    use HasFactory;
    protected $table="addfavourite";
    protected $fillable = [
        'id','UserId','doctor_id','favouritestatus',
        'created_at','updated_at'
    ];

}
