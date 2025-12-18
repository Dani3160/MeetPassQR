<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Pusher\Pusher;
use Pusher\PusherException;

class PusherService
{
    private ?Pusher $pusher = null;

    public function __construct()
    {
        try {
            $this->pusher = new Pusher(
                config('services.pusher.key', '443fb7eb8273230a07de'),
                config('services.pusher.secret', '85b45764ff857c901bf5'),
                config('services.pusher.app_id', '1101241'),
                [
                    'cluster' => config('services.pusher.cluster', 'mt1'),
                    'useTLS' => true,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Pusher initialization failed: ' . $e->getMessage());
            $this->pusher = null;
        }
    }

    public function trigger(string $channel, string $event, array $data): bool
    {
        if (!$this->pusher) {
            return false;
        }

        try {
            $this->pusher->trigger($channel, $event, $data);
            return true;
        } catch (PusherException $e) {
            Log::error('Pusher error: ' . $e->getMessage());
            return false;
        }
    }
}
