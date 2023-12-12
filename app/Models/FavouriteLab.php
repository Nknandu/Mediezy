<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouriteLab extends Model
{
    use HasFactory;
    protected $table='favouriteslab';
    protected $fillable=['id','doctor_id','lab_id','created_at','updated_at'];
}
