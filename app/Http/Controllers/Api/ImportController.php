<?php

namespace App\Http\Controllers\Api;

use App\Repositories\EventRepository;
use App\Repositories\GuestRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ImportController
{
    public function __construct(
        private GuestRepository $guestRepository,
        private EventRepository $eventRepository,
        private UserRepository $userRepository
    ) {}

    public function importExcel(Request $request): JsonResponse
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

        // Check if event date has passed
        if ($event->date_event && Carbon::parse($event->date_event)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat import Excel karena tanggal event sudah lewat',
            ], 403);
        }

        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            'default_label' => ['nullable', 'integer', 'in:0,1'],
            'default_session' => ['nullable', 'integer', 'exists:event_sessions,id_session'],
        ]);

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();

            $defaultLabel = $request->input('default_label', 0);
            $defaultSession = $request->input('default_session');
            $defaultPic = $event->event_default_guest_pic;

            // Detect header row
            $headerRow = null;
            $startRow = null;

            for ($checkRow = 1; $checkRow <= 3; $checkRow++) {
                $cellEValue = $worksheet->getCell('E' . $checkRow)->getValue();
                $cellFValue = $worksheet->getCell('F' . $checkRow)->getValue();
                $cellE = is_null($cellEValue) ? '' : trim($cellEValue);
                $cellF = is_null($cellFValue) ? '' : trim($cellFValue);

                if ((stripos($cellE, 'koordinator') !== false || stripos($cellE, 'coordinator') !== false) ||
                    (stripos($cellF, 'peserta') !== false || stripos($cellF, 'participant') !== false)) {
                    $headerRow = $checkRow;
                    $startRow = $checkRow + 1;
                    break;
                }
            }

            if ($headerRow === null) {
                $headerRow = 2;
                $startRow = 3;
            }

            // Calculate how many guests will be imported (before actual import)
            $guestsToImport = 0;
            
            // Count koordinator (Column E)
            for ($row = $startRow; $row <= $highestRow; $row++) {
                $cellValue = $worksheet->getCell('E' . $row)->getValue();
                $namaKoordinator = is_null($cellValue) ? '' : trim($cellValue);
                
                if (!empty($namaKoordinator) && !$this->guestRepository->isGuestExistsByName($namaKoordinator, $event->id_event)) {
                    $guestsToImport++;
                }
            }
            
            // Count peserta (Column F)
            for ($row = $startRow; $row <= $highestRow; $row++) {
                $cellValue = $worksheet->getCell('E' . $row)->getValue();
                $namaKoordinator = is_null($cellValue) ? '' : trim($cellValue);
                
                $cellValue = $worksheet->getCell('F' . $row)->getValue();
                $namaPeserta = is_null($cellValue) ? '' : trim($cellValue);
                
                if (!empty($namaPeserta)) {
                    $guestTitle = trim($worksheet->getCell('A' . $row)->getValue() ?? '');
                    $namaArray = array_map('trim', explode(',', $namaPeserta));
                    
                    foreach ($namaArray as $nama) {
                        $nama = trim($nama);
                        if (empty($nama)) {
                            continue;
                        }
                        
                        // Skip if same as koordinator
                        $namaNormalized = trim(strtolower($nama));
                        $koordinatorNormalized = !empty($namaKoordinator) ? trim(strtolower($namaKoordinator)) : '';
                        
                        if (!empty($koordinatorNormalized) && $namaNormalized === $koordinatorNormalized) {
                            continue;
                        }
                        
                        // Count only if guest doesn't exist
                        if (!$this->guestRepository->isGuestExistsByNameAndTitle($nama, $guestTitle, $event->id_event)) {
                            $guestsToImport++;
                        }
                    }
                }
            }
            
            // Check if guest total limit will be exceeded
            if ($event->guest_total) {
                $currentGuestCount = $this->guestRepository->findByEvent($event->id_event)->count();
                if ($currentGuestCount + $guestsToImport > $event->guest_total) {
                    $availableSlots = $event->guest_total - $currentGuestCount;
                    return response()->json([
                        'success' => false,
                        'message' => "Tidak dapat import Excel karena jumlah tamu yang akan diimport ({$guestsToImport} tamu) melebihi batas yang tersedia. Jumlah tamu saat ini: {$currentGuestCount}, batas maksimal: {$event->guest_total}, slot tersedia: {$availableSlots}",
                    ], 403);
                }
            }

            $successCount = 0;
            $skipCount = 0;
            $errorCount = 0;
            $coordinatorCount = 0;
            $errors = [];

            // STEP 1: Import Koordinator (Column E)
            for ($row = $startRow; $row <= $highestRow; $row++) {
                $cellValue = $worksheet->getCell('E' . $row)->getValue();
                $namaKoordinator = is_null($cellValue) ? '' : trim($cellValue);

                if (empty($namaKoordinator)) {
                    continue;
                }

                // Check if guest already exists
                if ($this->guestRepository->isGuestExistsByName($namaKoordinator, $event->id_event)) {
                    $skipCount++;
                    continue;
                }

                $guestTitle = trim($worksheet->getCell('A' . $row)->getValue() ?? '');
                $guestAddress = trim($worksheet->getCell('B' . $row)->getValue() ?? '');
                $guestEmail = trim($worksheet->getCell('C' . $row)->getValue() ?? '');
                $guestPhone = trim($worksheet->getCell('D' . $row)->getValue() ?? '');
                
                // Use default_session (no longer reading from Excel column E)
                $finalSessionId = $defaultSession;

                // Check guest total limit before creating
                if ($event->guest_total) {
                    $currentGuestCount = $this->guestRepository->findByEvent($event->id_event)->count();
                    if ($currentGuestCount >= $event->guest_total) {
                        $skipCount++;
                        $errors[] = "Tidak dapat menambah koordinator: {$namaKoordinator} - Batas maksimal tamu sudah tercapai";
                        continue;
                    }
                }

                try {
                    $this->guestRepository->create([
                        'id_event' => $event->id_event,
                        'id_session' => $finalSessionId,
                        'guest_title' => $guestTitle,
                        'guest_name' => $namaKoordinator,
                        'guest_address' => $guestAddress,
                        'guest_phone' => $guestPhone,
                        'guest_email' => $guestEmail,
                        'guest_label' => $defaultLabel,
                        'guest_pic' => $defaultPic,
                    ]);
                    $coordinatorCount++;
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Failed to insert coordinator: {$namaKoordinator} - " . $e->getMessage();
                    Log::error("Import coordinator error: " . $e->getMessage());
                }
            }

            // STEP 2: Import Nama Peserta (Column F)
            for ($row = $startRow; $row <= $highestRow; $row++) {
                $cellValue = $worksheet->getCell('E' . $row)->getValue();
                $namaKoordinator = is_null($cellValue) ? '' : trim($cellValue);

                $cellValue = $worksheet->getCell('F' . $row)->getValue();
                $namaPeserta = is_null($cellValue) ? '' : trim($cellValue);

                if (empty($namaPeserta)) {
                    continue;
                }

                $guestTitle = trim($worksheet->getCell('A' . $row)->getValue() ?? '');
                $guestAddress = trim($worksheet->getCell('B' . $row)->getValue() ?? '');
                
                // Use default_session (no longer reading from Excel column E)
                $finalSessionId = $defaultSession;

                // Split nama peserta by comma
                $namaArray = array_map('trim', explode(',', $namaPeserta));

                foreach ($namaArray as $nama) {
                    $nama = trim($nama);
                    if (empty($nama)) {
                        continue;
                    }

                    // Skip if same as koordinator
                    $namaNormalized = trim(strtolower($nama));
                    $koordinatorNormalized = !empty($namaKoordinator) ? trim(strtolower($namaKoordinator)) : '';

                    if (!empty($koordinatorNormalized) && $namaNormalized === $koordinatorNormalized) {
                        $skipCount++;
                        continue;
                    }

                    // Check if guest already exists with same title
                    if ($this->guestRepository->isGuestExistsByNameAndTitle($nama, $guestTitle, $event->id_event)) {
                        $skipCount++;
                        continue;
                    }

                    // Check guest total limit before creating
                    if ($event->guest_total) {
                        $currentGuestCount = $this->guestRepository->findByEvent($event->id_event)->count();
                        if ($currentGuestCount >= $event->guest_total) {
                            $skipCount++;
                            $errors[] = "Tidak dapat menambah peserta: {$nama} - Batas maksimal tamu sudah tercapai";
                            continue;
                        }
                    }

                    try {
                        $this->guestRepository->create([
                            'id_event' => $event->id_event,
                            'id_session' => $finalSessionId,
                            'guest_title' => $guestTitle,
                            'guest_name' => $nama,
                            'guest_address' => $guestAddress,
                            'guest_label' => $defaultLabel,
                            'guest_pic' => $defaultPic,
                        ]);
                        $successCount++;
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errors[] = "Failed to insert: {$nama} (Row {$row}) - " . $e->getMessage();
                        Log::error("Import guest error: " . $e->getMessage());
                    }
                }
            }

            $pesertaCount = $successCount - $coordinatorCount;

            return response()->json([
                'success' => true,
                'message' => 'Import completed!',
                'data' => [
                    'koordinator_imported' => $coordinatorCount,
                    'peserta_imported' => $pesertaCount,
                    'total_imported' => $successCount,
                    'skipped' => $skipCount,
                    'errors' => $errorCount,
                    'error_messages' => array_slice($errors, 0, 10), // First 10 errors
                ],
            ]);
        } catch (\Exception $e) {
            Log::error("Import Excel error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing Excel file: ' . $e->getMessage(),
            ], 500);
        }
    }
}
