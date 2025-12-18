<?php

namespace App\Services;

use App\Repositories\GuestRepository;
use App\Services\PusherService;
use App\Services\FourVisionMediaService;
use Illuminate\Support\Facades\Log;

class GuestService
{
    public function __construct(
        private GuestRepository $guestRepository,
        private PusherService $pusherService,
        private FourVisionMediaService $fourVisionMediaService
    ) {}

    public function checkIn(int $guestId, int $eventId, string $userToken): array
    {
        // Validate guest is invited to event
        if (!$this->guestRepository->isGuestInvited($guestId, $eventId)) {
            return [
                'success' => false,
                'message' => 'Guest not found in event list.',
            ];
        }

        // Check if first time check-in
        $isFirstCheckIn = !$this->guestRepository->hasAttended($guestId, $eventId);

        // Perform check-in
        $success = $this->guestRepository->checkIn($guestId, $isFirstCheckIn);

        if (!$success) {
            return [
                'success' => false,
                'message' => 'Update failed. Scan barcode again.',
            ];
        }

        $guest = $this->guestRepository->findById($guestId);
        $response = [
            'success' => true,
            'message' => "Welcome {$guest->guest_name}",
        ];

        // Send Pusher notification
        $guestData = $this->getGuestDataForNotification($guest);
        $pusherResult = $this->pusherService->trigger($userToken, (string) $eventId, [
            'message' => $guestData,
        ]);

        $response['notification'] = $pusherResult ? $guestData : false;

        // Submit to 4Vision Media API only on first check-in
        if ($isFirstCheckIn) {
            try {
                $this->fourVisionMediaService->submitGuest($guest);
            } catch (\Exception $e) {
                Log::error('4Vision Media API error: ' . $e->getMessage());
            }
        }

        return $response;
    }

    public function checkOut(int $guestId, int $eventId, string $userToken): array
    {
        // Validate guest is invited to event
        if (!$this->guestRepository->isGuestInvited($guestId, $eventId)) {
            return [
                'success' => false,
                'message' => 'Guest not found in event list.',
            ];
        }

        // Check if guest has checked in
        if (!$this->guestRepository->hasAttended($guestId, $eventId)) {
            return [
                'success' => false,
                'message' => 'Guest has not checked in',
            ];
        }

        // Perform check-out
        $success = $this->guestRepository->updateProfile($guestId, [
            'guest_time_leave' => now(),
        ]);

        if (!$success) {
            return [
                'success' => false,
                'message' => 'Update failed. Scan barcode again.',
            ];
        }

        $guest = $this->guestRepository->findById($guestId);
        $response = [
            'success' => true,
            'message' => "Bye {$guest->guest_name}",
        ];

        // Send Pusher notification
        $guestData = $this->getGuestDataForNotification($guest);
        $guestData['status'] = 'checkOut';
        $pusherResult = $this->pusherService->trigger($userToken, (string) $eventId, [
            'message' => $guestData,
        ]);

        $response['notification'] = $pusherResult ? $guestData : false;

        return $response;
    }

    public function updateProfileAndCheckIn(
        int $guestId,
        int $eventId,
        array $profileData,
        string $userToken
    ): array {
        // Validate guest exists in event
        $guest = $this->guestRepository->findByIdAndEvent($guestId, $eventId);

        if (!$guest) {
            return [
                'success' => false,
                'message' => 'Guest not found in event list.',
            ];
        }

        // Validate guest name is not empty
        if (empty($profileData['guest_name'])) {
            return [
                'success' => false,
                'message' => 'Nama peserta tidak boleh kosong',
            ];
        }

        // Check if first time check-in
        $isFirstCheckIn = !$guest->hasAttended();

        // Prepare update data
        $updateData = [
            'guest_name' => $profileData['guest_name'],
        ];

        if (!empty($profileData['guest_title'])) {
            $updateData['guest_title'] = $profileData['guest_title'];
        }

        if (!empty($profileData['guest_phone'])) {
            $updateData['guest_phone'] = $profileData['guest_phone'];
        }

        if (!empty($profileData['guest_email'])) {
            $updateData['guest_email'] = $profileData['guest_email'];
        }

        // Also check-in the guest
        $updateData['guest_status'] = true;
        if ($isFirstCheckIn) {
            $updateData['guest_time_arrival'] = now();
        }

        // Update guest
        $success = $this->guestRepository->updateProfile($guestId, $updateData);

        if (!$success) {
            return [
                'success' => false,
                'message' => 'Update failed.',
            ];
        }

        // Refresh guest data
        $guest->refresh();

        // Send Pusher notification
        $guestData = $this->getGuestDataForNotification($guest);
        $pusherResult = $this->pusherService->trigger($userToken, (string) $eventId, [
            'message' => $guestData,
        ]);

        // Submit to 4Vision Media API only on first check-in
        if ($isFirstCheckIn) {
            try {
                $this->fourVisionMediaService->submitGuest($guest);
            } catch (\Exception $e) {
                Log::error('4Vision Media API error: ' . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'message' => "Profile berhasil diupdate dan check-in berhasil. Selamat datang {$guest->guest_name}!",
            'notification' => $pusherResult ? $guestData : false,
        ];
    }

    private function getGuestDataForNotification($guest): array
    {
        return [
            'id_guest' => $guest->id_guest,
            'guest_name' => $guest->guest_name,
            'guest_title' => $guest->guest_title,
            'guest_address' => $guest->guest_address,
            'guest_email' => $guest->guest_email,
            'guest_phone' => $guest->guest_phone,
            'guest_label' => $guest->guest_label,
            'guest_status' => $guest->guest_status,
            'guest_time_arrival' => $guest->guest_time_arrival?->toDateTimeString(),
            'guest_time_leave' => $guest->guest_time_leave?->toDateTimeString(),
            'guest_pic' => $guest->guest_pic,
            'id_event' => $guest->id_event,
            'id_session' => $guest->id_session,
            'status' => 'checkIn',
        ];
    }
}
