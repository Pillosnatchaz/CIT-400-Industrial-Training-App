<?php

namespace App\Http\Traits;

use App\Models\ActivityLog; // Assuming your ActivityLog model is in App\Models
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

trait LogsActivity
{
    /**
     * Define sensitive fields that should be redacted from log notes.
     * Add any fields you don't want fully dumped in the notes.
     * @var array
     */
    protected $sensitiveLogFields = ['password', 'api_token']; // Example: Add other sensitive fields if applicable

    /**
     * Log an activity with detailed notes.
     *
     * @param string $entityType The type of entity (e.g., 'Project', 'User').
     * @param int $entityId The ID of the entity.
     * @param string $action The action performed (e.g., 'created', 'updated', 'deleted').
     * @param array $payload An array containing data relevant to the action (e.g., validated data, old/new attributes).
     * @return void
     */
    protected function logActivity(string $entityType, int $entityId, string $action, array $payload = [])
    {
        $notes = '';

        switch ($action) {
            case 'created':
                $createdData = $payload['data'] ?? [];
                // Redact sensitive fields
                $filteredData = array_diff_key($createdData, array_flip($this->sensitiveLogFields));
                $notes = 'Created ' . $entityType . '. Data: ' . json_encode($filteredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                break;

            case 'updated':
                $oldAttributes = $payload['old_attributes'] ?? [];
                $newAttributes = $payload['new_attributes'] ?? []; // These are actually the "changes" from getChanges()

                $changes = [];
                foreach ($newAttributes as $key => $newValue) {
                    // Exclude 'updated_at' if it's not relevant for tracking
                    if ($key === 'updated_at') {
                        continue;
                    }

                    $oldValue = $oldAttributes[$key] ?? null; // Get old value for this key

                    // Redact sensitive fields
                    if (in_array($key, $this->sensitiveLogFields)) {
                        $changes[] = $key . ': [REDACTED]';
                    } else {
                        // Convert arrays (like 'start_range') or objects to JSON strings for display
                        $oldDisplay = is_array($oldValue) ? json_encode($oldValue) : (string)$oldValue;
                        $newDisplay = is_array($newValue) ? json_encode($newValue) : (string)$newValue;

                        // Truncate long strings for readability in logs
                        if (mb_strlen($oldDisplay) > 100 || mb_strlen($newDisplay) > 100) {
                            $changes[] = $key . ': (value changed)';
                        } else {
                            $changes[] = $key . ': "' . $oldDisplay . '" -> "' . $newDisplay . '"';
                        }
                    }
                }

                if (empty($changes)) {
                    $notes = 'Updated ' . $entityType . ' (no significant attribute changes).';
                } else {
                    $notes = 'Updated ' . $entityType . '. Changes: ' . implode('; ', $changes) . '.';
                }
                break;

            case 'deleted':
                $deletedData = $payload['data'] ?? [];
                $entityName = $deletedData['name'] ?? 'ID: ' . $entityId; // Fallback if 'name' not present
                $notes = 'Deleted ' . $entityType . ' "' . $entityName . '".';
                break;

            default:
                $notes = 'Action: ' . $action . ' on ' . $entityType . ' ID: ' . $entityId;
                if (!empty($payload)) {
                    $notes .= ' Payload: ' . json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                }
                break;
        }

        ActivityLog::create([
            'admin_id' => Auth::id(), // Will be null if no user is authenticated
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'notes' => $notes,
            'performed_at' => Carbon::now(),
        ]);
    }
}