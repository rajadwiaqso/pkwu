<?php

namespace App\Console\Commands;

use App\Models\ChatChannel;
use App\Models\ChatUserSetting;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Console\Command;

class SetupChatSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:setup {--seed : Seed default channels and settings}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the global chat system';

    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        parent::__construct();
        $this->chatService = $chatService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up Global Chat System...');

        if ($this->option('seed')) {
            $this->call('db:seed', ['--class' => 'ChatSystemSeeder']);
        }

        // Create category channels
        $this->info('Creating category channels...');
        $this->chatService->createProductCategoryChannels();

        // Ensure storage directories exist
        $this->info('Creating storage directories...');
        $directories = [
            'chat/files',
            'chat/thumbnails',
            'chat/voice',
            'chat/temp',
        ];

        foreach ($directories as $directory) {
            if (!is_dir(storage_path("app/public/{$directory}"))) {
                mkdir(storage_path("app/public/{$directory}"), 0755, true);
                $this->info("Created directory: {$directory}");
            }
        }

        // Create symbolic link for storage if it doesn't exist
        if (!is_link(public_path('storage'))) {
            $this->call('storage:link');
        }

        $this->info('✅ Global Chat System setup completed!');
        $this->info('');
        $this->info('Next steps:');
        $this->info('1. Start the queue worker: php artisan queue:work');
        $this->info('2. Start the WebSocket server: php artisan reverb:start');
        $this->info('3. The chat system is now ready to use!');
    }
}
