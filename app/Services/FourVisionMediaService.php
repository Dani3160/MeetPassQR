<?php

namespace App\Services;

use App\Models\Guest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FourVisionMediaService
{
    private string $apiUrl = 'https://www.4visionmedia.com/api/event/4/submit-form';
    private string $apiKey = 'N8hbC81xiJmlrQ8S';
    private string $apiSecret = 'iYcuPqNACKpOZcEtM7w';

    public function submitGuest(Guest $guest): bool
    {
        // Skip if name is empty
        if (empty($guest->guest_name)) {
            Log::info('Guest name is empty, skipping 4Vision Media API');
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'API-Key' => $this->apiKey,
                    'API-Secret' => $this->apiSecret,
                ])
                ->asForm()
                ->post($this->apiUrl, [
                    'name' => $guest->guest_name,
                    'email' => $guest->guest_email ?? '',
                    'phone' => $guest->guest_phone ?? '',
                    'coop_name' => $guest->guest_title ?? '',
                ]);

            if ($response->successful()) {
                Log::info("4Vision Media API success: Guest {$guest->guest_name} submitted successfully");
                return true;
            } else {
                Log::error("4Vision Media API error: HTTP {$response->status()} - Response: " . substr($response->body(), 0, 200));
                return false;
            }
        } catch (\Exception $e) {
            Log::error('4Vision Media API exception: ' . $e->getMessage());
            return false;
        }
    }
}
