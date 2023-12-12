<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouriteShop extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table='favouirtes_shop';
    protected $fillable=['id','doctor_id','medicalshop_id','created_at','updated_at'];
}
