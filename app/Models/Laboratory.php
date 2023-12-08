<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    use HasFactory;


    protected $table="laboratory";
    protected $fillable = [
        'id',
        'firstname',
        'lab_image',
        'mobileNo',
        'location',
        'email',
        'address',
        'UserId',
        'created_at',
        'updated_at','Type'];
}
