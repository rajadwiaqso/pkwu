<?php

namespace App\Services;

class ChatService
{
    /**
     * Update user presence status.
     * Returns a simple object with a `status` property used by middleware.
     */
    public function updateUserPresence(int $userId, bool $isOnline)
    {
        $settings = new \stdClass();
        $settings->status = $isOnline ? 'online' : 'offline';

        return $settings;
    }

    /**
     * Create product category channels (no-op placeholder).
     * Implement actual channel creation later if chat models are added.
     */
    public function createProductCategoryChannels()
    {
        // Placeholder: keep this idempotent and safe during package discovery.
        return true;
    }
}
