<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class schedule extends Model
{
    use HasFactory;
    protected $table='schedule';
     protected $fillable=['id','docter_id','hospital_Id',
    'session_title','date','startingTime','endingTime','tokens',
    'TokenCount','created_at','timeduration','format','updated_at',
    'scheduleupto','selecteddays','eveningstartingTime','eveningendingTime'];


    public function docter()
    {
        return $this->belongsTo(Docter::class, 'docter_id', 'id');
    }
    public function doctoravailability()
    {
        return $this->belongsTo(DocterAvailability::class, 'hospital_Id');
    }

}
