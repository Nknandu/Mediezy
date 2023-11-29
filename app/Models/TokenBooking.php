<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenBooking extends Model
{
    use HasFactory;

    protected $table="token_booking";
    protected $fillable=['id','doctor_id',
    'BookedPerson_id',	'PatientName'	,
    'gender',
    'age'	,'MobileNo',
    'Appoinmentfor_id',	'date',	'TokenNumber',	'TokenTime',
    'Bookingtime',	'Is_checkIn'	,'Is_completed'	,'Is_canceled',	'whenitstart',
    'whenitcomes'	,'regularmedicine',	'amount',
    'paymentmethod','created_at','updated_at','	clinic_id'	];
}
