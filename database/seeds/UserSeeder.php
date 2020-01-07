<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new User;
        $admin->fill([
          'name'     => 'admin',
          'password' => 'secret',
          'email'    => 'admin@example.com'
        ]);
        $admin->save();
        $admin->refresh();
        $admin->assignRole('admin');

        $member = new User;
        $member->fill([
          'name'     => 'member',
          'password' => 'secret',
          'email'    => 'member@example.com'
        ]);
        $member->save();
        $member->refresh();
        $member->assignRole('member');
    }
}
