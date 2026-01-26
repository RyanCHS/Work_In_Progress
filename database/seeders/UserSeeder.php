<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
      [
        "UserId" => "1",
        "Username" => "admin",
        "Password" => Hash::make("secret123"),
        "UserGroup" => "Administrator",
         "IsActivated" => true
      ]
    ];

    foreach ($data as $item) {
        User::create($item);
    }
    }
}
