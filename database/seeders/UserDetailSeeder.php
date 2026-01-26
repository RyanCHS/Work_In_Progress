<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserDetail;

class UserDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $data = [
      [
        "UserId" => "1",
        "Name" => "admin",
        "Email" => "admin@admin.com",
        "Address" => "Karawaci",
        "City" => "Tangerang",
        "Phone" => "08xxxxxxxxxxx"
      ]
    ];

    foreach ($data as $item) {
        UserDetail::create($item);
    }
    }
}
