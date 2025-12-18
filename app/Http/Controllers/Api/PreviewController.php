<?php

namespace App\Http\Controllers\Api;

use App\Repositories\EventRepository;
use App\Repositories\GuestRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreviewController
{
    public function __construct(
        private EventRepository $eventRepository,
        private GuestRepository $guestRepository,
        private UserRepository $userRepository
    ) {}

    public function getEventData(Request $request): JsonResponse
    {
        $eventIdentifier = $request->query('eventId') ?? $request->query('eventSlug');

        if (empty($eventIdentifier)) {
            return response()->json(['success' => false, 'message' => 'EventId or EventSlug required'], 400);
        }

        // Support both slug and ID for backward compatibility
        $event = $this->eventRepository->findBySlugOrId($eventIdentifier);
        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }

        // Get user token for Pusher channel
        $user = $this->userRepository->findById($event->id_user);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Get all checked-in guests
        $guests = $this->guestRepository->findByEvent($event->id_event, true);

        // Get statistics
        $allGuests = $this->guestRepository->findByEvent($event->id_event);
        $totalGuests = $allGuests->count();
        $totalCheckIn = $allGuests->where('guest_status', true)->count();
        $totalCheckOut = $allGuests->where('guest_status', true)
            ->filter(function ($guest) {
                return $guest->guest_time_leave && 
                       $guest->guest_time_leave->format('Y-m-d H:i:s') !== '1970-01-02 00:00:00';
            })->count();
        $totalStay = $allGuests->where('guest_status', true)
            ->filter(function ($guest) {
                return !$guest->guest_time_leave || 
                       $guest->guest_time_leave->format('Y-m-d H:i:s') === '1970-01-02 00:00:00';
            })->count();
        $totalNotAttend = $allGuests->where('guest_status', false)->count();

        // Get guests currently at event (checked in but not checked out)
        $guestsAtEvent = $allGuests->where('guest_status', true)
            ->filter(function ($guest) {
                return !$guest->guest_time_leave || 
                       $guest->guest_time_leave->format('Y-m-d H:i:s') === '1970-01-02 00:00:00';
            })
            ->sortByDesc('guest_time_arrival')
            ->take(220);

        // Get event logo - check if event_default_guest_pic exists and is not default avatar
        $eventLogo = null;
        if ($event->event_default_guest_pic && 
            $event->event_default_guest_pic !== '/event/avatar.png' &&
            $event->event_default_guest_pic !== 'event/avatar.png') {
            $eventLogo = \Illuminate\Support\Facades\Storage::disk('public')->url($event->event_default_guest_pic);
        }

        return response()->json([
            'success' => true,
            'event' => [
                'id' => $event->id_event,
                'name' => $event->name_event,
                'date' => $event->date_event->format('Y-m-d'),
                'location' => $event->location_event,
                'logo' => $eventLogo,
            ],
            'statistics' => [
                'total_guests' => $totalGuests,
                'total_check_in' => $totalCheckIn,
                'total_check_out' => $totalCheckOut,
                'total_stay' => $totalStay,
                'total_not_attend' => $totalNotAttend,
            ],
            'pusher' => [
                'key' => config('services.pusher.key'),
                'cluster' => config('services.pusher.cluster'),
                'channel' => $user->token,
                'event' => (string) $event->id_event,
            ],
            'guests' => $guestsAtEvent->map(function ($guest) {
                $imageUrl = config('app.url') . '/img' . $guest->guest_pic;

                return [
                    'id' => $guest->id_guest,
                    'name' => $guest->guest_name,
                    'title' => $guest->guest_title,
                    'image' => $imageUrl,
                    'label' => $guest->guest_label,
                    'status' => $guest->guest_status ? 'checkIn' : 'notCheckedIn',
                    'arrival' => $guest->guest_time_arrival?->toDateTimeString(),
                ];
            })->values(),
        ]);
    }
}
