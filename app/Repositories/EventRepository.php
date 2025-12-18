<?php

namespace App\Repositories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;

class EventRepository
{
    public function findByUser(int $userId): Collection
    {
        return Event::where('id_user', $userId)
            ->orderBy('date_event', 'desc')
            ->get();
    }

    public function findByUserPaginated(int $userId, int $page = 1, int $perPage = 6): array
    {
        $query = Event::where('id_user', $userId)
            ->orderBy('date_event', 'asc');

        $total = $query->count();
        $events = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return [
            'data' => $events,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }

    public function findById(int $eventId): ?Event
    {
        return Event::where('id_event', $eventId)->first();
    }

    public function findBySlug(string $slug): ?Event
    {
        return Event::where('slug', $slug)->first();
    }

    public function findBySlugOrId(string|int $identifier): ?Event
    {
        // Try to find by ID first (if numeric), otherwise by slug
        if (is_numeric($identifier)) {
            return $this->findById((int) $identifier);
        }
        return $this->findBySlug($identifier);
    }

    public function findByIdAndUser(int $eventId, int $userId): ?Event
    {
        return Event::where('id_event', $eventId)
            ->where('id_user', $userId)
            ->first();
    }

    public function findBySlugAndUser(string $slug, int $userId): ?Event
    {
        return Event::where('slug', $slug)
            ->where('id_user', $userId)
            ->first();
    }

    public function findBySlugOrIdAndUser(string|int $identifier, int $userId): ?Event
    {
        // Try to find by ID first (if numeric), otherwise by slug
        if (is_numeric($identifier)) {
            return $this->findByIdAndUser((int) $identifier, $userId);
        }
        return $this->findBySlugAndUser($identifier, $userId);
    }

    public function create(array $data): Event
    {
        return Event::create($data);
    }

    public function update(Event $event, array $data): bool
    {
        return $event->update($data);
    }

    public function delete(Event $event): bool
    {
        return $event->delete();
    }

    public function findByNameAndUser(string $name, int $userId): ?Event
    {
        return Event::where('name_event', $name)
            ->where('id_user', $userId)
            ->first();
    }
}
