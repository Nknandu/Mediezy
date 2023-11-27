<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Symtoms extends Model
{
    use HasFactory;
    protected $table="symtoms";
    protected $fillable = [
        'id','specialization_id','symtoms',
        'created_at','updated_at'
    ];
}
