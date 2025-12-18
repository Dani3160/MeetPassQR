<?php

namespace App\Repositories;

use App\Models\Guest;
use Illuminate\Database\Eloquent\Collection;

class GuestRepository
{
    public function findByEvent(int $eventId, ?bool $attendStatus = null): Collection
    {
        $query = Guest::where('id_event', $eventId);

        if ($attendStatus !== null) {
            $query->where('guest_status', $attendStatus);
        }

        return $query->get();
    }

    public function findByEventPaginated(int $eventId, ?bool $attendStatus = null, int $page = 1, int $perPage = 10): array
    {
        $query = Guest::where('id_event', $eventId);

        if ($attendStatus !== null) {
            $query->where('guest_status', $attendStatus);
        }

        $total = $query->count();
        $guests = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return [
            'data' => $guests,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }

    public function findById(int $guestId): ?Guest
    {
        return Guest::where('id_guest', $guestId)->first();
    }

    public function findByIdAndEvent(int $guestId, int $eventId): ?Guest
    {
        return Guest::where('id_guest', $guestId)
            ->where('id_event', $eventId)
            ->first();
    }

    public function isGuestInvited(int $guestId, int $eventId): bool
    {
        return $this->findByIdAndEvent($guestId, $eventId) !== null;
    }

    public function hasAttended(int $guestId, int $eventId): bool
    {
        $guest = $this->findByIdAndEvent($guestId, $eventId);
        
        return $guest && $guest->hasAttended();
    }

    public function checkIn(int $guestId, bool $isFirstCheckIn = false): bool
    {
        $guest = $this->findById($guestId);
        
        if (!$guest) {
            return false;
        }

        $data = ['guest_status' => true];

        if ($isFirstCheckIn) {
            $data['guest_time_arrival'] = now();
        }

        return $guest->update($data);
    }

    public function updateProfile(int $guestId, array $data): bool
    {
        $guest = $this->findById($guestId);
        
        if (!$guest) {
            return false;
        }

        return $guest->update($data);
    }

    public function create(array $data): Guest
    {
        return Guest::create($data);
    }

    public function delete(Guest $guest): bool
    {
        return $guest->delete();
    }

    public function isGuestExistsByName(string $name, int $eventId): bool
    {
        return Guest::where('guest_name', $name)
            ->where('id_event', $eventId)
            ->exists();
    }

    public function isGuestExistsByNameAndTitle(string $name, string $title, int $eventId): bool
    {
        return Guest::where('guest_name', $name)
            ->where('guest_title', $title)
            ->where('id_event', $eventId)
            ->exists();
    }

    public function findByIdsAndEvent(array $guestIds, int $eventId): Collection
    {
        return Guest::whereIn('id_guest', $guestIds)
            ->where('id_event', $eventId)
            ->get();
    }
}
