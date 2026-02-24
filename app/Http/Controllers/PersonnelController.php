<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Support\FlashMessage;
use Illuminate\Support\Facades\DB;
use App\Models\Task;


class PersonnelController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->filter ?? 'active'; // default active

        $query = User::query()
            ->withCount([
                'tasks as total_tasks_count',
                'tasks as ongoing_tasks_count' => function ($q) {
                    $q->whereBetween('progress', [1, 99]);
                }
            ]);

        // =========================
        // STATUS FILTER
        // =========================
        if ($status === 'inactive') {
            $query->where('account_status', 'inactive');
        } elseif ($status === 'all') {
            // no filter
        } else {
            $query->where('account_status', 'active');
        }

        // =========================
        // SEARCH
        // =========================
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        $users = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // Status counts (for dropdown labels)
        $statusCounts = [
            'active' => User::where('account_status', 'active')->count(),
            'inactive' => User::where('account_status', 'inactive')->count(),
            'all' => User::count(),
        ];

        return view('personnel.index', compact(
            'users',
            'statusCounts'
        ));
    }

    public function create()
    {
        return view('personnel.create');
    }

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

    public function edit(User $user)
    {
        // If not admin, user can edit only their own profile
        if (!auth()->user()->isAdmin() && auth()->id() !== $user->id) {
            abort(403);
        }

        return view('personnel.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
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

        if (auth()->user()->isAdmin()) {
            $rules['role'] = 'required|in:admin,user';
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // ===============================
        // ADMIN ROLE PROTECTION
        // ===============================

        if (auth()->user()->isAdmin() && isset($validated['role'])) {

            $currentUser = auth()->user();

            // If admin is editing themselves
            if ($user->id === $currentUser->id && $validated['role'] !== 'admin') {

                return back()->with(
                    'error',
                    'You cannot change your own admin role.'
                );
            }

            // Prevent removing last admin in system
            if ($user->role === 'admin' && $validated['role'] !== 'admin') {

                $adminCount = \App\Models\User::where('role', 'admin')
                    ->where('account_status', 'active')
                    ->count();

                if ($adminCount <= 1) {
                    return back()->with(
                        'error',
                        'At least one active admin must remain in the system.'
                    );
                }
            }
        }

        $changes = [];

        // ===============================
        // NORMAL FIELD COMPARISON
        // ===============================

        foreach ($validated as $field => $value) {

            if ($field === 'password') {
                continue;
            }

            $old = $user->$field ?? null;

            if ($field === 'employment_started') {
                $old = $old ? \Carbon\Carbon::parse($old)->format('Y-m-d') : null;
            }

            if ((string)$old !== (string)$value) {
                $changes[$field] = [
                    'old' => $old,
                    'new' => $value,
                ];
            }
        }

        // ===============================
        // ADMIN PASSWORD RESET
        // ===============================

        if (auth()->user()->isAdmin() && !empty($validated['password'])) {

            $changes['password_reset'] = [
                'old' => '********',
                'new' => 'Temporary password set',
            ];
        }

        // ===============================
        // PHOTO CHANGE
        // ===============================

        if ($request->hasFile('photo')) {

            $changes['photo'] = [
                'old' => $user->photo,
                'new' => 'Photo updated',
            ];
        }

        if (empty($changes)) {
            return redirect()
                ->route('personnel.index')
                ->with('warning', FlashMessage::warning('personnel_updated'));
        }

        // ===============================
        // OPTIONAL PASSWORD RESET
        // ===============================

        if (!empty($request->password)) {
            $validated['password'] = \Hash::make($request->password);
        } else {
            unset($validated['password']); // prevent null overwrite
        }

        // ===============================
        // APPLY UPDATE
        // ===============================

        foreach ($validated as $field => $value) {

            if ($field === 'password' && !empty($value)) {
                $user->password = Hash::make($value);
            } elseif ($field !== 'photo') {
                $user->$field = $value;
            }
        }

        if ($request->hasFile('photo')) {
            $user->photo = $request->file('photo')->store('personnel', 'public');
        }

        $user->save();

        return redirect()
            ->route('personnel.index')
            ->with('success', FlashMessage::success('personnel_updated'));
    }

    public function deactivate(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if (auth()->id() === $user->id) {
            return back()->with('error', FlashMessage::error('personnel_deactivated'));
        }

        if ($user->account_status === 'inactive') {
            return back()->with('warning', FlashMessage::warning('personnel_deactivated'));
        }

        DB::transaction(function () use ($user) {

            $user->update([
                'account_status' => 'inactive',
                'deactivated_at' => now(),
            ]);

            Task::where('assigned_user_id', $user->id)
                ->update(['assigned_user_id' => null]);
        });

        return redirect()
            ->route('personnel.index')
            ->with('success', FlashMessage::success('personnel_deactivated'));
    }

    public function reactivate(User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($user->account_status === 'active') {
            return back()
                ->with('warning', FlashMessage::warning('personnel_reactivated'));
        }

        $user->update([
            'account_status' => 'active',
            'deactivated_at' => null,
        ]);

        return redirect()
            ->route('archives.index', ['scope' => 'personnel'])
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

    public function show(User $user)
    {
        // Only admins can view other personnel profiles
        if (!auth()->user()->isAdmin() && auth()->id() !== $user->id) {
            abort(403);
        }

        return view('personnel.show', compact('user'));
    }
}
