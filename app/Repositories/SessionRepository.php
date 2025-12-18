<?php

namespace App\Repositories;

use App\Models\Session;
use Illuminate\Database\Eloquent\Collection;

class SessionRepository
{
    public function findByEvent(int $eventId): Collection
    {
        return Session::where('id_event', $eventId)->get();
    }

    public function findById(int $sessionId): ?Session
    {
        return Session::where('id_session', $sessionId)->first();
    }

    public function create(array $data): Session
    {
        return Session::create($data);
    }

    public function update(Session $session, array $data): bool
    {
        return $session->update($data);
    }

    public function delete(Session $session): bool
    {
        return $session->delete();
    }

    public function findByNameAndEvent(string $sessionName, int $eventId): ?Session
    {
        return Session::where('id_event', $eventId)
            ->where('name_session', $sessionName)
            ->first();
    }
}
