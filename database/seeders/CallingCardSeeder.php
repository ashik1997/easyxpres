<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CallingCard;

class CallingCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cards = [
            ['card_no' => '1234 5678 9012 0001', 'user_name' => 'Rahim',  'pin' => '1234', 'point' => 50, 'minute_sec' => '10:00', 'payment' => 100],
            ['card_no' => '1234 5678 9012 0002', 'user_name' => 'Karim',  'pin' => '2234', 'point' => 60, 'minute_sec' => '12:00', 'payment' => 200],
            ['card_no' => '1234 5678 9012 0003', 'user_name' => 'Sabbir', 'pin' => '3234', 'point' => 40, 'minute_sec' => '08:00', 'payment' => 300],
          
        ];

        foreach ($cards as $card) {
            CallingCard::create($card);
        }
    }
}
