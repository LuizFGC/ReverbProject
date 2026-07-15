<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */

        public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {

            Ticket::factory(2)->create([
                'user_id' => $user->sid,
                'user_name' => $user->name,
            ]);

        }
    }

}
