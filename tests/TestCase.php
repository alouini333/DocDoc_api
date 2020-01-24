<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp():void
    {
        parent::setUp();
        User::create([
          'name'  => 'admin',
          'email' => 'admin@example.com',
          'password'  => \Hash::make('secret'),
        ]);
    }
}
