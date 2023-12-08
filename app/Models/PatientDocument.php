<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientDocument extends Model
{
    use HasFactory;


    public function LabReports()
    {
        return $this->hasMany(LabReport::class, 'document_id', 'id')->orderBy('date', 'desc');
    }
    public function PatientPrescriptions()
    {
        return $this->hasMany(PatientPrescriptions::class, 'document_id', 'id')->orderBy('date', 'desc');
    }

}
