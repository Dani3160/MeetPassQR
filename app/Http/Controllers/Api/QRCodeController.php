<?php

namespace App\Http\Controllers\Api;

use App\Repositories\EventRepository;
use App\Repositories\GuestRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController
{
    public function __construct(
        private GuestRepository $guestRepository,
        private EventRepository $eventRepository,
        private UserRepository $userRepository
    ) {}

    public function generate(Request $request): JsonResponse
    {
        $token = $request->query('token');
        $eventIdentifier = $request->query('eventId');
        $guestId = $request->query('guestId');

        if (empty($token) || empty($eventIdentifier) || empty($guestId)) {
            return response()->json(['success' => false, 'message' => 'Missing parameters'], 400);
        }

        $user = $this->userRepository->findByToken($token);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
        }

        // Support both slug and ID for backward compatibility
        $event = $this->eventRepository->findBySlugOrIdAndUser($eventIdentifier, $user->id_user);
        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }

        $guest = $this->guestRepository->findByIdAndEvent((int) $guestId, $event->id_event);
        if (!$guest) {
            return response()->json(['success' => false, 'message' => 'Guest not found'], 404);
        }

        // Generate QR code URL
        $qrUrl = $this->generateQRUrl($guestId, $event->id_event, $token);

        // Generate QR code as base64
        $qrCode = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->generate($qrUrl);

        return response()->json([
            'success' => true,
            'qr_url' => $qrUrl,
            'qr_code' => 'data:image/png;base64,' . base64_encode($qrCode),
        ]);
    }

    public function download(Request $request)
    {
        $token = $request->query('token');
        $eventIdentifier = $request->query('eventId');
        $guestId = $request->query('guestId');

        if (empty($token) || empty($eventIdentifier) || empty($guestId)) {
            abort(400, 'Missing parameters');
        }

        $user = $this->userRepository->findByToken($token);
        if (!$user) {
            abort(401, 'Invalid token');
        }

        // Support both slug and ID for backward compatibility
        $event = $this->eventRepository->findBySlugOrIdAndUser($eventIdentifier, $user->id_user);
        if (!$event) {
            abort(404, 'Event not found');
        }

        $guest = $this->guestRepository->findByIdAndEvent((int) $guestId, $event->id_event);
        if (!$guest) {
            abort(404, 'Guest not found');
        }

        // Generate QR code URL
        $qrUrl = $this->generateQRUrl($guestId, $event->id_event, $token);

        // Generate QR code
        $qrCode = QrCode::format('png')
            ->size(600)
            ->errorCorrection('H')
            ->generate($qrUrl);

        // Generate filename
        $filename = $this->generateFilename($guest);

        return response($qrCode, 200)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function generateQRUrl(int $guestId, int $eventId, string $token): string
    {
        $baseUrl = config('app.url');
        return "{$baseUrl}/guest-checkin?guestId={$guestId}&eventId={$eventId}&token={$token}";
    }

    private function generateFilename($guest): string
    {
        $title = $guest->guest_title ? preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $guest->guest_title) : '';
        $name = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $guest->guest_name);
        
        if (!empty($title)) {
            return "{$title} - {$name}.png";
        }
        
        return "{$name}.png";
    }
}
