<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Patient;

class PatientController extends Controller
{
    public function index()
    {
        try {
            $patients = Patient::get();
            return \Utils::returnData($data);
        } catch (\Exception $e) {
            return \Utils::handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $patient = Patient::findOrFail($id);
            return \Utils::returnData($patient);
        } catch (\Exception $e) {
            return \Utils::handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
              'name'           => 'required|string',
              'familyName'     => 'required|string',
              'date_of_birth'  => 'required|date|before:today',
              'avatar'         => 'nullable|file|max:8000',
              'email'          => 'required|email',
              'phone'          => 'required|string',
            ]);
            $patient = new Patient;
            $patient->fill($request->all());
            if ($request->has('avatar')) {
                $patient->avatar = $request->avatar->hashName();
                $request->avatar->store('avatars');
            }
            $patient->save();
            return \Utils::returnSuccess('Patient created with success');
        } catch (\Exception $e) {
            return \Utils::handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
              'name'           => 'sometimes|string',
              'familyName'     => 'sometimes|string',
              'date_of_birth'  => 'sometimes|date|before:today',
              'avatar'         => 'sometimes|nullable|file|max:8000',
              'email'          => 'sometimes|email',
              'phone'          => 'sometimes|string',
            ]);
            $patient = new Patient;
            $patient->fill($request->all());
            if ($request->has('avatar')) {
                if ($patient->avatar) {
                    Storage::delete($patient->getOriginal('avatar'));
                }
                $patient->avatar = $request->avatar->hashName();
                $request->avatar->store('avatars');
            }
            $patient->save();
            return \Utils::returnSuccess('Patient created with success');
        } catch (\Exception $e) {
            return \Utils::handleException($e);
        }
    }

    public function delete($id)
    {
        try {
            $patient = Patient::findOrFail($id);
            $patient->delete();
            return \Utils::returnSuccess('Patient deleted with success');
        } catch (\Exception $e) {
            return \Utils::handleException($e);
        }

    }
}
