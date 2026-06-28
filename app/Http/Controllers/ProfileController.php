<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        ActivityLogger::log('profile.updated', $user, [
            'name' => $user->name,
            'email' => $user->email,
        ]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $user->updatePhoto($request->file('photo'));

        ActivityLogger::log('profile.photo_updated', $user);

        return Redirect::route('profile.edit')->with('status', 'photo-updated');
    }

    public function destroyPhoto(Request $request): RedirectResponse
    {
        $user = $request->user();
        $user->removePhoto();

        ActivityLogger::log('profile.photo_removed', $user);

        return Redirect::route('profile.edit')->with('status', 'photo-removed');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        ActivityLogger::log('user.deleted', $user, [
            'name' => $user->name,
            'email' => $user->email,
        ], $user->id);

        $user->deletePhotoFile();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
