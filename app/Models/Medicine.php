<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
    protected $table='medicalprescription';
    protected $fillable=['id','user_id','docter_id','token_id','medicineName','Dosage','NoOfDays','MorningBF','MorningAF','Noon','night'];
}
