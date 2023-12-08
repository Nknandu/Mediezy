<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    use HasFactory;
    protected $table="lab_test";
    protected $fillable = [
        'id',
        'lab_id',
        'TestName',
        'product_image',
        'product_description',
        'Test_price',
        'discount',
        'Total_price',
        'created_at',
        'updated_at'];
}
