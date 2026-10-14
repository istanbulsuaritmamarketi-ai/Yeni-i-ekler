<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::where('is_active', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->orderBy('sort_order')->orderByDesc('id')->paginate(12);
        $categories = Project::where('is_active', true)->whereNotNull('category')->distinct()->pluck('category');

        return view('projects.index', compact('projects', 'categories'));
    }

    public function show($slug)
    {
        $project = Project::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $relatedProjects = Project::where('is_active', true)
            ->where('id', '!=', $project->id)
            ->take(3)
            ->get();

        return view('projects.show', compact('project', 'relatedProjects'));
    }
}
