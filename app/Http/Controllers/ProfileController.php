<?php

namespace App\Http\Controllers;

use App\Models\NutritionGoal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $goal = $user->getOrCreateGoal();

        return view('profile.index', compact('user', 'goal'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'calorie_goal' => ['required', 'numeric', 'min:500', 'max:10000'],
            'protein_goal' => ['required', 'numeric', 'min:10', 'max:500'],
            'carbohydrates_goal' => ['required', 'numeric', 'min:10', 'max:1000'],
            'fat_goal' => ['required', 'numeric', 'min:5', 'max:500'],
            'dietary_preferences' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah dipakai oleh akun lain.',
            'calorie_goal.required' => 'Target kalori harian wajib diisi.',
            'protein_goal.required' => 'Target protein harian wajib diisi.',
            'carbohydrates_goal.required' => 'Target karbohidrat harian wajib diisi.',
            'fat_goal.required' => 'Target lemak harian wajib diisi.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        NutritionGoal::updateOrCreate(
            ['user_id' => $user->id],
            [
                'calorie_goal' => (int) $validated['calorie_goal'],
                'protein_goal' => (int) $validated['protein_goal'],
                'carbohydrates_goal' => (int) $validated['carbohydrates_goal'],
                'fat_goal' => (int) $validated['fat_goal'],
                'dietary_preferences' => $validated['dietary_preferences'],
            ]
        );

        return back()->with('success', 'Profil dan target nutrisi berhasil diperbarui!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal :min karakter.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password Anda telah berhasil diperbarui.');
    }
}
