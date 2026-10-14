<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('sort_order', 'asc')->latest()->paginate(15);

        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:150',
            'status' => 'required|string|max:50',
            'client' => 'nullable|string|max:150',
            'summary' => 'nullable|string',
            'description' => 'nullable|string',
            'featured_image' => 'nullable|image|max:4096',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['title']).'-'.Str::random(5);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active') ? true : true;
        $validated['sort_order'] = $request->input('sort_order', 0);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('projects', 'public');
        }

        Project::create($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Proje başarıyla portföye eklendi.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:150',
            'status' => 'required|string|max:50',
            'client' => 'nullable|string|max:150',
            'summary' => 'nullable|string',
            'description' => 'nullable|string',
            'featured_image' => 'nullable|image|max:4096',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $request->input('sort_order', 0);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('projects', 'public');
        }

        $project->update($validated);

        return redirect()->route('admin.projects.index')->with('success', 'Proje başarıyla güncellendi.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Proje portföyden silindi.');
    }
}
