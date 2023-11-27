<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
    protected $table='medicalprescription';
    protected $fillable=['id','token_id','user_id','docter_id','medicineName','Dosage','NoOfDays','MorningBF','morning','Noon','night','type','attachment'];
}
