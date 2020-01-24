<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Appointment;
use App\Exceptions\DatesException;

class AppointmentController extends Controller
{
    public function index()
    {
        try {
            $appointments = Appointment::with('patient')->get();
            return \Utils::returnData($appointments);
            // @codeCoverageIgnoreStart
        } catch (\Exception $e) {
            return \Utils::handleException($e);
        }   // @codeCoverageIgnoreEnd
    }

    public function show($id)
    {
        try {
            $appointment = Appointment::with('patient')->findOrFail($id);
            return \Utils::returnData($appointment);
        } catch (\Exception $e) {
            return \Utils::handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
              'starts_on'       => 'required|date_format:Y-m-d H:i|after:now',
              'ends_on'         => 'required|date_format:Y-m-d H:i|after:starts_on',
              'notes'           => 'sometimes|nullable|string',
              'patient_id'      => 'required|exists:patients,id',
            ]);
            if (!Appointment::isFreeBetween($request->starts_on, $request->ends_on)) {
                throw new DatesException;
            }
            $appointment = new Appointment;
            $appointment->fill($request->only(['starts_on', 'notes', 'patient_id', 'ends_on']));
            $appointment->save();
            return \Utils::returnSuccess('Appointment created with success');
        } catch (\Exception $e) {
            return \Utils::handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
              'starts_on'       => 'sometimes|date_format:Y-m-d H:i|after:now',
              'ends_on'         => 'required_with:starts_on|date_format:Y-m-d H:i|after:starts_on',
              'notes'           => 'sometimes|nullable|string',
              'visited'         => 'sometimes|boolean'
            ]);
            $appointment = Appointment::findOrFail($id);
            if ($request->has('starts_on') && !Appointment::isFreeBetween($request->starts_on, $request->ends_on, $id)) {
                throw new DatesException;
            }
            $appointment->fill($request->only(['starts_on', 'notes', 'patient_id', 'ends_on', 'visited']));
            $appointment->save();
            return \Utils::returnSuccess('Appointment updated with success');
        } catch (\Exception $e) {
            return \Utils::handleException($e);
        }
    }

    public function delete($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $appointment->delete();
            return \Utils::returnSuccess('Appointment deleted with success');
        } catch (\Exception $e) {
            return \Utils::handleException($e);
        }
    }
}
