<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Support\FlashMessage;


class PersonnelController extends Controller
{
    /**
     * Display a list of personnel (admin only).
     */
    public function index()
    {
        $users = User::where('account_status', 'active')
            ->withCount([
                // Total tasks
                'tasks as total_tasks_count',

                // Ongoing tasks (progress 1–99)
                'tasks as ongoing_tasks_count' => function ($query) {
                    $query->whereBetween('progress', [1, 99]);
                }
            ])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('personnel.index', compact('users'));
    }

    /**
     * Show form to create a new personnel.
     */
    public function create()
    {
        return view('personnel.create');
    }

    /**
     * Store a newly created personnel.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'contact_number' => 'nullable|string|max:50',
            'role' => 'required|in:admin,user',
            'password' => 'required|string|min:8|confirmed',
            'designation' => 'nullable|string|max:255',
            'profession' => 'nullable|string|max:255',
            'employment_status' => 'nullable|string|max:50',
            'employment_started' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',

        ]);

        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('personnel', 'public');
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'] ?? null,
            'role' => $validated['role'],
            'designation' => $validated['designation'] ?? null,
            'profession' => $validated['profession'] ?? null,
            'employment_status' => $validated['employment_status'] ?? null,
            'employment_started' => $validated['employment_started'] ?? null,
            'password' => Hash::make($validated['password']),
            'account_status' => 'active',
            'photo' => $photoPath,
        ]);

        return redirect()
            ->route('personnel.index')
            ->with('success', FlashMessage::success('personnel_created'));
    }

    /**
     * Show form to edit personnel.
     */
    public function edit(User $user)
    {
        // If not admin, user can edit only their own profile
        if (!auth()->user()->isAdmin() && auth()->id() !== $user->id) {
            abort(403);
        }

        return view('personnel.edit', compact('user'));
    }

    /**
     * Update personnel information.
     */
    public function update(Request $request, User $user)
    {
        // Authorization check
        if (!auth()->user()->isAdmin() && auth()->id() !== $user->id) {
            abort(403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'contact_number' => 'nullable|string|max:50',
            'designation' => 'nullable|string|max:255',
            'profession' => 'nullable|string|max:255',
            'employment_status' => 'nullable|string|max:50',
            'employment_started' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
        ];

        // Only admin can update role
        if (auth()->user()->isAdmin()) {
            $rules['role'] = 'required|in:admin,user';
        }

        $validated = $request->validate($rules);

        unset($validated['photo']);

        $user->update($validated);

        // Upload Photo
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('personnel', 'public');
            $user->photo = $photoPath;
            $user->save();
        }

        return redirect()
            ->route('personnel.index')
            ->with('success', FlashMessage::success('personnel_updated'));
    }

    /**
     * Deactivate personnel (soft action).
     */
    public function deactivate(User $user)
    {
        // Admin only
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        // Prevent self-deactivation
        if (auth()->id() === $user->id) {
            return back()->with('error', FlashMessage::success('personnel_deactivated'));
        }

        $user->update([
            'account_status' => 'inactive',
            'deactivated_at' => now(),
        ]);

        return redirect()
            ->route('personnel.index')
            ->with('success', FlashMessage::success('personnel_deactivated'));
    }

    /**
     * Reactivate personnel.
     */
    public function reactivate(User $user)
    {
        // Admin only
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $user->update([
            'account_status' => 'active',
            'deactivated_at' => null,
        ]);

        return redirect()
            ->route('personnel.archived')
            ->with('success', FlashMessage::success('personnel_reactivated'));
    }

    public function archived()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $users = User::withoutGlobalScopes()
            ->where('account_status', '!=', 'active')
            ->withCount([
                'tasks as total_tasks_count',
                'tasks as ongoing_tasks_count' => function ($query) {
                    $query->whereBetween('progress', [1, 99]);
                }
            ])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('archives.personnel', compact('users'));
    }
}
