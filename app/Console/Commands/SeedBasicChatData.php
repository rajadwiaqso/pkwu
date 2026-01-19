<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ChatChannel;

class SeedBasicChatData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:seed-basic-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed basic chat channels for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding basic chat channels...');
        
        $channels = [
            [
                'name' => 'General',
                'slug' => 'general',
                'description' => 'General discussion channel',
                'type' => 'category',
                'is_private' => false,
                'is_readonly' => false,
            ],
            [
                'name' => 'Gaming',
                'slug' => 'gaming',
                'description' => 'Gaming discussion and trading',
                'type' => 'category',
                'is_private' => false,
                'is_readonly' => false,
            ],
            [
                'name' => 'Marketplace',
                'slug' => 'marketplace',
                'description' => 'Buy and sell items',
                'type' => 'community',
                'is_private' => false,
                'is_readonly' => false,
            ],
        ];
        
        foreach ($channels as $channelData) {
            $channel = ChatChannel::firstOrCreate(
                ['slug' => $channelData['slug']],
                $channelData
            );
            
            $this->info("Created/Updated channel: {$channel->name}");
        }
        
        $this->info('Basic chat channels seeded successfully!');
    }
}
