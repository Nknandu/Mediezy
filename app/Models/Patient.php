<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    protected $table="patient";
    protected $fillable=['id',
    'firstname',
    'lastname',
    'user_image',
    'mobileNo',
    'gender',
    'age',
    'location',
    'email',
    'UserId',
    'created_at',
    'updated_at','user_type'];

     // Accessor for the 'username' attribute
     public function getGenderAttribute($value)
     {
         return ucwords($value);
     }
}




