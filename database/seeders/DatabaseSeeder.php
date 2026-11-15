<?php

namespace Database\Seeders;

use App\Models\MaliOperator;
use App\Models\User;
use App\Models\appSetup;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            AdminSeeder::class,
            CallingCardSeeder::class,
        ]);

        appSetup::firstOrCreate(
            ['id' => 1],
            ['add_balance_content' => '']
        );

        $operator = [
            [
                'name' => 'Dhiraagu',
                'code' => '7',
                'status' => 'active',
            ],
            [
                'name' => 'ooredoo',
                'code' => '9',
                'status' => 'active',
            ],
        ];
        foreach ($operator as $op) {
            MaliOperator::create($op);
        }

        /* User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); */
    }
}
