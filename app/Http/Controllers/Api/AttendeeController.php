<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendee;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendeeController extends Controller
{
    /**
     * Display a listing of attendees
     */
    public function index()
    {
        $attendees = Attendee::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $attendees,
        ]);
    }

    /**
     * Store a newly created attendee
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:attendees,email',
            'phone' => 'nullable|string',
            'ticket_number' => 'required|string|unique:attendees,ticket_number',
            'event_name' => 'required|string|max:255',
        ]);

        $attendee = new Attendee($validated);
        $attendee->qr_code = $attendee->generateQrCode();
        $attendee->save();

        return response()->json([
            'success' => true,
            'message' => 'Attendee created successfully',
            'data' => $attendee,
        ], 201);
    }

    /**
     * Generate QR code for an attendee
     */
    public function generateQrCode($id)
    {
        $attendee = Attendee::findOrFail($id);

        // Generate QR code with attendee data
        $qrData = json_encode([
            'id' => $attendee->id,
            'qr_code' => $attendee->qr_code,
            'ticket_number' => $attendee->ticket_number,
            'name' => $attendee->name,
        ]);

        $qrCode = QrCode::size(300)
            ->format('png')
            ->generate($qrData);

        return response($qrCode)
            ->header('Content-Type', 'image/png');
    }

    /**
     * Validate attendee by QR code
     */
    public function validateQrCode(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $attendee = Attendee::where('qr_code', $request->qr_code)->first();

        if (!$attendee) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau peserta tidak ditemukan',
            ], 404);
        }

        if ($attendee->isValidated()) {
            return response()->json([
                'success' => true,
                'message' => 'Peserta sudah pernah divalidasi',
                'data' => [
                    'attendee' => $attendee,
                    'is_duplicate' => true,
                    'validated_at' => $attendee->validated_at,
                ],
            ]);
        }

        // Mark as validated
        $attendee->markAsValidated();

        return response()->json([
            'success' => true,
            'message' => 'Data peserta valid dan berhasil divalidasi',
            'data' => [
                'attendee' => $attendee,
                'is_duplicate' => false,
                'validated_at' => $attendee->validated_at,
            ],
        ]);
    }

    /**
     * Get attendee by QR code
     */
    public function getByQrCode($qrCode)
    {
        $attendee = Attendee::where('qr_code', $qrCode)->first();

        if (!$attendee) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau peserta tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $attendee,
        ]);
    }

    /**
     * Show single attendee
     */
    public function show($id)
    {
        $attendee = Attendee::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $attendee,
        ]);
    }

    /**
     * Update attendee
     */
    public function update(Request $request, $id)
    {
        $attendee = Attendee::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:attendees,email,' . $id,
            'phone' => 'nullable|string',
            'ticket_number' => 'sometimes|required|string|unique:attendees,ticket_number,' . $id,
            'event_name' => 'sometimes|required|string|max:255',
        ]);

        $attendee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Attendee updated successfully',
            'data' => $attendee,
        ]);
    }

    /**
     * Delete attendee
     */
    public function destroy($id)
    {
        $attendee = Attendee::findOrFail($id);
        $attendee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attendee deleted successfully',
        ]);
    }
}

