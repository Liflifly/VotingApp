<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventFieldDefinition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EventSettingsController extends Controller
{
    public function edit(Request $request, Event $event)
    {
        $voterFields     = $event->voterFieldDefinitions()->get()->map->toFormField();
        $candidateFields = $event->candidateFieldDefinitions()->get()->map->toFormField();

        // If no voter fields are defined yet, provide defaults for the builder
        if ($voterFields->isEmpty()) {
            $voterFields = [
                [
                    'key'        => 'name',
                    'label'      => 'VOTER NAME',
                    'type'       => 'text',
                    'required'   => true,
                    'is_primary' => false,
                    'options'    => null,
                ],
                [
                    'key'        => 'voter_id',
                    'label'      => 'ID NUMBER (NIS/NIK)',
                    'type'       => 'text',
                    'required'   => true,
                    'is_primary' => true,
                    'options'    => null,
                ]
            ];
        }

        // If no candidate fields are defined yet, provide defaults for the builder
        if ($candidateFields->isEmpty()) {
            $candidateFields = [
                [
                    'key'      => 'order_number',
                    'label'    => 'CANDIDATE NUMBER',
                    'type'     => 'number',
                    'required' => true,
                    'options'  => null,
                ],
                [
                    'key'      => 'name',
                    'label'    => 'FULL NAME',
                    'type'     => 'text',
                    'required' => true,
                    'options'  => null,
                ]
            ];
        }

        // Build full shareable URLs
        $voterLink = url("/join/v/{$event->voter_access_token}");
        $adminLink = url("/join/a/{$event->admin_access_token}");

        return Inertia::render('Admin/EventSettings', [
            'event'           => $event,
            'voterFields'     => $voterFields,
            'candidateFields' => $candidateFields,
            'voterLink'       => $voterLink,
            'adminLink'       => $adminLink,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string', 'max:1000'],
            'theme'              => ['required', 'in:neo-brutalism,semi-formal,formal'],
            'results_visibility' => ['required', 'in:public,private'],
        ]);

        $event->update($validated);

        return back()->with('success', 'Event settings updated.');
    }

    /**
     * Regenerate voter and admin access tokens (invalidates old links/QR codes).
     */
    public function regenerateLinks(Request $request, Event $event)
    {
        $event->regenerateTokens();

        return back()->with('success', 'Share links regenerated. Old links and QR codes are now invalid.');
    }

    /**
     * Generate a new one-time admin invite token.
     */
    public function generateToken(Request $request, Event $event)
    {
        $validated = $request->validate([
            'role'       => ['required', 'in:admin,super_admin'],
            'expires_in' => ['nullable', 'integer', 'min:1', 'max:720'], // Hours
        ]);

        $token = EventInviteToken::create([
            'event_id'   => $event->id,
            'token'      => EventInviteToken::generateToken(),
            'role'       => $validated['role'],
            'expires_at' => isset($validated['expires_in'])
                ? now()->addHours($validated['expires_in'])
                : null,
        ]);

        return back()->with('success', "Token generated: {$token->token}");
    }

    /**
     * Revoke (delete) a specific invite token.
     */
    public function revokeToken(Request $request, Event $event, EventInviteToken $token)
    {
        abort_if($token->event_id !== $event->id, 403);
        $token->delete();

        return back()->with('success', 'Token revoked.');
    }

    /**
     * Save field definitions (voter or candidate) from the DynamicFieldBuilder.
     */
    public function updateFields(Request $request, Event $event)
    {
        $validated = $request->validate([
            'target'            => ['required', 'in:voter,candidate'],
            'fields'            => ['present', 'array'],
            'fields.*.key'      => ['required', 'string', 'alpha_dash', 'max:64'],
            'fields.*.label'    => ['required', 'string', 'max:255'],
            'fields.*.type'     => ['required', 'in:text,textarea,number,email,select,image'],
            'fields.*.options'  => ['nullable', 'array'],
            'fields.*.required'   => ['required', 'boolean'],
            'fields.*.is_primary' => ['nullable', 'boolean'],
        ]);

        $target = $validated['target'];

        DB::transaction(function () use ($event, $target, $validated) {
            // Delete existing definitions for this target
            $event->fieldDefinitions()->where('target', $target)->delete();

            // Insert new definitions
            foreach ($validated['fields'] as $index => $field) {
                EventFieldDefinition::create([
                    'event_id' => $event->id,
                    'target'   => $target,
                    'key'      => $field['key'],
                    'label'    => $field['label'],
                    'type'       => $field['type'],
                    'options'    => $field['options'] ?? null,
                    'required'   => $field['required'],
                    'is_primary' => $field['is_primary'] ?? false,
                    'order'      => $index,
                ]);
            }
        });

        return redirect()->route('events.admin.settings', $event)
            ->with('success', ucfirst($target) . ' fields updated.');
    }

    /**
     * Permanently delete the event and all associated data.
     */
    public function destroy(Request $request, Event $event)
    {
        // Only the creator (super_admin) can delete the event
        // This is already enforced by the 'super_admin' middleware on the route group
        
        $event->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Event deleted successfully.');
    }
}
