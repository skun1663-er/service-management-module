<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query()->latest();

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $services = $query->paginate(10);

        return view('admin.services.index', compact('services', 'request'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:100',
            'description' => 'required|min:30',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:0,1',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:1024'
        ]);

        $validated['slug'] = $this->generateUniqueSlug($request->title);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('services', 'public');
        }

        Service::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service created successfully.');
    }

    public function show(Service $service)
    {
        //
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
{
    $validated = $request->validate([
        'title' => 'required|max:100',
        'description' => 'required|min:30',
        'price' => 'nullable|numeric|min:0',
        'status' => 'required|in:0,1',
        'image' => 'nullable|image|mimes:jpeg,png,webp|max:1024',
    ]);

    // Auto-generate slug from title (handles uniqueness)
    $validated['slug'] = $this->generateUniqueSlug($request->title, $service->id);

    // Delete old image if new one is uploaded
    if ($request->hasFile('image')) {
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }
        $validated['image'] = $request->file('image')->store('services', 'public');
    }

    $service->update($validated);

    return redirect()->route('admin.services.index')
        ->with('success', 'Service updated successfully.');
}


    public function destroy(Service $service)
    {
        // Delete image if exists
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully.');
    }

    private function generateUniqueSlug($title, $ignoreId = null)
{
    $slug = Str::slug($title);
    $originalSlug = $slug;

    $count = 1;
    while (Service::where('slug', $slug)
        ->when($ignoreId, function ($query) use ($ignoreId) {
            return $query->where('id', '!=', $ignoreId);
        })
        ->exists()) {
        $slug = $originalSlug . '-' . $count++;
    }

    return $slug;
}

}
