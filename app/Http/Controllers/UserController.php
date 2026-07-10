<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Notifications\UserStatusChanged;
use App\Services\ActivityLogger;
use App\Services\UserListQueryService;
use App\Support\CrmValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
    ];

    public function __construct(
        protected UserListQueryService $userListQuery,
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $filters = $request->validate($this->userListQuery->filterRules());

        $users = $this->userListQuery
            ->query($filters)
            ->paginate(10)
            ->withQueryString();

        $roles = Role::query()->orderBy('name')->pluck('name', 'slug');

        return view('users.index', [
            'users' => $users,
            'roles' => $roles,
            'statuses' => self::STATUSES,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $this->authorize('create', User::class);

        return view('users.create', [
            'roles' => Role::query()->orderBy('name')->get(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        return view('users.show', [
            'user' => $user->load('roles'),
            'statuses' => self::STATUSES,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate(CrmValidation::userStoreRules());

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
            'status' => $validated['status'],
        ]);

        $user->syncRoles($validated['roles']);

        if ($request->hasFile('photo')) {
            $user->updatePhoto($request->file('photo'));
        }

        ActivityLogger::log('user.created', $user, [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roleNames(),
            'status' => $user->status,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::query()->orderBy('name')->get(),
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'exists:roles,id'],
            'status' => ['required', Rule::in(array_keys(self::STATUSES))],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $adminRoleId = Role::query()->where('slug', 'admin')->value('id');

        if ($user->id === auth()->id() && $adminRoleId && ! in_array($adminRoleId, $validated['roles'], true)) {
            return back()->withErrors(['roles' => 'You cannot remove your own admin role.'])
                ->withInput();
        }

        if ($user->id === auth()->id() && $validated['status'] !== 'active') {
            return back()->withErrors(['status' => 'You cannot deactivate your own account.'])
                ->withInput();
        }

        $oldStatus = $user->status;

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->status = $validated['status'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles($validated['roles']);

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
            'roles' => $user->roleNames(),
            'status' => $user->status,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

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
        $this->authorize('update', $user);

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
