<?php

namespace Database\Seeders;

use App\Models\ChatChannel;
use App\Models\ChatUserSetting;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ChatSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default public channels
        $defaultChannels = [
            [
                'name' => '💬 General',
                'slug' => 'general',
                'description' => 'General discussion for all topics',
                'type' => 'public',
                'icon' => '💬',
                'color' => '#3B82F6',
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => '🛍️ Marketplace',
                'slug' => 'marketplace',
                'description' => 'Discuss products, share recommendations, and find great deals',
                'type' => 'public',
                'icon' => '🛍️',
                'color' => '#10B981',
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => '💡 Help & Support',
                'slug' => 'help-support',
                'description' => 'Get help with orders, products, and platform features',
                'type' => 'public',
                'icon' => '💡',
                'color' => '#F59E0B',
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => '🎮 Gaming',
                'slug' => 'gaming',
                'description' => 'Discuss gaming products, reviews, and recommendations',
                'type' => 'public',
                'icon' => '🎮',
                'color' => '#8B5CF6',
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => '📱 Tech Talk',
                'slug' => 'tech-talk',
                'description' => 'Technology discussions and product reviews',
                'type' => 'public',
                'icon' => '📱',
                'color' => '#EF4444',
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => '🎉 Community Events',
                'slug' => 'community-events',
                'description' => 'Stay updated on sales, events, and community activities',
                'type' => 'public',
                'icon' => '🎉',
                'color' => '#EC4899',
                'is_active' => true,
                'created_by' => 1,
            ],
        ];

        foreach ($defaultChannels as $channelData) {
            ChatChannel::firstOrCreate(
                ['slug' => $channelData['slug']],
                $channelData
            );
        }

        // Create category-based channels if categories exist
        $categories = Category::all();
        foreach ($categories as $category) {
            ChatChannel::firstOrCreate(
                ['category_id' => $category->id],
                [
                    'name' => "🏷️ {$category->name}",
                    'slug' => "category-" . Str::slug($category->name),
                    'description' => "Discuss products in the {$category->name} category",
                    'type' => 'category',
                    'icon' => '🏷️',
                    'color' => '#6366F1',
                    'is_active' => true,
                    'created_by' => 1,
                    'category_id' => $category->id,
                ]
            );
        }

        // Create default chat settings for existing users
        $users = User::all();
        foreach ($users as $user) {
            ChatUserSetting::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'notification_settings' => [
                        'mentions' => true,
                        'direct_messages' => true,
                        'channel_messages' => false,
                        'replies' => true,
                        'system' => true,
                        'email_notifications' => false,
                        'push_notifications' => true,
                        'desktop_notifications' => true,
                        'sound_enabled' => true,
                        'vibration_enabled' => true
                    ],
                    'privacy_settings' => [
                        'show_online_status' => true,
                        'show_last_seen' => true,
                        'allow_direct_messages' => true,
                        'read_receipts' => true,
                        'typing_indicators' => true
                    ],
                    'appearance_settings' => [
                        'theme' => 'auto',
                        'font_size' => 'medium',
                        'compact_mode' => false,
                        'show_avatars' => true,
                        'show_timestamps' => true,
                        'message_grouping' => true,
                        'emoji_style' => 'native'
                    ],
                    'status' => 'offline',
                    'is_online' => false,
                ]
            );
        }

        // Auto-join all users to General channel
        $generalChannel = ChatChannel::where('slug', 'general')->first();
        if ($generalChannel) {
            foreach ($users as $user) {
                if (!$generalChannel->isMember($user->id)) {
                    $generalChannel->addMember($user->id, 'member');
                }
            }
        }

        $this->command->info('Chat system seeded successfully with default channels and user settings.');
    }
}
