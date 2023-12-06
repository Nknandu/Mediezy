<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicalshop extends Model
{
    use HasFactory;
    protected $table="medicalshop";
    protected $fillable = [
        'id',
        'firstname',
        'lastname',
        'shop_image',
        'mobileNo',
        'location',
        'email',
        'address',
        'UserId',
        'created_at',
        'updated_at'];
}
