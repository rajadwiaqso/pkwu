<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class server extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample servers
        \App\Models\Server::create([
            'logo' => 'black_logo.png',
            'fee' => 3,
            'name' => 'MarketRaja',
            'description' => 'This is MarketRaja, providing reliable and fast services for all your gaming needs.',
            'url' => 'http://localhost:8000',
            'status' => 'active',
            'type' => 'local',
            'ip_address' => '127.0.0.1',
            'port' => '8000',
        ]);
    }
}
