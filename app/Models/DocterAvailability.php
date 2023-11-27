<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocterAvailability extends Model
{
    use HasFactory;
    protected $table='docteravaliblity';
    protected $fillable=['id','docter_id','hospital_Name','availability'];
}
