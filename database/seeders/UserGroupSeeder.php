<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserGroup;

class UserGroupSeeder extends Seeder
{
  public function run()
  {
    $data = [
      [
        "Name" => "Administrator",
      ],
      [
        "Name" => "Pengawas K3",
      ],
      [
        "Name" => "Safety Advisor",
      ],
    ];

    foreach ($data as $item) {
        UserGroup::create($item);
    }
  }
}
