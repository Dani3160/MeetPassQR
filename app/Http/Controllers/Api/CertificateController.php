<?php

namespace App\Http\Controllers\Api;

use App\Models\CertificateTemplate;
use App\Models\EventCertificate;
use App\Repositories\UserRepository;
use App\Repositories\EventRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateController
{
    public function __construct(
        private UserRepository $userRepository,
        private EventRepository $eventRepository
    ) {}

    /**
     * Get all certificate templates
     */
    public function getTemplates(Request $request): JsonResponse
    {
        $token = $request->query('token');
        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $templates = CertificateTemplate::where('is_active', true)->get();
        
        return response()->json($templates);
    }

    /**
     * Get all templates including inactive (for editor)
     */
    public function getAllTemplates(Request $request): JsonResponse
    {
        $token = $request->query('token');
        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $templates = CertificateTemplate::all();
        
        return response()->json($templates);
    }

    /**
     * Create a new certificate template
     */
    public function createTemplate(Request $request): JsonResponse
    {
        $token = $request->query('token');
        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'template_name' => 'required|string|max:100',
            'template_description' => 'nullable|string',
            'html_structure' => 'required|string',
            'css_styles' => 'nullable|string',
            'configurable_fields' => 'nullable|array',
            'is_active' => 'nullable|boolean'
        ]);

        $template = CertificateTemplate::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil dibuat',
            'id_template' => $template->id_template,
            'data' => $template
        ]);
    }

    /**
     * Update a certificate template
     */
    public function updateTemplate(Request $request, $id): JsonResponse
    {
        $token = $request->query('token');
        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $template = CertificateTemplate::find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'template_name' => 'required|string|max:100',
            'template_description' => 'nullable|string',
            'html_structure' => 'required|string',
            'css_styles' => 'nullable|string',
            'configurable_fields' => 'nullable|array',
            'is_active' => 'nullable|boolean'
        ]);

        $template->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil diperbarui',
            'data' => $template
        ]);
    }

    /**
     * Delete a certificate template
     */
    public function deleteTemplate(Request $request, $id): JsonResponse
    {
        $token = $request->query('token');
        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $template = CertificateTemplate::find($id);

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak ditemukan'
            ], 404);
        }

        // Check if template is being used
        $usageCount = $template->eventCertificates()->count();
        if ($usageCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Template sedang digunakan oleh {$usageCount} event. Tidak dapat dihapus."
            ], 400);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template berhasil dihapus'
        ]);
    }

    /**
     * Get certificate configuration for an event
     */
    public function getCertificate(Request $request, string $eventIdentifier): JsonResponse
    {
        $token = $request->query('token');
        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Support both slug and ID for backward compatibility
        $event = $this->eventRepository->findBySlugOrId($eventIdentifier);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $certificate = EventCertificate::where('id_event', $event->id_event)
            ->with('template')
            ->first();

        if (!$certificate) {
            return response()->json(['data' => null]);
        }

        return response()->json(['data' => $certificate]);
    }

    /**
     * Create or update certificate configuration for an event
     */
    public function saveCertificate(Request $request, string $eventIdentifier): JsonResponse
    {
        $token = $request->query('token');
        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Support both slug and ID for backward compatibility
        $event = $this->eventRepository->findBySlugOrId($eventIdentifier);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $validated = $request->validate([
            'id_template' => 'required|exists:certificate_templates,id_template',
            'introductory_phrase' => 'nullable|string|max:255',
            'completion_phrase' => 'nullable|string|max:255',
            'organization_name' => 'nullable|string|max:100',
            'signatory_name' => 'nullable|string|max:100',
            'signatory_title' => 'nullable|string|max:100',
            'signature_image' => 'nullable|string', // Bisa URL atau path, tidak perlu max karena bisa panjang
            'verification_url_base' => 'nullable|string|max:255',
            'certificate_id_prefix' => 'nullable|string|max:20',
            'auto_generate_id' => 'nullable|boolean',
            'custom_fields' => 'nullable|array'
        ]);

        $certificate = EventCertificate::updateOrCreate(
            ['id_event' => $event->id_event],
            $validated
        );

        $certificate->load('template');

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi sertifikat berhasil disimpan',
            'data' => $certificate
        ]);
    }

    /**
     * Upload signature image
     */
    public function uploadSignature(Request $request, string $eventIdentifier): JsonResponse
    {
        $token = $request->query('token');
        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Support both slug and ID for backward compatibility
        $event = $this->eventRepository->findBySlugOrId($eventIdentifier);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $request->validate([
            'signature' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'], // max 5MB
        ]);

        try {
            $file = $request->file('signature');
            
            // Generate random string untuk nama file (8 karakter)
            $randomString = Str::random(8);
            $extension = $file->getClientOriginalExtension();
            $fileName = "sig_{$randomString}.{$extension}";
            
            // Store file
            $path = $file->storeAs("events/{$event->id_event}/signatures", $fileName, 'public');
            
            // Delete old signature if exists
            $certificate = EventCertificate::where('id_event', $event->id_event)->first();
            if ($certificate && $certificate->signature_image) {
                $oldPath = str_replace('/storage/', '', $certificate->signature_image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Signature uploaded successfully',
                'data' => [
                    'signature_url' => Storage::disk('public')->url($path),
                    'signature_path' => $path,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload signature: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete certificate configuration
     */
    public function deleteCertificate(Request $request, string $eventIdentifier): JsonResponse
    {
        $token = $request->query('token');
        $user = $this->userRepository->findByToken($token);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Support both slug and ID for backward compatibility
        $event = $this->eventRepository->findBySlugOrId($eventIdentifier);
        if (!$event) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        $certificate = EventCertificate::where('id_event', $event->id_event)->first();

        if ($certificate) {
            // Delete signature file if exists
            if ($certificate->signature_image) {
                $oldPath = str_replace('/storage/', '', $certificate->signature_image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $certificate->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi sertifikat berhasil dihapus'
        ]);
    }
}
