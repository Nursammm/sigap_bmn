<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update profil (name, email, password, photo) dalam satu form.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // 1) Rules dasar
        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];

        // 2) Rules password hanya bila diisi
        if ($request->filled('password')) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['password']         = ['required', 'confirmed', Password::min(8)];
        }

        $validated = $request->validate($rules);

        // 3) Update nama & email
        $user->name = $validated['name'];
        if ($user->email !== $validated['email']) {
            $user->email = $validated['email'];
            if ($this->schemaHasColumn('users', 'email_verified_at')) {
                $user->email_verified_at = null;
            }
        }

        // 4) Update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        // 5) Upload foto (opsional)
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');

            if ($this->schemaHasColumn('users', 'profile_photo_path')) {
                $user->profile_photo_path = $path;
            } elseif ($this->schemaHasColumn('users', 'avatar')) {
                $user->avatar = $path;
            }
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Hapus akun.
     */
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

    /**
     * Helper untuk cek kolom pada tabel (aman dipakai di shared hosting).
     */
    private function schemaHasColumn(string $table, string $column): bool
    {
        try {
            return Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}