<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

/** CRUD пользователей (роли: admin, manager, cashier). */
class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->whereIn('role', ['manager', 'cashier'])
            ->orderBy('name')
            ->paginate((int) $request->get('per_page', 15));
        $usersData = $users->getCollection()->map(fn ($u) => (new UserResource($u))->toArray(request()))->values()->all();

        return Inertia::render('Panel/Users/Index', [
            'users' => $usersData,
            'meta' => ['current_page' => $users->currentPage(), 'last_page' => $users->lastPage(), 'total' => $users->total()],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Panel/Users/Create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'role' => $request->validated('role'),
        ]);

        AuditLog::write(User::class, $user->id, 'create');

        $perPage = 15;
        $rank = User::query()
            ->where('name', '<', $user->name)
            ->orWhere(fn ($q) => $q->where('name', $user->name)->where('id', '<=', $user->id))
            ->count();
        $page = max(1, (int) ceil($rank / $perPage));

        return redirect()->route('panel.users.index', ['page' => $page])->with('success', __('user.created'));
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Panel/Users/Edit', [
            'editingUser' => (new UserResource($user))->toArray(request()),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        foreach (['name', 'email', 'role'] as $field) {
            if (array_key_exists($field, $data) && $user->getAttribute($field) !== $data[$field]) {
                AuditLog::write(User::class, $user->id, 'update', $field, $user->getAttribute($field), $data[$field]);
            }
        }
        if (isset($data['password'])) {
            AuditLog::write(User::class, $user->id, 'update', 'password', null, '[updated]');
        }

        $user->update($data);

        return redirect()->route('panel.users.index')->with('success', __('user.updated'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('panel.users.index')->with('error', __('user.cant_delete_self'));
        }

        AuditLog::write(User::class, $user->id, 'delete');
        $user->delete();

        return redirect()->route('panel.users.index')->with('success', __('user.deleted'));
    }
}
