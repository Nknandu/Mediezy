<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenHistory extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table='token_history';
     protected $fillable=['id','docter_id','hospital_Id',
    'session_title','TokenUpdateddate','startingTime','endingTime','tokens','TokenCount','timeduration','format','scheduleupto','selecteddays'];

}
