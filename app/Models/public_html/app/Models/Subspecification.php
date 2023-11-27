<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subspecification extends Model
{
    use HasFactory;
    protected $table='subspecialization';
    protected $fillable=['id','subspecification','remark'];
}
