<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineProduct extends Model
{
    use HasFactory;
    protected $table="medicine";
    protected $fillable = [
        'id',
        'medicalshop_id',
        'MedicineName',
        'product_image',
        'product_description',
        'product_price',
        'discount',
        'Total_price',
        'created_at',
        'updated_at'];

}
