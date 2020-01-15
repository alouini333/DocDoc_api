<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Appointment;

class AppointmentController extends Controller
{
    public function index()
    {
        try {
            $appointments = Appointment::with('patient')->get();
            return \Utils::returnData($appointments);
        } catch (\Exception $e) {
            return \Utils::handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            return \Utils::returnData($appointment);
        } catch (\Exception $e) {
            return \Utils::handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
              'happening_on'    => 'required|date_format:Y-m-d H:i|after:now',
              'notes'           => 'sometimes|nullable|string',
              'patient_id'      => 'required|exists:patients,id',
            ]);
            $appointment = new Appointment;
            $appointment->fill($request->only(['happening_on', 'notes', 'patient_id']));
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
              'happening_on'    => 'sometimes|date_format:Y-m-d H:i|after:now',
              'notes'           => 'sometimes|nullable|string',
              'visited'         => 'sometimes|boolean'
            ]);
            $appointment = new Appointment;
            $appointment->fill($request->only(['happening_on', 'notes', 'visited']));
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
