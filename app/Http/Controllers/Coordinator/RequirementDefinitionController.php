<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\RequirementDefinition;
use Illuminate\Http\Request;

class RequirementDefinitionController extends Controller
{
    public function index()
    {
        $definitions = RequirementDefinition::orderBy('stage')
            ->orderBy('semester')
            ->orderByDesc('is_required')
            ->orderBy('name')
            ->get();

        return view('coordinator.requirements.definitions.index', compact('definitions'));
    }

    public function create()
    {
        return view('coordinator.requirements.definitions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'stage'       => ['required', 'in:Field Study,Internship'],
            'semester'    => ['required', 'string', 'in:1st Semester,2nd Semester,Summer'],
            'is_required' => ['required', 'boolean'],
            'is_active'   => ['required', 'boolean'],
        ]);

        RequirementDefinition::create([
            ...$validated,
            'created_by' => optional(auth()->user()->coordinator)->id,
        ]);

        return redirect()
            ->route('coordinator.requirements.definitions.index')
            ->with('success', 'Requirement created successfully.');
    }

    public function edit(RequirementDefinition $definition)
    {
        return view('coordinator.requirements.definitions.edit', compact('definition'));
    }

    public function update(Request $request, RequirementDefinition $definition)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'stage'       => ['required', 'in:Field Study,Internship'],
            'semester'    => ['required', 'string', 'in:1st Semester,2nd Semester,Summer'],
            'is_required' => ['required', 'boolean'],
            'is_active'   => ['required', 'boolean'],
        ]);

        $definition->update($validated);

        return redirect()
            ->route('coordinator.requirements.definitions.index')
            ->with('success', 'Requirement updated successfully.');
    }

    public function toggleActive(RequirementDefinition $definition)
    {
        $definition->update(['is_active' => !$definition->is_active]);

        return redirect()
            ->route('coordinator.requirements.definitions.index')
            ->with('success', 'Requirement status updated.');
    }

    public function destroy(RequirementDefinition $definition)
    {
        $definition->delete();

        return redirect()
            ->route('coordinator.requirements.definitions.index')
            ->with('success', 'Requirement deleted.');
    }
}