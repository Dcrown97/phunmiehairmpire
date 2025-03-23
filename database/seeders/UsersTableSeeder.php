<?php

namespace Database\Seeders;

use App\Interfaces\UserStatusInterface;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customerRole = config('roles.models.role')::where('name', '=', 'Customer')->first();
        $adminRole = config('roles.models.role')::where('name', '=', 'Admin')->first();
        $permissions = config('roles.models.permission')::all();

        /*
         * Add Users
         *
         */

        if (User::where('email', '=', 'admin@phunmiehairmpire.com')->first() === null) {
            $newUser = User::create([
                'name'     => 'Phunmiehairmpire Admin',
                'email'    => 'admin@phunmiehairmpire.com',
                'phoneno' => '08067799245',
                'address' => '764 Steuber Fort, North Ruthville 10175-6621',
                'from' => '12:00pm',
                'to' => '03:00pm',
                'is_verified' => "true",
                'is_active' => UserStatusInterface::ACTIVE,
                'can_login' => "true",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ]);

            $newUser->attachRole($adminRole);
            foreach ($permissions as $permission) {
                $newUser->attachPermission($permission);
            }
        }

        if (User::where('email', '=', 'customer@phunmiehairmpire.com')->first() === null) {
            $newUser = User::create([
                'name'     => 'Phunmiehairmpire Customer',
                'email'    => 'customer@phunmiehairmpire.com',
                'phoneno' => '081296394676',
                'address' => '1234 Maple Street, Apt 5B, Springfield, IL 62704',
                'from' => '12:00pm',
                'to' => '03:00pm',
                'is_verified' => "true",
                'is_active' => UserStatusInterface::ACTIVE,
                'can_login' => "true",
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ]);

            $newUser->attachRole($customerRole);
        }
    }
}
