<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminCandidateController extends Controller
{
    public function index(Event $event, Election $election)
    {
        $candidateFields = $event->candidateFieldDefinitions()->get()->map->toFormField();
        $candidates      = $election->candidates()
            ->orderBy('order_number')
            ->get()
            ->map(fn ($c) => [
                'id'           => $c->id,
                'order_number' => $c->order_number,
                'fields'       => $c->fields,
                'photo_url'    => $c->photo_url,
            ]);

        return Inertia::render('Admin/Candidates/Index', compact('event', 'election', 'candidates', 'candidateFields'));
    }

    public function create(Event $event, Election $election)
    {
        $candidateFields = $event->candidateFieldDefinitions()->get()->map->toFormField();

        return Inertia::render('Admin/Candidates/Create', compact('event', 'election', 'candidateFields'));
    }

    public function store(Request $request, Event $event, Election $election)
    {
        if ($election->status === 'active') {
            abort(403, 'Cannot modify candidates during an active election.');
        }

        $request->validate([
            'order_number' => ['nullable', 'integer', 'min:1'],
        ]);

        $fieldDefs    = $event->candidateFieldDefinitions()->get();
        $fieldsData   = [];
        $dynamicRules = [];

        foreach ($fieldDefs as $field) {
            $rules = $field->required ? ['required'] : ['nullable'];
            if ($field->type === 'image') {
                $dynamicRules["fields.{$field->key}"] = [...$rules, 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'];
            } else {
                $dynamicRules["fields.{$field->key}"] = [...$rules, 'string', 'max:2000'];
            }
        }

        $request->validate($dynamicRules);

        foreach ($fieldDefs as $field) {
            if ($field->type === 'image' && $request->hasFile("fields.{$field->key}")) {
                $fieldsData[$field->key] = $request->file("fields.{$field->key}")
                    ->store('candidates', 'public');
            } else {
                $fieldsData[$field->key] = $request->input("fields.{$field->key}");
            }
        }

        $nextOrder = $election->candidates()->max('order_number') + 1;

        $election->candidates()->create([
            'order_number' => $request->input('order_number') ?? $nextOrder,
            'fields'       => $fieldsData,
        ]);

        return redirect()->route('events.admin.candidates.index', [$event, $election])
            ->with('success', 'Candidate added successfully.');
    }

    public function edit(Event $event, Election $election, Candidate $candidate)
    {
        $candidateFields = $event->candidateFieldDefinitions()->get()->map->toFormField();

        return Inertia::render('Admin/Candidates/Edit', compact('event', 'election', 'candidate', 'candidateFields'));
    }

    public function update(Request $request, Event $event, Election $election, Candidate $candidate)
    {
        if ($election->status === 'active') {
            abort(403, 'Cannot modify candidates during an active election.');
        }

        $request->validate([
            'order_number' => ['nullable', 'integer', 'min:1'],
        ]);

        $fieldDefs    = $event->candidateFieldDefinitions()->get();
        $fieldsData   = $candidate->fields ?? [];
        $dynamicRules = [];

        foreach ($fieldDefs as $field) {
            $rules = $field->required ? ['required'] : ['nullable'];
            if ($field->type === 'image') {
                // On edit, image is optional if one already exists; only required if none stored yet
                $hasExistingImage = isset($fieldsData[$field->key]) && $fieldsData[$field->key];
                $imageRules = $hasExistingImage ? ['nullable'] : $rules;
                $dynamicRules["fields.{$field->key}"] = [...$imageRules, 'sometimes', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'];
            } else {
                $dynamicRules["fields.{$field->key}"] = [...$rules, 'string', 'max:2000'];
            }
        }

        $request->validate($dynamicRules);

        foreach ($fieldDefs as $field) {
            if ($field->type === 'image' && $request->hasFile("fields.{$field->key}")) {
                // Delete old image
                if (isset($fieldsData[$field->key])) {
                    Storage::disk('public')->delete($fieldsData[$field->key]);
                }
                $fieldsData[$field->key] = $request->file("fields.{$field->key}")
                    ->store('candidates', 'public');
            } elseif ($request->has("fields.{$field->key}")) {
                $fieldsData[$field->key] = $request->input("fields.{$field->key}");
            }
        }

        $candidate->update([
            'order_number' => $request->input('order_number') ?: $candidate->order_number,
            'fields'       => $fieldsData,
        ]);

        return redirect()->route('events.admin.candidates.index', [$event, $election])
            ->with('success', 'Candidate updated successfully.');
    }

    public function destroy(Event $event, Election $election, Candidate $candidate)
    {
        if ($election->status === 'active') {
            abort(403, 'Cannot delete candidates during an active election.');
        }

        // Delete any stored image files
        $fields = $candidate->fields ?? [];
        foreach ($fields as $value) {
            if (is_string($value) && Storage::disk('public')->exists($value)) {
                Storage::disk('public')->delete($value);
            }
        }

        $candidate->delete();

        return redirect()->route('events.admin.candidates.index', [$event, $election])
            ->with('success', 'Candidate removed.');
    }
}
