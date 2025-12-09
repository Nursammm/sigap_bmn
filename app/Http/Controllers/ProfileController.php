<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];

        if ($request->filled('password')) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['password']         = ['required', 'confirmed', Password::min(8)];
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        if ($user->email !== $validated['email']) {
            $user->email = $validated['email'];
            if ($this->schemaHasColumn('users', 'email_verified_at')) {
                $user->email_verified_at = null;
            }
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('photo')) {
            $oldPath = $user->profile_photo_path ?? $user->avatar ?? null;
            $path    = $request->file('photo')->store('profile-photos', 'public');

            if ($this->schemaHasColumn('users', 'profile_photo_path')) {
                $user->profile_photo_path = $path;
            } elseif ($this->schemaHasColumn('users', 'avatar')) {
                $user->avatar = $path;
            }

            if ($oldPath && $oldPath !== $path && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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

    private function schemaHasColumn(string $table, string $column): bool
    {
        try {
            return Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
