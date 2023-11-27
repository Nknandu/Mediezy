<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docter extends Model
{
    use HasFactory;
    protected $table='docter';
    protected $fillable=[
    'id',
    'firstname',
    'lastname',
    'docter_image',
    'mobileNo',
    'gender',
    'location',
    'email',
    'specialization_id',
    'specification_id',
    'subspecification_id',
	'about'	,
    'Services_at',
    'UserId',
    'created_at',
    'updated_at'];



}

