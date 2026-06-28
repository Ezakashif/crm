<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\UserStatusChanged;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public const ROLES = [
        'admin' => 'Admin',
        'user' => 'User',
    ];

    public const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
    ];

    public function index()
    {
        $users = User::latest()->paginate(10);

        return view('users.index', [
            'users' => $users,
            'roles' => self::ROLES,
            'statuses' => self::STATUSES,
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'roles' => self::ROLES,
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(array_keys(self::ROLES))],
            'status' => ['required', Rule::in(array_keys(self::STATUSES))],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        if ($request->hasFile('photo')) {
            $user->updatePhoto($request->file('photo'));
        }

        ActivityLogger::log('user.created', $user, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => self::ROLES,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(array_keys(self::ROLES))],
            'status' => ['required', Rule::in(array_keys(self::STATUSES))],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot remove your own admin role.'])
                ->withInput();
        }

        if ($user->id === auth()->id() && $validated['status'] !== 'active') {
            return back()->withErrors(['status' => 'You cannot deactivate your own account.'])
                ->withInput();
        }

        $oldStatus = $user->status;

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->status = $validated['status'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if ($request->hasFile('photo')) {
            $user->updatePhoto($request->file('photo'));
        }

        if ($oldStatus !== $validated['status']) {
            $this->notifyStatusChange($user, $oldStatus, $validated['status']);

            ActivityLogger::log('user.status_changed', $user, [
                'from' => $oldStatus,
                'to' => $validated['status'],
            ]);
        }

        ActivityLogger::log('user.updated', $user, [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        ActivityLogger::log('user.deleted', $user, [
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $user->deletePhotoFile();
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function changeStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(self::STATUSES))],
        ]);

        if ($user->id === auth()->id() && $validated['status'] !== 'active') {
            return back()->withErrors(['error' => 'You cannot deactivate your own account.']);
        }

        $oldStatus = $user->status;

        if ($oldStatus === $validated['status']) {
            return back()->with('success', 'User status unchanged.');
        }

        $user->update(['status' => $validated['status']]);

        $this->notifyStatusChange($user, $oldStatus, $validated['status']);

        ActivityLogger::log('user.status_changed', $user, [
            'from' => $oldStatus,
            'to' => $validated['status'],
        ]);

        return back()->with('success', 'User status updated successfully.');
    }

    protected function notifyStatusChange(User $user, string $oldStatus, string $newStatus): void
    {
        $user->notify(new UserStatusChanged($oldStatus, $newStatus, auth()->user()));
    }
}
