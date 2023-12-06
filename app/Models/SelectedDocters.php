<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectedDocters extends Model
{
    use HasFactory;
    protected $table='selecteddocters';
    protected $fillable = ['cat_id', 'dataList'];
    protected $casts = [
        'dataList' => 'array',
    ];
}