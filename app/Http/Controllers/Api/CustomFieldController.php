<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCustomField;
use App\Repositories\EventRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomFieldController extends Controller
{
    public function __construct(
        private EventRepository $eventRepository
    ) {}

    /**
     * Get all custom fields for an event
     */
    public function index(string $eventIdentifier)
    {
        try {
            // Support both slug and ID for backward compatibility
            $event = $this->eventRepository->findBySlugOrId($eventIdentifier);
            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found'
                ], 404);
            }
            $fields = $event->customFields;
            
            return response()->json([
                'success' => true,
                'data' => $fields
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch custom fields',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new custom field
     */
    public function store(Request $request, string $eventIdentifier)
    {
        try {
            // Support both slug and ID for backward compatibility
            $event = $this->eventRepository->findBySlugOrId($eventIdentifier);
            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found'
                ], 404);
            }
            
            $validator = Validator::make($request->all(), [
                'field_name' => 'required|string|max:100|regex:/^[a-z0-9_]+$/',
                'field_label' => 'required|string|max:255',
                'field_type' => 'required|in:input,textarea,file,select,radio,checkbox',
                'field_options' => 'nullable|array',
                'is_required' => 'boolean',
                'field_order' => 'integer',
                'field_placeholder' => 'nullable|string|max:255',
                'field_validation' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if field_name already exists for this event
            $existingField = EventCustomField::where('id_event', $event->id_event)
                ->where('field_name', $request->field_name)
                ->first();

            if ($existingField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Field name already exists for this event'
                ], 422);
            }

            // Validate options for select/radio/checkbox
            if (in_array($request->field_type, ['select', 'radio', 'checkbox'])) {
                if (empty($request->field_options['options']) || !is_array($request->field_options['options'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Options required for select/radio/checkbox type'
                    ], 422);
                }

                // Validate each option has value and label
                foreach ($request->field_options['options'] as $option) {
                    if (empty($option['value']) || empty($option['label'])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Each option must have value and label'
                        ], 422);
                    }
                }
            }

            // Get max order for this event
            $maxOrder = EventCustomField::where('id_event', $event->id_event)->max('field_order') ?? 0;

            $field = EventCustomField::create([
                'id_event' => $event->id_event,
                'field_name' => $request->field_name,
                'field_label' => $request->field_label,
                'field_type' => $request->field_type,
                'field_options' => $request->field_options ?? null,
                'is_required' => $request->is_required ?? false,
                'field_order' => $request->field_order ?? ($maxOrder + 1),
                'field_placeholder' => $request->field_placeholder,
                'field_validation' => $request->field_validation ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Custom field created successfully',
                'data' => $field
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create custom field',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a custom field
     */
    public function update(Request $request, string $eventIdentifier, $fieldId)
    {
        try {
            // Support both slug and ID for backward compatibility
            $event = $this->eventRepository->findBySlugOrId($eventIdentifier);
            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found'
                ], 404);
            }

            $field = EventCustomField::where('id_event', $event->id_event)
                ->findOrFail($fieldId);

            $validator = Validator::make($request->all(), [
                'field_label' => 'sometimes|string|max:255',
                'field_type' => 'sometimes|in:input,textarea,file,select,radio,checkbox',
                'field_options' => 'nullable|array',
                'is_required' => 'boolean',
                'field_order' => 'integer',
                'field_placeholder' => 'nullable|string|max:255',
                'field_validation' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validate options if type changed to select/radio/checkbox
            if ($request->has('field_type') && in_array($request->field_type, ['select', 'radio', 'checkbox'])) {
                if (empty($request->field_options['options']) || !is_array($request->field_options['options'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Options required for select/radio/checkbox type'
                    ], 422);
                }
            }

            $field->update($request->only([
                'field_label',
                'field_type',
                'field_options',
                'is_required',
                'field_order',
                'field_placeholder',
                'field_validation',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Custom field updated successfully',
                'data' => $field->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update custom field',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a custom field
     */
    public function destroy(string $eventIdentifier, $fieldId)
    {
        try {
            // Support both slug and ID for backward compatibility
            $event = $this->eventRepository->findBySlugOrId($eventIdentifier);
            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found'
                ], 404);
            }

            $field = EventCustomField::where('id_event', $event->id_event)
                ->findOrFail($fieldId);

            $field->delete();

            return response()->json([
                'success' => true,
                'message' => 'Custom field deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete custom field',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder custom fields
     */
    public function reorder(Request $request, string $eventIdentifier)
    {
        try {
            // Support both slug and ID for backward compatibility
            $event = $this->eventRepository->findBySlugOrId($eventIdentifier);
            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'fields' => 'required|array',
                'fields.*.id_field' => 'required|exists:event_custom_fields,id_field',
                'fields.*.field_order' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            foreach ($request->fields as $fieldData) {
                EventCustomField::where('id_field', $fieldData['id_field'])
                    ->where('id_event', $event->id_event)
                    ->update(['field_order' => $fieldData['field_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Fields reordered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder fields',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
