<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{

    public function index()
    {
        $slides = Slide::orderBy('display_order')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('slides.index', compact('slides'));
    }

    public function create()
    {
        return view('slides.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'required|integer',
            'is_active' => 'required|boolean',
        ]);

        $imagePath = $request->file('image')
            ->store('slides', 'public');

        Slide::create([
            'title' => $request->title,
            'description' => $request->description,
            'image_path' => $imagePath,
            'display_order' => $request->display_order,
            'is_active' => $request->is_active,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('slides.index')
            ->with('success', 'Slide created successfully.');
    }

    public function edit(Slide $slide)
    {
        return view('slides.edit', compact('slide'));
    }

    public function update(Request $request, Slide $slide)
    {
        $request->validate([
            'image' => 'nullable|image|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'display_order' => 'required|integer',
            'is_active' => 'required|boolean',
        ]);

        $originalData = $slide->getOriginal();

        // Handle image replacement
        if ($request->hasFile('image')) {

            if ($slide->image_path) {
                Storage::disk('public')->delete($slide->image_path);
            }

            $slide->image_path = $request->file('image')
                ->store('slides', 'public');
        }

        $slide->title = $request->title;
        $slide->description = $request->description;
        $slide->display_order = $request->display_order;
        $slide->is_active = $request->is_active;

        if ($slide->isDirty()) {
            $slide->save();
            return redirect()
                ->route('slides.index')
                ->with('success', 'Slide updated successfully.');
        }

        return redirect()
            ->route('slides.index')
            ->with('warning', 'No changes were made.');
    }

    public function archive(Slide $slide)
    {
        $slide->delete(); // soft delete

        return back()->with('success', 'Slide archived successfully.');
    }

    public function archived()
    {
        $slides = Slide::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->paginate(10);

        return view('archives.slides', compact('slides'));
    }

    public function restore($id)
    {
        $slide = Slide::onlyTrashed()->findOrFail($id);

        $slide->restore();

        return redirect()
            ->route('slides.archived')
            ->with('success', 'Slide restored successfully.');
    }
}
