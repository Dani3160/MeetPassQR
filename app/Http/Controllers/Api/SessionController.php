<?php

namespace App\Http\Controllers\Api;

use App\Repositories\EventRepository;
use App\Repositories\SessionRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SessionController
{
    public function __construct(
        private SessionRepository $sessionRepository,
        private EventRepository $eventRepository,
        private UserRepository $userRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $token = $request->query('token');
        $eventIdentifier = $request->query('eventId');

        if (empty($token) || empty($eventIdentifier)) {
            return response()->json([], 400);
        }

        $user = $this->userRepository->findByToken($token);
        if (!$user) {
            return response()->json([], 401);
        }

        // Support both slug and ID for backward compatibility
        $event = $this->eventRepository->findBySlugOrIdAndUser($eventIdentifier, $user->id_user);
        if (!$event) {
            return response()->json([], 404);
        }

        $sessions = $this->sessionRepository->findByEvent($event->id_event);

        $response = $sessions->map(function ($session) {
            return [
                'id' => $session->id_session,
                'name' => $session->name_session,
                'name_session' => $session->name_session,
                'time_started' => $session->time_started_session,
                'time_started_session' => $session->time_started_session,
                'time_ended' => $session->time_ended_session,
                'time_ended_session' => $session->time_ended_session,
            ];
        });

        return response()->json($response->values());
    }

    public function store(Request $request): JsonResponse
    {
        $token = $request->query('token') ?? $request->input('token');
        $eventIdentifier = $request->query('eventId') ?? $request->input('eventId');

        if (empty($token) || empty($eventIdentifier)) {
            return response()->json(['success' => false, 'message' => 'Token and eventId required'], 400);
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

        $request->validate([
            'name_session' => [
                'required',
                'string',
                'max:255',
                Rule::unique('event_sessions', 'name_session')
                    ->where('id_event', $event->id_event)
            ],
            'time_started_session' => ['required', 'date_format:H:i'],
            'time_ended_session' => ['required', 'date_format:H:i', 'after:time_started_session'],
        ], [
            'name_session.unique' => 'Nama sesi sudah digunakan untuk event ini. Silakan gunakan nama yang berbeda.',
        ]);

        $session = $this->sessionRepository->create([
            'id_event' => $event->id_event,
            'name_session' => $request->input('name_session'),
            'time_started_session' => $request->input('time_started_session'),
            'time_ended_session' => $request->input('time_ended_session'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session Created',
            'data' => [
                'id' => $session->id_session,
                'name' => $session->name_session,
            ],
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $token = $request->query('token');
        $eventIdentifier = $request->query('eventId');

        if (empty($token) || empty($eventIdentifier)) {
            return response()->json([], 400);
        }

        $user = $this->userRepository->findByToken($token);
        if (!$user) {
            return response()->json([], 401);
        }

        // Support both slug and ID for backward compatibility
        $event = $this->eventRepository->findBySlugOrIdAndUser($eventIdentifier, $user->id_user);
        if (!$event) {
            return response()->json([], 404);
        }

        $session = $this->sessionRepository->findById($id);
        if (!$session || $session->id_event != $event->id_event) {
            return response()->json([], 404);
        }

        return response()->json([
            'id' => $session->id_session,
            'name' => $session->name_session,
            'time_started' => $session->time_started_session,
            'time_ended' => $session->time_ended_session,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $token = $request->query('token') ?? $request->input('token');
        $eventIdentifier = $request->query('eventId') ?? $request->input('eventId');

        if (empty($token) || empty($eventIdentifier)) {
            return response()->json(['success' => false, 'message' => 'Token and eventId required'], 400);
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

        $session = $this->sessionRepository->findById($id);
        if (!$session || $session->id_event != $event->id_event) {
            return response()->json(['success' => false, 'message' => 'Session not found'], 404);
        }

        $request->validate([
            'name_session' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('event_sessions', 'name_session')
                    ->where('id_event', $event->id_event)
                    ->ignore($session->id_session, 'id_session')
            ],
            'time_started_session' => ['sometimes', 'date_format:H:i'],
            'time_ended_session' => ['sometimes', 'date_format:H:i', 'after:time_started_session'],
        ], [
            'name_session.unique' => 'Nama sesi sudah digunakan untuk event ini. Silakan gunakan nama yang berbeda.',
        ]);

        $this->sessionRepository->update($session, $request->only([
            'name_session',
            'time_started_session',
            'time_ended_session',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Session Updated',
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $token = $request->query('token') ?? $request->input('token');
        $eventIdentifier = $request->query('eventId') ?? $request->input('eventId');

        if (empty($token) || empty($eventIdentifier)) {
            return response()->json(['success' => false, 'message' => 'Token and eventId required'], 400);
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

        $session = $this->sessionRepository->findById($id);
        if (!$session || $session->id_event != $event->id_event) {
            return response()->json(['success' => false, 'message' => 'Session not found'], 404);
        }

        $this->sessionRepository->delete($session);

        return response()->json([
            'success' => true,
            'message' => 'Session deleted',
        ]);
    }
}
