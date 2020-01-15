<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
      'happening_on',
      'notes',
      'patient_id',
      'visited'
    ];
    public function patient()
    {
        return $this->hasOne('App\Patient');
    }
}
