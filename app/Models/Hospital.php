<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;
    protected $table="Hosptal";
    protected $fillable = [
        'id',
        'firstname',
        'hospital_image',
        'mobileNo',
        'location',
        'email',
        'address',
        'UserId',
        'created_at',
        'updated_at','Type'];

}
