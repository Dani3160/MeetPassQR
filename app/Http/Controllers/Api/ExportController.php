<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\EventCustomField;
use App\Repositories\EventRepository;
use App\Repositories\GuestRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExportController
{
    public function __construct(
        private GuestRepository $guestRepository,
        private EventRepository $eventRepository,
        private UserRepository $userRepository
    ) {}

    /**
     * Download Excel template with dynamic custom fields
     */
    public function downloadTemplate(Request $request)
    {
        $token = $request->query('token');
        $eventIdentifier = $request->query('eventId');

        if (empty($token) || empty($eventIdentifier)) {
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

        // Get custom fields for this event
        $customFields = EventCustomField::where('id_event', $event->id_event)
            ->orderBy('field_order')
            ->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        // Header row
        $headers = [
            'A' => 'Jabatan',
            'B' => 'Alamat',
            'C' => 'Email',
            'D' => 'Telepon',
            'E' => 'Nama Koordinator',
            'F' => 'Nama Peserta',
        ];

        $col = 'G';
        foreach ($customFields as $field) {
            $headers[$col] = $field->field_label;
            $col++;
        }

        // Set headers
        $row = 1;
        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . $row, $header);
        }

        // Style header row
        $headerRange = 'A1:' . $col . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FF8C00'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        foreach ($customFields as $index => $field) {
            $colLetter = chr(ord('G') + $index);
            $sheet->getColumnDimension($colLetter)->setWidth(20);
        }

        // Add example row
        $row = 2;
        $sheet->setCellValue('A' . $row, 'Contoh: Dr.');
        $sheet->setCellValue('B' . $row, 'Contoh: Jl. Contoh No. 123');
        $sheet->setCellValue('C' . $row, 'contoh@email.com');
        $sheet->setCellValue('D' . $row, '081234567890');
        $sheet->setCellValue('E' . $row, 'Nama Koordinator');
        $sheet->setCellValue('F' . $row, 'Nama Peserta 1, Nama Peserta 2');

        $col = 'G';
        foreach ($customFields as $field) {
            if ($field->field_type === 'select' || $field->field_type === 'radio') {
                $options = $field->getOptionsArray();
                $exampleValue = !empty($options) ? $options[0]['value'] ?? '' : '';
                $sheet->setCellValue($col . $row, $exampleValue);
            } else {
                $sheet->setCellValue($col . $row, 'Contoh: ' . $field->field_label);
            }
            $col++;
        }

        // Style example row
        $exampleRange = 'A2:' . chr(ord('G') + count($customFields) - 1) . '2';
        $sheet->getStyle($exampleRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF8DC'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Freeze header row
        $sheet->freezePane('A2');

        // Generate filename
        $filename = 'Template_Import_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $event->name_event) . '.xlsx';

        // Output
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_template_');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Download all QR codes as ZIP
     */
    public function downloadAllQR(Request $request)
    {
        $token = $request->query('token');
        $eventIdentifier = $request->query('eventId');

        if (empty($token) || empty($eventIdentifier)) {
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

        // Get all guests with session relationship
        $guests = $this->guestRepository->findByEvent($event->id_event);
        $guests->load('session');

        if ($guests->isEmpty()) {
            abort(404, 'No guests found');
        }

        // Create ZIP file
        $zip = new ZipArchive();
        $zipFileName = storage_path('app/temp/qr_codes_' . $event->id_event . '_' . time() . '.zip');
        Storage::makeDirectory('temp');

        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            abort(500, 'Cannot create ZIP file');
        }

        // Generate QR codes
        foreach ($guests as $guest) {
            $qrUrl = $this->generateQRUrl($guest->id_guest, $event->id_event, $token);
            $qrCode = QrCode::format('png')
                ->size(600)
                ->errorCorrection('H')
                ->generate($qrUrl);

            $filename = $this->generateQRFilename($guest);
            $zip->addFromString($filename, $qrCode);
        }

        $zip->close();

        $downloadFilename = 'QR_Codes_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $event->name_event) . '.zip';

        return response()->download($zipFileName, $downloadFilename)->deleteFileAfterSend(true);
    }

    /**
     * Download selected QR codes as ZIP
     */
    public function downloadSelectedQR(Request $request)
    {
        $token = $request->query('token');
        $eventIdentifier = $request->query('eventId');
        $guestIds = $request->input('guestIds', []);

        if (empty($token) || empty($eventIdentifier)) {
            abort(400, 'Missing parameters');
        }

        if (empty($guestIds) || !is_array($guestIds)) {
            abort(400, 'No guests selected');
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

        // Get selected guests with session relationship
        $guests = $this->guestRepository->findByIdsAndEvent($guestIds, $event->id_event);
        $guests->load('session');

        if ($guests->isEmpty()) {
            abort(404, 'No guests found');
        }

        // Create ZIP file
        $zip = new ZipArchive();
        $zipFileName = storage_path('app/temp/qr_codes_selected_' . $event->id_event . '_' . time() . '.zip');
        Storage::makeDirectory('temp');

        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            abort(500, 'Cannot create ZIP file');
        }

        // Generate QR codes
        foreach ($guests as $guest) {
            $qrUrl = $this->generateQRUrl($guest->id_guest, $event->id_event, $token);
            $qrCode = QrCode::format('png')
                ->size(600)
                ->errorCorrection('H')
                ->generate($qrUrl);

            $filename = $this->generateQRFilename($guest);
            $zip->addFromString($filename, $qrCode);
        }

        $zip->close();

        $downloadFilename = 'QR_Codes_Selected_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $event->name_event) . '.zip';

        return response()->download($zipFileName, $downloadFilename)->deleteFileAfterSend(true);
    }

    private function generateQRUrl(int $guestId, int $eventId, string $token): string
    {
        $baseUrl = config('app.url');
        return "{$baseUrl}/guest-checkin?guestId={$guestId}&eventId={$eventId}&token={$token}";
    }

    private function generateQRFilename($guest): string
    {
        $title = $guest->guest_title ? preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $guest->guest_title) : '';
        $name = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $guest->guest_name);
        
        // Add session info if exists - format: "nama sesi, waktu (jam mulai - jam selesai)"
        $sessionPart = '';
        if ($guest->session) {
            $sessionName = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $guest->session->name_session);
            
            // Format waktu menjadi H:i (jam:menit) tanpa detik
            if (is_string($guest->session->time_started_session)) {
                $startTime = preg_match('/^\d{2}:\d{2}:\d{2}/', $guest->session->time_started_session) 
                    ? substr($guest->session->time_started_session, 0, 5)
                    : (preg_match('/^\d{2}:\d{2}$/', $guest->session->time_started_session) 
                        ? $guest->session->time_started_session 
                        : Carbon::parse($guest->session->time_started_session)->format('H:i'));
            } else {
                $startTime = Carbon::parse($guest->session->time_started_session)->format('H:i');
            }
            
            if (is_string($guest->session->time_ended_session)) {
                $endTime = preg_match('/^\d{2}:\d{2}:\d{2}/', $guest->session->time_ended_session) 
                    ? substr($guest->session->time_ended_session, 0, 5)
                    : (preg_match('/^\d{2}:\d{2}$/', $guest->session->time_ended_session) 
                        ? $guest->session->time_ended_session 
                        : Carbon::parse($guest->session->time_ended_session)->format('H:i'));
            } else {
                $endTime = Carbon::parse($guest->session->time_ended_session)->format('H:i');
            }
            
            $sessionPart = " - {$sessionName}, {$startTime}-{$endTime}";
        }
        
        if (!empty($title)) {
            return "{$title} - {$name}{$sessionPart}.png";
        }
        
        return "{$name}{$sessionPart}.png";
    }
}
