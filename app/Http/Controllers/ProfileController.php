<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        $user->loadCount([
            'tasks as total_tasks_count',
            'tasks as ongoing_tasks_count' => function ($query) {
                $query->whereBetween('progress', [1, 99]);
            },
            'tasks as completed_tasks_count' => function ($query) {
                $query->where('progress', 100);
            },
        ]);

        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        return view('profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'max:2048'],

            // password fields
            'current_password' => ['nullable'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        /**
         * 🔒 PASSWORD INTENT VALIDATION (ALL-OR-NOTHING RULE)
         * --------------------------------------------------
         * Prevent:
         * - current password only
         * - new password only
         */
        if ($request->filled('current_password') && !$request->filled('password')) {
            return back()
                ->withErrors([
                    'password' => 'Please enter a new password if you want to change your password.',
                ])
                ->withInput();
        }

        if ($request->filled('password') && !$request->filled('current_password')) {
            return back()
                ->withErrors([
                    'current_password' => 'Current password is required to change your password.',
                ])
                ->withInput();
        }

        /**
         * 🔐 PASSWORD HARDENING LOGIC
         */
        if (!empty($validated['password'])) {

            // Current password must be correct
            if (!\Hash::check($validated['current_password'], $user->password)) {
                return back()
                    ->withErrors(['current_password' => 'Current password is incorrect.'])
                    ->withInput();
            }

            // Hash the new password
            $validated['password'] = \Hash::make($validated['password']);
        } else {
            // No password change intended
            unset($validated['password']);
        }

        // Remove current_password from update data
        unset($validated['current_password']);

        /**
         * 📸 Photo upload (optional)
         */
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->update($validated);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Your profile has been updated successfully.');
    }


    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
