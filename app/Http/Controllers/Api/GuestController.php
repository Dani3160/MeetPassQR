<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CheckInRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Repositories\EventRepository;
use App\Repositories\GuestRepository;
use App\Repositories\UserRepository;
use App\Services\GuestService;
use App\Models\EventCustomField;
use App\Models\GuestCustomFieldValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GuestController
{
    public function __construct(
        private GuestRepository $guestRepository,
        private EventRepository $eventRepository,
        private UserRepository $userRepository,
        private GuestService $guestService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $token = $request->query('token');
        $eventIdentifier = $request->query('eventId');
        $attend = $request->query('attend');
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 10);

        if (empty($token) || empty($eventIdentifier)) {
            return response()->json([], 400);
        }

        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            return response()->json([], 401);
        }

        $event = $this->eventRepository->findBySlugOrId($eventIdentifier);

        if (!$event) {
            return response()->json([], 404);
        }

        $attendStatus = null;
        if ($attend !== null && $attend !== '') {
            $attendStatus = $attend === 'true' ? true : ($attend === 'false' ? false : null);
        }
        
        $pagination = $this->guestRepository->findByEventPaginated($event->id_event, $attendStatus, $page, $perPage);
        $guests = $pagination['data'];

        $customFields = EventCustomField::where('id_event', $event->id_event)
            ->orderBy('field_order')
            ->get();

        $guestsData = $guests->map(function ($guest) use ($customFields) {
            $picName = str_replace(' ', '%20', $guest->guest_pic);
            $imageUrl = config('app.url') . '/img' . $guest->guest_pic;

            $customFieldValues = [];
            foreach ($customFields as $field) {
                $value = GuestCustomFieldValue::where('id_guest', $guest->id_guest)
                    ->where('id_field', $field->id_field)
                    ->first();
                
                if ($value) {
                    $customFieldValues[$field->field_name] = [
                        'id_field' => $field->id_field,
                        'field_label' => $field->field_label,
                        'field_type' => $field->field_type,
                        'value' => $value->getDisplayValue(),
                        'file_path' => $value->file_path,
                        'raw_value' => $value->field_value,
                    ];
                } else {
                    $customFieldValues[$field->field_name] = [
                        'id_field' => $field->id_field,
                        'field_label' => $field->field_label,
                        'field_type' => $field->field_type,
                        'value' => null,
                        'file_path' => null,
                        'raw_value' => null,
                    ];
                }
            }

            return [
                'id_guest' => $guest->id_guest,
                'guest_name' => $guest->guest_name,
                'guest_title' => $guest->guest_title,
                'guest_address' => $guest->guest_address,
                'guest_email' => $guest->guest_email,
                'guest_phone' => $guest->guest_phone,
                'guest_pic' => $guest->guest_pic,
                'guest_status' => $guest->guest_status,
                'guest_label' => $guest->guest_label,
                'guest_time_arrival' => $guest->guest_time_arrival?->toDateTimeString(),
                'guest_time_leave' => $guest->guest_time_leave?->toDateTimeString(),
                'id_session' => $guest->id_session,
                'custom_fields' => $customFieldValues,
            ];
        });

        return response()->json([
            'data' => $guestsData->values(),
            'pagination' => [
                'total' => $pagination['total'],
                'per_page' => $pagination['per_page'],
                'current_page' => $pagination['current_page'],
                'last_page' => $pagination['last_page'],
            ]
        ]);
    }

    public function show(Request $request): JsonResponse
    {
        $token = $request->query('token');
        $guestId = $request->query('guestId');

        if (empty($token) || empty($guestId)) {
            return response()->json([], 400);
        }

        $guest = $this->guestRepository->findById((int) $guestId);

        if (!$guest) {
            return response()->json([], 404);
        }

        $picName = str_replace(' ', '%20', $guest->guest_pic);
        $imageUrl = config('app.url') . '/img/guest/' . $picName;

        $customFieldValues = [];
        if ($guest->id_event) {
            $customFields = \App\Models\EventCustomField::where('id_event', $guest->id_event)
                ->orderBy('field_order')
                ->get();
            
            foreach ($customFields as $field) {
                $value = \App\Models\GuestCustomFieldValue::where('id_guest', $guest->id_guest)
                    ->where('id_field', $field->id_field)
                    ->first();
                
                if ($value) {
                    $customFieldValues[$field->id_field] = [
                        'id_field' => $field->id_field,
                        'field_name' => $field->field_name,
                        'field_label' => $field->field_label,
                        'field_type' => $field->field_type,
                        'value' => $value->field_value,
                        'file_path' => $value->file_path,
                        'raw_value' => $value->field_value,
                    ];
                }
            }
        }

        return response()->json([
            'id' => $guest->id_guest,
            'name' => $guest->guest_name,
            'title' => $guest->guest_title,
            'email' => $guest->guest_email,
            'phone' => $guest->guest_phone,
            'image' => $imageUrl,
            'address' => $guest->guest_address,
            'attend' => $guest->guest_status,
            'arrival' => $guest->guest_time_arrival?->toDateTimeString(),
            'leave' => $guest->guest_time_leave?->toDateTimeString(),
            'guest_time_leave' => $guest->guest_time_leave?->toDateTimeString(),
            'custom_fields' => $customFieldValues,
        ]);
    }

    public function checkIn(CheckInRequest $request): JsonResponse
    {
        $token = $request->input('token') ?? $request->query('token');
        $eventIdentifier = $request->input('eventId') ?? $request->query('eventId');
        $guestId = $request->query('guestId');

        if (empty($token) || empty($eventIdentifier) || empty($guestId)) {
            return response()->json([
                'success' => false,
                'message' => 'Missing required parameters',
            ], 400);
        }

        $user = $this->userRepository->findByToken($token);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 401);
        }

        $event = $this->eventRepository->findBySlugOrIdAndUser($eventIdentifier, $user->id_user);
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found',
            ], 404);
        }

        $result = $this->guestService->checkIn((int) $guestId, $event->id_event, $token);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function updateProfileAndCheckIn(UpdateProfileRequest $request): JsonResponse
    {
        $token = $request->input('token') ?? $request->query('token');
        $eventIdentifier = $request->input('eventId') ?? $request->query('eventId');
        $guestId = $request->query('guestId');

        if (empty($token) || empty($eventIdentifier) || empty($guestId)) {
            return response()->json([
                'success' => false,
                'message' => 'Missing required parameters',
            ], 400);
        }

        $user = $this->userRepository->findByToken($token);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 401);
        }

        $event = $this->eventRepository->findBySlugOrId($eventIdentifier);
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found',
            ], 404);
        }

        $eventOwner = $this->userRepository->findById($event->id_user);
        $userToken = $eventOwner?->token ?? $token;

        $result = $this->guestService->updateProfileAndCheckIn(
            (int) $guestId,
            $event->id_event,
            [
                'guest_title' => $request->input('guestTitle'),
                'guest_name' => $request->input('guestName'),
                'guest_phone' => $request->input('guestPhone'),
                'guest_email' => $request->input('guestEmail'),
            ],
            $userToken
        );

        if ($request->has('custom_fields')) {
            $customFieldsData = $request->input('custom_fields');
            if (is_string($customFieldsData)) {
                $customFieldsData = json_decode($customFieldsData, true) ?? [];
            }
            
            if (is_array($customFieldsData) && !empty($customFieldsData)) {
                $guest = $this->guestRepository->findById((int) $guestId);
                if ($guest) {
                    $this->saveCustomFields($guest->id_guest, $event->id_event, $customFieldsData, $request);
                }
            }
        }

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function checkOut(Request $request): JsonResponse
    {
        $token = $request->input('token') ?? $request->query('token');
        $eventIdentifier = $request->input('eventId') ?? $request->query('eventId');
        $guestId = $request->query('guestId');

        if (empty($token) || empty($eventIdentifier) || empty($guestId)) {
            return response()->json([
                'success' => false,
                'message' => 'Missing required parameters',
            ], 400);
        }

        $user = $this->userRepository->findByToken($token);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 401);
        }

        $event = $this->eventRepository->findBySlugOrIdAndUser($eventIdentifier, $user->id_user);
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found',
            ], 404);
        }

        $result = $this->guestService->checkOut((int) $guestId, $event->id_event, $token);

        return response()->json($result, $result['success'] ? 200 : 400);
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

        $event = $this->eventRepository->findBySlugOrIdAndUser($eventIdentifier, $user->id_user);
        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }

        if ($event->date_event && Carbon::parse($event->date_event)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menambah tamu karena tanggal event sudah lewat',
            ], 403);
        }

        if ($event->guest_total) {
            $currentGuestCount = $this->guestRepository->findByEvent($event->id_event)->count();
            if ($currentGuestCount >= $event->guest_total) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak dapat menambah tamu karena jumlah tamu sudah mencapai batas maksimal ({$event->guest_total} tamu)",
                ], 403);
            }
        }

        $request->validate([
            'guest_title' => ['nullable', 'string', 'max:255'],
            'guest_name' => ['required', 'string', 'max:100'],
            'guest_address' => ['required', 'string', 'max:100'],
            'guest_email' => ['nullable', 'email', 'max:100'],
            'guest_phone' => ['nullable', 'string', 'max:20'],
            'guest_label' => ['nullable', 'integer', 'in:0,1'],
                'id_session' => ['nullable', 'integer', 'exists:event_sessions,id_session'],
            'guest_pic' => ['nullable', 'string', 'max:255'],
        ]);

        $guest = $this->guestRepository->create([
            'id_event' => $event->id_event,
            'guest_title' => $request->input('guest_title'),
            'guest_name' => $request->input('guest_name'),
            'guest_address' => $request->input('guest_address'),
            'guest_email' => $request->input('guest_email'),
            'guest_phone' => $request->input('guest_phone'),
            'guest_label' => $request->input('guest_label', 0),
            'id_session' => $request->input('id_session'),
            'guest_pic' => $request->input('guest_pic', $event->event_default_guest_pic),
        ]);

        if ($request->has('custom_fields')) {
            $customFieldsData = $request->input('custom_fields');
            if (is_string($customFieldsData)) {
                $customFieldsData = json_decode($customFieldsData, true) ?? [];
            }
            if (is_array($customFieldsData) && !empty($customFieldsData)) {
                $this->saveCustomFields($guest->id_guest, $event->id_event, $customFieldsData, $request);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Guest Created',
            'data' => [
                'id' => $guest->id_guest,
                'name' => $guest->guest_name,
            ],
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $token = $request->query('token') ?? $request->input('token');
        $eventIdentifier = $request->query('eventId') ?? $request->input('eventId');
        
        \Log::info('GuestController@update - Request received', [
            'guestId' => $id,
            'eventIdentifier' => $eventIdentifier,
            'eventIdentifierType' => gettype($eventIdentifier),
            'token' => $token ? 'present' : 'missing',
            'allQueryParams' => $request->query(),
            'allInputParams' => $request->input()
        ]);

        if (empty($token) || empty($eventIdentifier)) {
            \Log::error('GuestController@update - Missing required params', [
                'token' => empty($token),
                'eventIdentifier' => empty($eventIdentifier)
            ]);
            return response()->json(['success' => false, 'message' => 'Token and eventId required'], 400);
        }

        $user = $this->userRepository->findByToken($token);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
        }

        $event = $this->eventRepository->findBySlugOrIdAndUser($eventIdentifier, $user->id_user);
        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }

        $guest = $this->guestRepository->findByIdAndEvent($id, $event->id_event);
        if (!$guest) {
            return response()->json(['success' => false, 'message' => 'Guest not found'], 404);
        }

        $request->validate([
            'guest_title' => ['sometimes', 'string', 'max:255'],
            'guest_name' => ['sometimes', 'string', 'max:100'],
            'guest_address' => ['sometimes', 'string', 'max:100'],
            'guest_email' => ['nullable', 'email', 'max:100'],
            'guest_phone' => ['nullable', 'string', 'max:20'],
            'guest_label' => ['nullable', 'integer', 'in:0,1'],
                'id_session' => ['nullable', 'integer', 'exists:event_sessions,id_session'],
            'guest_pic' => ['nullable', 'string', 'max:255'],
        ]);

        $allData = $request->all();
        
        $allowedFields = [
            'guest_title',
            'guest_name',
            'guest_address',
            'guest_email',
            'guest_phone',
            'guest_label',
            'id_session',
            'guest_pic',
        ];
        
        $updateData = [];
        $contentType = $request->header('Content-Type', '');
        $isMultipart = str_contains($contentType, 'multipart/form-data');
        
        if ($isMultipart && $request->method() === 'PUT') {
            $rawContent = $request->getContent();
            
            \Log::info('GuestController@update - Parsing FormData fields from raw content', [
                'contentLength' => strlen($rawContent),
                'hasGuestName' => str_contains($rawContent, 'name="guest_name"'),
                'hasGuestTitle' => str_contains($rawContent, 'name="guest_title"'),
            ]);
            
            foreach ($allowedFields as $field) {
                $value = null;
                $foundVia = null;
                
                if (isset($allData[$field])) {
                    $value = $allData[$field];
                    $foundVia = 'allData';
                } elseif ($request->has($field)) {
                    $value = $request->input($field);
                    $foundVia = 'input';
                } else {
                    
                    $fieldMarker = 'name="' . $field . '"';
                    $fieldPos = strpos($rawContent, $fieldMarker);
                    
                    if ($fieldPos !== false) {
                        $section = substr($rawContent, $fieldPos);
                        
                        $lines = preg_split('/\r\n|\r|\n/', $section);
                        
                        $valueLines = [];
                        $foundName = false;
                        $foundBlankLine = false;
                        
                        foreach ($lines as $line) {
                            if (str_contains($line, $fieldMarker)) {
                                $foundName = true;
                                continue;
                            }
                            
                            if ($foundName) {
                                if (str_starts_with(trim($line), '------') || str_starts_with(trim($line), '--')) {
                                    break;
                                }
                                
                                if (trim($line) === '' && !$foundBlankLine) {
                                    $foundBlankLine = true;
                                    continue;
                                }
                                
                                if ($foundBlankLine) {
                                    $valueLines[] = $line;
                                }
                            }
                        }
                        
                        if (!empty($valueLines)) {
                            $value = trim(implode("\n", $valueLines));
                            
                            if (!str_starts_with($value, '------') && 
                                !str_contains($value, 'Content-Disposition') &&
                                !str_contains($value, 'form-data') &&
                                !str_contains($value, 'boundary')) {
                                $foundVia = 'rawContent_manual';
                                
                                // Handle empty values
                                if ($value === 'null' || $value === 'undefined' || $value === '') {
                                    if (in_array($field, ['guest_email', 'guest_phone', 'guest_title'])) {
                                        $value = $value === '' ? '' : null;
                                    } else {
                                        $value = ($value === '') ? null : $value;
                                    }
                                }
                            } else {
                                $value = null; // Invalid match
                            }
                        }
                    }
                }
                
                \Log::info("GuestController@update - Field parsed: {$field}", [
                    'field' => $field,
                    'value' => $value,
                    'foundVia' => $foundVia,
                    'inRawContent' => str_contains($rawContent, 'name="' . $field . '"')
                ]);
                
                // Include field if value is provided
                if ($value !== null) {
                    // Convert to appropriate type
                    if ($field === 'guest_label' || $field === 'id_session') {
                        $updateData[$field] = ($value === '' || $value === null) ? null : (int) $value;
                    } else {
                        $updateData[$field] = $value;
                    }
                }
            }
        } else {
            // Normal request handling
            foreach ($allowedFields as $field) {
                $value = null;
                
                if (isset($allData[$field])) {
                    $value = $allData[$field];
                } elseif ($request->has($field)) {
                    $value = $request->input($field);
                } elseif ($request->get($field) !== null) {
                    $value = $request->get($field);
                }
                
                if ($value !== null) {
                    if ($field === 'guest_label' || $field === 'id_session') {
                        $updateData[$field] = ($value === '' || $value === null) ? null : (int) $value;
                    } else {
                        $updateData[$field] = $value;
                    }
                }
            }
        }
        
        \Log::info('GuestController@update - Update data prepared', [
            'updateData' => $updateData,
            'allData' => $allData,
            'requestAll' => $request->all(),
            'requestInput' => $request->input(),
            'method' => $request->method(),
            'contentType' => $contentType,
            'isMultipart' => $isMultipart
        ]);
        
        // Update the guest profile
        if (!empty($updateData)) {
            $this->guestRepository->updateProfile($id, $updateData);
        } else {
            \Log::warning('GuestController@update - No data to update', [
                'allData' => $allData,
                'allowedFields' => $allowedFields,
                'isMultipart' => $isMultipart
            ]);
        }

        // Handle custom fields
        // FormData dengan PUT method - Laravel mungkin tidak membaca semua fields
        // Parse dari raw content jika tidak ditemukan di request
        $allRequestData = $request->all();
        $customFieldsData = null;
        
        // Try different methods to get custom_fields
        if (isset($allRequestData['custom_fields'])) {
            $customFieldsData = $allRequestData['custom_fields'];
        } elseif ($request->has('custom_fields')) {
            $customFieldsData = $request->input('custom_fields');
        } elseif ($request->get('custom_fields')) {
            $customFieldsData = $request->get('custom_fields');
        } else {
            // Parse from raw content if FormData (Laravel doesn't read FormData properly with PUT method)
            $contentType = $request->header('Content-Type', '');
            if (str_contains($contentType, 'multipart/form-data')) {
                $rawContent = $request->getContent();
                \Log::info('GuestController@update - Parsing raw content for custom_fields', [
                    'contentLength' => strlen($rawContent),
                    'hasCustomFields' => str_contains($rawContent, 'name="custom_fields"')
                ]);
                
                // Parse multipart form data manually - look for custom_fields
                // From log: name="custom_fields" followed by newlines, then JSON, then boundary
                // Try multiple patterns to handle different formats
                $patterns = [
                    // Pattern 1: Match with Content-Disposition header (most specific - from log format)
                    // Format: Content-Disposition: form-data; name="custom_fields" + 2 newlines + JSON + newline + ------
                    '/Content-Disposition:\s*form-data;\s*name="custom_fields"\s*[\r\n]+\s*[\r\n]+\s*(.+?)\s*[\r\n]+\s*------/s',
                    // Pattern 2: Direct match after name="custom_fields" (handle escaped quotes in log)
                    '/name="custom_fields"\s*[\r\n]+\s*[\r\n]+\s*(.+?)\s*[\r\n]+\s*------/s',
                    // Pattern 3: More flexible - match any whitespace after name
                    '/name="custom_fields".*?[\r\n]+\s*[\r\n]+\s*(.+?)\s*[\r\n]+\s*------/s',
                    // Pattern 4: Match until any boundary (-- at start of line)
                    '/name="custom_fields"\s*[\r\n]+\s*[\r\n]+\s*(.+?)\s*[\r\n]+\s*--/s',
                ];
                
                $customFieldsJson = null;
                $matchedPattern = null;
                foreach ($patterns as $idx => $pattern) {
                    if (preg_match($pattern, $rawContent, $matches)) {
                        $customFieldsJson = trim($matches[1]);
                        $matchedPattern = $idx + 1;
                        \Log::info('GuestController@update - Found custom_fields in raw content', [
                            'patternIndex' => $matchedPattern,
                            'pattern' => $pattern,
                            'json' => $customFieldsJson,
                            'jsonLength' => strlen($customFieldsJson),
                            'matchFull' => substr($matches[0], 0, 300),
                            'matchGroup1' => $matches[1] ?? 'N/A'
                        ]);
                        break;
                    }
                }
                
                // If no pattern matched, try a simpler approach - extract JSON directly
                if (!$customFieldsJson && str_contains($rawContent, 'name="custom_fields"')) {
                    // Find the section containing custom_fields
                    $startPos = strpos($rawContent, 'name="custom_fields"');
                    if ($startPos !== false) {
                        // Get content after custom_fields declaration
                        $section = substr($rawContent, $startPos, 500); // Get next 500 chars
                        
                        // Find the first { and extract until matching }
                        $braceStart = strpos($section, '{');
                        if ($braceStart !== false) {
                            $jsonPart = substr($section, $braceStart);
                            // Find matching closing brace
                            $braceCount = 0;
                            $braceEnd = -1;
                            for ($i = 0; $i < strlen($jsonPart); $i++) {
                                if ($jsonPart[$i] === '{') {
                                    $braceCount++;
                                } elseif ($jsonPart[$i] === '}') {
                                    $braceCount--;
                                    if ($braceCount === 0) {
                                        $braceEnd = $i + 1;
                                        break;
                                    }
                                }
                            }
                            
                            if ($braceEnd > 0) {
                                $customFieldsJson = substr($jsonPart, 0, $braceEnd);
                                \Log::info('GuestController@update - Found custom_fields JSON using brace matching', [
                                    'json' => $customFieldsJson,
                                    'jsonLength' => strlen($customFieldsJson)
                                ]);
                            }
                        }
                    }
                }
                
                if ($customFieldsJson) {
                    // The JSON might have escaped quotes from raw content
                    // Try decode as-is first, then try with stripslashes if needed
                    $decoded = json_decode($customFieldsJson, true);
                    $jsonError = json_last_error();
                    
                    if ($jsonError !== JSON_ERROR_NONE) {
                        // Try with unescaped
                        $customFieldsJson = stripslashes($customFieldsJson);
                        $decoded = json_decode($customFieldsJson, true);
                        $jsonError = json_last_error();
                    }
                    
                    if ($jsonError === JSON_ERROR_NONE && is_array($decoded)) {
                        $customFieldsData = $decoded;
                        \Log::info('GuestController@update - Successfully parsed custom_fields from raw content', [
                            'data' => $customFieldsData,
                            'keys' => array_keys($customFieldsData),
                            'originalJson' => $matches[1] ?? null
                        ]);
                    } else {
                        \Log::warning('GuestController@update - Failed to decode custom_fields JSON from raw content', [
                            'json' => $customFieldsJson,
                            'error' => $jsonError,
                            'message' => json_last_error_msg()
                        ]);
                    }
                } else {
                    \Log::warning('GuestController@update - custom_fields not found in raw content', [
                        'rawContentPreview' => substr($rawContent, 0, 1000),
                        'hasCustomFields' => str_contains($rawContent, 'custom_fields'),
                        'customFieldsPosition' => strpos($rawContent, 'custom_fields')
                    ]);
                }
            }
        }
        
        \Log::info('GuestController@update - Checking custom_fields', [
            'has' => $request->has('custom_fields'),
            'input' => $request->input('custom_fields'),
            'get' => $request->get('custom_fields'),
            'allKeys' => array_keys($allRequestData),
            'allData' => $allRequestData,
            'customFieldsData' => $customFieldsData,
            'type' => $customFieldsData ? gettype($customFieldsData) : 'null',
            'contentType' => $request->header('Content-Type'),
            'isMultipart' => str_contains($request->header('Content-Type', ''), 'multipart/form-data'),
            'method' => $request->method()
        ]);
        
        if ($customFieldsData !== null) {
            
            if (is_string($customFieldsData)) {
                $decoded = json_decode($customFieldsData, true);
                $jsonError = json_last_error();
                \Log::info('GuestController@update - JSON decode attempt', [
                    'decoded' => $decoded,
                    'jsonError' => $jsonError,
                    'jsonErrorMessage' => json_last_error_msg()
                ]);
                
                if ($jsonError === JSON_ERROR_NONE && is_array($decoded)) {
                    $customFieldsData = $decoded;
                } else {
                    $customFieldsData = [];
                    \Log::warning('GuestController@update - Failed to decode custom_fields JSON', [
                        'error' => $jsonError,
                        'message' => json_last_error_msg()
                    ]);
                }
            }
            
            // Normalize keys - convert string keys to integers if they are numeric
            if (is_array($customFieldsData)) {
                $normalized = [];
                foreach ($customFieldsData as $key => $value) {
                    // Convert numeric string keys to integers
                    if (is_string($key) && is_numeric($key)) {
                        $normalized[(int)$key] = $value;
                    } else {
                        $normalized[$key] = $value;
                    }
                }
                $customFieldsData = $normalized;
            }
            
            \Log::info('GuestController@update - Processed custom_fields:', [
                'data' => $customFieldsData,
                'keys' => array_keys($customFieldsData),
                'count' => count($customFieldsData)
            ]);
            
            // Always call saveCustomFields, even if empty, to handle deletions
            \Log::info('GuestController@update - Calling saveCustomFields', [
                'guestId' => $id,
                'eventId' => $event->id_event,
                'eventIdInt' => (int) $event->id_event,
                'customFieldsData' => $customFieldsData
            ]);
            
            $this->saveCustomFields($id, $event->id_event, is_array($customFieldsData) ? $customFieldsData : [], $request);
        } else {
            \Log::warning('GuestController@update - No custom_fields in request', [
                'requestAll' => $request->all(),
                'requestKeys' => array_keys($request->all()),
                'hasCustomFields' => $request->has('custom_fields'),
                'inputCustomFields' => $request->input('custom_fields'),
                'getCustomFields' => $request->get('custom_fields')
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Guest Updated',
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

        $guest = $this->guestRepository->findByIdAndEvent($id, $event->id_event);
        if (!$guest) {
            return response()->json(['success' => false, 'message' => 'Guest not found'], 404);
        }

        // Delete custom field values (cascade will handle this, but we delete files manually)
        $customFieldValues = GuestCustomFieldValue::where('id_guest', $id)->get();
        foreach ($customFieldValues as $value) {
            if ($value->file_path && Storage::disk('public')->exists($value->file_path)) {
                Storage::disk('public')->delete($value->file_path);
            }
        }

        $this->guestRepository->delete($guest);

        return response()->json([
            'success' => true,
            'message' => 'Guest deleted',
        ]);
    }

    /**
     * Save custom field values for a guest
     */
    private function saveCustomFields(int $guestId, int $eventId, array $customFields, Request $request): void
    {
        \Log::info('GuestController@saveCustomFields - Start', [
            'guestId' => $guestId,
            'eventId' => $eventId,
            'customFields' => $customFields
        ]);
        
        // Ensure eventId is integer and query with explicit column name
        $eventIdInt = (int) $eventId;
        \Log::info('GuestController@saveCustomFields - Querying custom fields', [
            'eventId' => $eventId,
            'eventIdInt' => $eventIdInt,
            'eventIdType' => gettype($eventId)
        ]);
        
        // Query using id_event column explicitly
        $eventCustomFields = EventCustomField::where('id_event', '=', $eventIdInt)->get();
        
        \Log::info('GuestController@saveCustomFields - Event custom fields found:', [
            'eventId' => $eventIdInt,
            'count' => $eventCustomFields->count(),
            'fields' => $eventCustomFields->map(fn($f) => [
                'id_field' => $f->id_field,
                'id_event' => $f->id_event,
                'field_name' => $f->field_name,
                'field_type' => $f->field_type
            ])->toArray(),
            'sql' => EventCustomField::where('id_event', '=', $eventIdInt)->toSql()
        ]);
        
        if ($eventCustomFields->isEmpty()) {
            \Log::warning('GuestController@saveCustomFields - No custom fields found for event', [
                'eventId' => $eventIdInt,
                'queryResult' => 'empty'
            ]);
            return; // Exit early if no custom fields exist
        }

        foreach ($eventCustomFields as $field) {
            $fieldId = $field->id_field;
            
            \Log::info("GuestController@saveCustomFields - Processing field", [
                'fieldId' => $fieldId,
                'fieldName' => $field->field_name,
                'fieldType' => $field->field_type,
                'inRequest' => isset($customFields[$fieldId]),
                'value' => $customFields[$fieldId] ?? 'NOT_SET'
            ]);
            
            // Convert fieldId to string and integer for matching (frontend might send as string)
            $fieldIdStr = (string) $fieldId;
            $fieldIdInt = (int) $fieldId;
            
            // Try to get value - check all possible key formats
            $value = null;
            $foundKey = null;
            
            // First try direct access
            if (isset($customFields[$fieldIdInt])) {
                $value = $customFields[$fieldIdInt];
                $foundKey = $fieldIdInt;
            } elseif (isset($customFields[$fieldIdStr])) {
                $value = $customFields[$fieldIdStr];
                $foundKey = $fieldIdStr;
            } else {
                // Iterate through all keys to find match
                foreach ($customFields as $key => $val) {
                    if ((int)$key === $fieldIdInt || (string)$key === $fieldIdStr) {
                        $value = $val;
                        $foundKey = $key;
                        \Log::info("GuestController@saveCustomFields - Found value by iteration", [
                            'key' => $key,
                            'keyType' => gettype($key),
                            'value' => $value,
                            'fieldId' => $fieldId,
                            'fieldIdInt' => $fieldIdInt,
                            'fieldIdStr' => $fieldIdStr
                        ]);
                        break;
                    }
                }
            }
            
            if ($value !== null) {
                \Log::info("GuestController@saveCustomFields - Value found", [
                    'fieldId' => $fieldId,
                    'foundKey' => $foundKey,
                    'foundKeyType' => gettype($foundKey),
                    'value' => $value,
                    'valueType' => gettype($value)
                ]);
            }
            
            \Log::info("GuestController@saveCustomFields - Value check", [
                'fieldId' => $fieldId,
                'fieldIdInt' => $fieldIdInt,
                'fieldIdStr' => $fieldIdStr,
                'value' => $value,
                'valueType' => gettype($value),
                'isNull' => $value === null,
                'isEmpty' => $value === '',
                'customFieldsKeys' => array_keys($customFields),
                'customFields' => $customFields
            ]);
            
            // Use the value we found earlier (from the improved matching above)
            // If value is still null, it means the field is not in the request
            if ($value === null) {
                // Only delete if field is not required and explicitly not provided
                // For required fields, keep existing value
                if (!$field->is_required) {
                    $existing = GuestCustomFieldValue::where('id_guest', $guestId)
                        ->where('id_field', $fieldId)
                        ->first();
                    if ($existing) {
                        if ($existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
                            Storage::disk('public')->delete($existing->file_path);
                        }
                        $existing->delete();
                    }
                }
                continue;
            }

            // Process the value - even if it's empty string, we should save it for text fields
            $fieldValueData = [
                'id_guest' => $guestId,
                'id_field' => $fieldId,
            ];
            
            \Log::info("GuestController@saveCustomFields - Processing value", [
                'fieldId' => $fieldId,
                'fieldName' => $field->field_name,
                'fieldType' => $field->field_type,
                'value' => $value,
                'valueType' => gettype($value),
                'valueLength' => is_string($value) ? strlen($value) : null,
                'isEmpty' => empty($value),
                'isNull' => $value === null
            ]);

            if ($field->field_type === 'file') {
                // Handle file upload
                $fileKey = "custom_fields.{$fieldId}";
                if ($request->hasFile($fileKey)) {
                    // Delete old file if exists
                    $existing = GuestCustomFieldValue::where('id_guest', $guestId)
                        ->where('id_field', $fieldId)
                        ->first();
                    if ($existing && $existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
                        Storage::disk('public')->delete($existing->file_path);
                    }
                    
                    $file = $request->file($fileKey);
                    $path = $file->store("guest-files/{$eventId}/{$guestId}", 'public');
                    $fieldValueData['file_path'] = $path;
                    $fieldValueData['field_value'] = null;
                } else if (is_string($value) && !empty($value) && $value !== 'file_upload') {
                    // Keep existing file path
                    $existing = GuestCustomFieldValue::where('id_guest', $guestId)
                        ->where('id_field', $fieldId)
                        ->first();
                    if ($existing && $existing->file_path) {
                        $fieldValueData['file_path'] = $existing->file_path;
                    } else {
                        $fieldValueData['file_path'] = $value;
                    }
                }
            } else if ($field->field_type === 'checkbox') {
                // Checkbox returns array
                if (is_array($value)) {
                    $fieldValueData['field_value'] = json_encode($value);
                } else if (is_string($value)) {
                    // Try to decode if it's JSON string
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $fieldValueData['field_value'] = json_encode($decoded);
                    } else {
                        $fieldValueData['field_value'] = json_encode([$value]);
                    }
                } else if ($value !== null && $value !== '') {
                    $fieldValueData['field_value'] = json_encode([$value]);
                } else {
                    $fieldValueData['field_value'] = json_encode([]);
                }
            } else {
                // Input, textarea, select, radio
                if (is_array($value)) {
                    $fieldValueData['field_value'] = json_encode($value);
                } else {
                    // Allow empty string for text fields
                    $fieldValueData['field_value'] = (string) $value;
                }
            }
            
            // Ensure we have field_value or file_path
            // For textarea and input, empty string is valid
            if (!isset($fieldValueData['field_value']) && !isset($fieldValueData['file_path'])) {
                \Log::warning("GuestController@saveCustomFields - No value or file_path set, skipping", [
                    'fieldId' => $fieldId,
                    'fieldName' => $field->field_name,
                    'fieldType' => $field->field_type,
                    'value' => $value,
                    'fieldValueData' => $fieldValueData
                ]);
                continue;
            }
            
            // For textarea and input, ensure field_value is set even if empty
            if ($field->field_type === 'textarea' || $field->field_type === 'input') {
                if (!isset($fieldValueData['field_value'])) {
                    $fieldValueData['field_value'] = (string) ($value ?? '');
                }
            }

            // Update or create
            \Log::info("GuestController@saveCustomFields - Saving field value", [
                'fieldId' => $fieldId,
                'fieldName' => $field->field_name,
                'fieldType' => $field->field_type,
                'fieldValueData' => $fieldValueData,
                'hasFieldValue' => isset($fieldValueData['field_value']),
                'hasFilePath' => isset($fieldValueData['file_path']),
                'fieldValue' => $fieldValueData['field_value'] ?? null,
                'filePath' => $fieldValueData['file_path'] ?? null
            ]);
            
            try {
                $result = GuestCustomFieldValue::updateOrCreate(
                    [
                        'id_guest' => $guestId,
                        'id_field' => $fieldId,
                    ],
                    $fieldValueData
                );
                
                \Log::info("GuestController@saveCustomFields - Field saved successfully", [
                    'fieldId' => $fieldId,
                    'fieldName' => $field->field_name,
                    'saved' => $result->wasRecentlyCreated ? 'created' : 'updated',
                    'id_value' => $result->id_value,
                    'id_guest' => $result->id_guest,
                    'id_field' => $result->id_field,
                    'field_value' => $result->field_value,
                    'file_path' => $result->file_path
                ]);
            } catch (\Exception $e) {
                \Log::error("GuestController@saveCustomFields - Failed to save field", [
                    'fieldId' => $fieldId,
                    'fieldName' => $field->field_name,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        }
        
        \Log::info('GuestController@saveCustomFields - Completed');
    }
}
