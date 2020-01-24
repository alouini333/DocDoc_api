<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Appointment;
use App\User;
use App\Patient;
use Carbon\Carbon;

class AppointmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexThrowsPermissionError()
    {
        $response = $this->get('/api/appointments');
        $response->assertStatus(401);
    }

    public function testGetAppointmentsSucessfully()
    {
        $user = User::first();
        $response = $this->get('/api/appointments', [
            'Authorization' => 'Bearer ' . \JWTAuth::fromUser($user)
        ]);
        $response->assertStatus(200);
    }

    public function testGetAppointmentReturnsNotFoundError()
    {
        $user = User::first();
        $response = $this->get('/api/appointments/random_id', [
            'Authorization' => 'Bearer ' . \JWTAuth::fromUser($user)
        ]);
        $response->assertStatus(404);
    }

    public function testGetAppointmentSucessfully()
    {
        $user = User::first();
        $patient = factory(Patient::class)->create();

        $appointment = Appointment::create([
          'starts_on' => Carbon::now(),
          'ends_on'   => Carbon::now()->addMinutes(30),
          'notes'     => 'Notes example',
          'patient_id'=> $patient->id
        ]);
        $response = $this->get('/api/appointments/'.$appointment->id, [
            'Authorization' => 'Bearer ' . \JWTAuth::fromUser($user)
        ]);
        $response->assertStatus(200);
    }

    public function testStoreAppointmentThrowsCheckDatesErrorCase1()
    {
        /*
         * This case when the Appointment overlaps
         * an existing appointment
         */
        $user = User::first();
        $patient = factory(Patient::class)->create();

        $startsOn = Carbon::now()->addMinutes(30);
        $endsOn = Carbon::now()->addMinutes(60);

        Appointment::create([
          'starts_on'   => $startsOn,
          'ends_on'     => $endsOn,
          'patient_id'  => $patient->id,
        ]);

        $response = $this->post('/api/appointments', [
          'starts_on' => $startsOn->subMinutes(15)->format('Y-m-d H:i'),
          'ends_on'   => $endsOn->addMinutes(15)->format('Y-m-d H:i'),
          'notes'     => 'Notes example',
          'patient_id'=> $patient->id
        ], [
            'Authorization' => 'Bearer ' . \JWTAuth::fromUser($user)
        ]);

        $response->assertStatus(500);

        $response->assertJson([
          'status'  => 'fail',
          'message' => 'Please check the start and end dates'
        ]);
    }

    public function testStoreAppointmentThrowsCheckDatesErrorCase2()
    {
        /*
         * This case when the Appointment overlaps only the start date of an existing
         * appointment
         */
        $user = User::first();
        $patient = factory(Patient::class)->create();

        $startsOn = Carbon::now()->addMinutes(30);
        $endsOn = Carbon::now()->addMinutes(60);

        Appointment::create([
          'starts_on'   => $startsOn,
          'ends_on'     => $endsOn,
          'patient_id'  => $patient->id,
        ]);

        $response = $this->post('/api/appointments', [
          'starts_on' => $startsOn->subMinutes(15)->format('Y-m-d H:i'),
          'ends_on'   => $startsOn->addMinutes(20)->format('Y-m-d H:i'),
          'notes'     => 'Notes example',
          'patient_id'=> $patient->id
        ], [
            'Authorization' => 'Bearer ' . \JWTAuth::fromUser($user)
        ]);

        $response->assertStatus(500);

        $response->assertJson([
          'status'  => 'fail',
          'message' => 'Please check the start and end dates'
        ]);
    }

    public function testStoreAppointmentThrowsCheckDatesErrorCase3()
    {
        /*
         * This case when the Appointment overlaps only the end date of an existing
         * appointment
         */
        $user = User::first();
        $patient = factory(Patient::class)->create();

        $startsOn = Carbon::now()->addMinutes(30);
        $endsOn = Carbon::now()->addMinutes(60);

        Appointment::create([
          'starts_on'   => $startsOn,
          'ends_on'     => $endsOn,
          'patient_id'  => $patient->id,
        ]);

        $response = $this->post('/api/appointments', [
          'starts_on' => $endsOn->subMinutes(15)->format('Y-m-d H:i'),
          'ends_on'   => $endsOn->addMinutes(20)->format('Y-m-d H:i'),
          'notes'     => 'Notes example',
          'patient_id'=> $patient->id
        ], [
            'Authorization' => 'Bearer ' . \JWTAuth::fromUser($user)
        ]);

        $response->assertStatus(500);

        $response->assertJson([
          'status'  => 'fail',
          'message' => 'Please check the start and end dates'
        ]);
    }

    public function testStoreAppointmentThrowsCheckDatesErrorCase4()
    {
        /*
         * This case when the Appointment will be overlapped
         * by an existing appointment
         */
        $user = User::first();
        $patient = factory(Patient::class)->create();

        $startsOn = Carbon::now()->addMinutes(30);
        $endsOn = Carbon::now()->addMinutes(60);

        Appointment::create([
          'starts_on'   => $startsOn,
          'ends_on'     => $endsOn,
          'patient_id'  => $patient->id,
        ]);

        $response = $this->post('/api/appointments', [
          'starts_on' => $startsOn->addMinutes(5)->format('Y-m-d H:i'),
          'ends_on'   => $endsOn->subMinutes(5)->format('Y-m-d H:i'),
          'notes'     => 'Notes example',
          'patient_id'=> $patient->id
        ], [
            'Authorization' => 'Bearer ' . \JWTAuth::fromUser($user)
        ]);

        $response->assertStatus(500);

        $response->assertJson([
          'status'  => 'fail',
          'message' => 'Please check the start and end dates'
        ]);
    }

    public function testStoreAppointmentThrowsValidationError()
    {
        $user = User::first();
        $response = $this->post('/api/appointments', [
        ], [
            'Authorization' => 'Bearer ' . \JWTAuth::fromUser($user)
        ]);

        $response->assertStatus(422);
    }

    public function testStoreAppointmentSucessfully()
    {
        $user = User::first();
        $patient = factory(Patient::class)->create();
        $startsOn = Carbon::now()->addMinutes(30);
        $endsOn = Carbon::now()->addMinutes(60);

        $response = $this->post('/api/appointments', [
          'starts_on' => $startsOn->format('Y-m-d H:i'),
          'ends_on'   => $endsOn->format('Y-m-d H:i'),
          'notes'     => 'Notes example',
          'patient_id'=> $patient->id
        ], [
            'Authorization' => 'Bearer ' . \JWTAuth::fromUser($user)
        ]);

        $response->assertStatus(200);

        $response->assertJson([
          'status'  => 'success',
          'message' => 'Appointment created with success'
        ]);

        $this->assertDatabaseHas('appointments', [
          'starts_on' => $startsOn->format('Y-m-d H:i'),
          'ends_on'   => $endsOn->format('Y-m-d H:i'),
          'notes'     => 'Notes example',
          'patient_id'=> $patient->id,
          'visited'   => false
        ]);
    }

    public function testStoreAppointmentThrowsCheckDatesError()
    {
        $user = User::first();
        $patient = factory(Patient::class)->create();
        $startsOn = Carbon::now()->addMinutes(30);
        $endsOn = Carbon::now()->addMinutes(60);
        $appointment = Appointment::create([
          'starts_on'   => $startsOn->format('Y-m-d H:i'),
          'ends_on'     => $endsOn->format('Y-m-d H:i'),
          'patient_id'  => $patient->id,
        ]);
        $appointment2 = Appointment::create([
          'starts_on'   => $startsOn->addHours(2)->format('Y-m-d H:i'),
          'ends_on'     => $endsOn->addHours(2)->format('Y-m-d H:i'),
          'patient_id'  => $patient->id,
        ]);

        $response = $this->put('/api/appointments/'.$appointment->id, [
          'starts_on' => $startsOn->addMinutes(15)->format('Y-m-d H:i'),
          'ends_on'   => $startsOn->addMinutes(15)->format('Y-m-d H:i'),
          'notes'     => 'Notes example',
          'patient_id'=> $patient->id
        ], [
            'Authorization' => 'Bearer ' . \JWTAuth::fromUser($user)
        ]);

        $response->assertStatus(500);

        $response->assertJson([
          'status'  => 'fail',
          'message' => 'Please check the start and end dates'
        ]);
    }

    public function testUpdateAppointmentSucessfully()
    {
        $user = User::first();
        $patient = factory(Patient::class)->create();
        $startsOn = Carbon::now()->addMinutes(30);
        $endsOn = Carbon::now()->addMinutes(60);
        $appointment = Appointment::create([
          'starts_on'   => $startsOn->format('Y-m-d H:i'),
          'ends_on'     => $endsOn->format('Y-m-d H:i'),
          'patient_id'  => $patient->id,
        ]);
        $response = $this->put('/api/appointments/'.$appointment->id, [
          'starts_on' => $startsOn->addMinutes(15)->format('Y-m-d H:i'),
          'ends_on'   => $endsOn->addMinutes(15)->format('Y-m-d H:i'),
          'notes'     => 'Notes example',
        ], [
            'Authorization' => 'Bearer ' . \JWTAuth::fromUser($user)
        ]);

        $response->assertStatus(200);

        $response->assertJson([
          'status'  => 'success',
          'message' => 'Appointment updated with success'
        ]);

        $this->assertDatabaseHas('appointments', [
          'starts_on' => $startsOn->format('Y-m-d H:i'),
          'ends_on'   => $endsOn->format('Y-m-d H:i'),
          'notes'     => 'Notes example',
          'patient_id'=> $patient->id,
          'visited'   => false
        ]);
    }

    public function testUpdateAppointmentThrowsNotFoundError()
    {
        $user = User::first();
        $patient = factory(Patient::class)->create();
        $startsOn = Carbon::now()->addMinutes(30);
        $endsOn = Carbon::now()->addMinutes(60);
        $appointment = Appointment::create([
          'starts_on'   => $startsOn->format('Y-m-d H:i'),
          'ends_on'     => $endsOn->format('Y-m-d H:i'),
          'patient_id'  => $patient->id,
        ]);
        $response = $this->put('/api/appointments/random_id', [
          'starts_on' => $startsOn->addMinutes(15)->format('Y-m-d H:i'),
          'ends_on'   => $endsOn->addMinutes(15)->format('Y-m-d H:i'),
          'notes'     => 'Notes example',
        ], [
            'Authorization' => 'Bearer ' . \JWTAuth::fromUser($user)
        ]);

        $response->assertStatus(404);
    }

    public function testDeleteAppointmentSucessfully()
    {
        $user = User::first();
        $patient = factory(Patient::class)->create();
        $startsOn = Carbon::now()->addMinutes(30);
        $endsOn = Carbon::now()->addMinutes(60);
        $appointment = Appointment::create([
          'starts_on'   => $startsOn->format('Y-m-d H:i'),
          'ends_on'     => $endsOn->format('Y-m-d H:i'),
          'patient_id'  => $patient->id,
        ]);
        $response = $this->delete('/api/appointments/'.$appointment->id, [
        ], [
            'Authorization' => 'Bearer ' . \JWTAuth::fromUser($user)
        ]);

        $response->assertStatus(200);

        $response->assertJson([
          'status'  => 'success',
          'message' => 'Appointment deleted with success'
        ]);

        $this->assertDatabaseMissing('appointments', [
          'id'        => $appointment->id
        ]);
    }

    public function testDeleteAppointmentThrowsNotFoundError()
    {
        $user = User::first();
        $response = $this->delete('/api/appointments/random_id', [
        ], [
            'Authorization' => 'Bearer ' . \JWTAuth::fromUser($user)
        ]);

        $response->assertStatus(404);
    }
}
