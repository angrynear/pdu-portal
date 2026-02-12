<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Support\FlashMessage;

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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'profession' => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'max:2048'],

            // Password fields
            'current_password' => ['nullable'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        /*
    |--------------------------------------------------------------------------
    | 🔒 PASSWORD INTENT VALIDATION (ALL-OR-NOTHING RULE)
    |--------------------------------------------------------------------------
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

        /*
    |--------------------------------------------------------------------------
    | 🔐 PASSWORD HARDENING LOGIC
    |--------------------------------------------------------------------------
    */

        if (!empty($validated['password'])) {

            if (!\Hash::check($validated['current_password'], $user->password)) {
                return back()
                    ->withErrors([
                        'current_password' => 'Current password is incorrect.',
                    ])
                    ->withInput();
            }

            $validated['password'] = \Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        unset($validated['current_password']);

        /*
    |--------------------------------------------------------------------------
    | 📸 Photo Upload
    |--------------------------------------------------------------------------
    */

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')
                ->store('profile-photos', 'public');
        }

        /*
    |--------------------------------------------------------------------------
    | 📧 EMAIL CHANGE DETECTION (TEST-SAFE & LARAVEL-STYLE)
    |--------------------------------------------------------------------------
    */

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.show')
            ->with('success', FlashMessage::success('profile_updated'));
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
