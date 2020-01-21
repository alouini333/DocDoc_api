<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Appointment extends Model
{
    protected $fillable = [
      'starts_on',
      'ends_on',
      'notes',
      'patient_id',
      'visited'
    ];

    public function patient()
    {
        return $this->belongsTo('App\Patient');
    }

    public static function isFreeBetween($startsOn, $endsOn)
    {
        $startsOn = Carbon::parse($startsOn)->subMinute();
        $endsOn = Carbon::parse($endsOn)->subMinute();
        return Appointment::where(function (Builder $query) use ($startsOn, $endsOn) {
            $query->where('starts_on', '>', $startsOn)->where('ends_on', '<', $endsOn);
        })->orWhere(function (Builder $query) use ($startsOn, $endsOn) {
            $query->where('starts_on', '<=', $startsOn)->where('ends_on', '>=', $endsOn);
        })->orWhere(function (Builder $query) use ($startsOn, $endsOn) {
            $query->where('starts_on', '>', $startsOn)->where('starts_on', '<', $endsOn);
        })->orWhere(function (Builder $query) use ($startsOn, $endsOn) {
            $query->where('ends_on', '>', $startsOn)->where('ends_on', '<', $endsOn);
        })->count() === 0;
    }
}
