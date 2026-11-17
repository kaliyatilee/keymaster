<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'string', 'exists:roles,name'],
            'status' => ['nullable', 'in:active,pending_consent,suspended'],
        ]);

        $users = User::query()
            ->with('roles:id,name')
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['role'] ?? null, fn ($q, string $role) => $q->whereHas('roles', fn ($r) => $r->where('name', $role)))
            ->when($filters['status'] ?? null, fn ($q, string $status) => $q->where('status', $status))
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString()
            ->through(fn (User $user) => [
                'id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->all(),
                'status' => $user->status->value,
                'account_type' => $user->account_type->value,
                'created_at' => $user->created_at?->toISOString(),
                'last_login_at' => $user->last_login_at?->toISOString(),
            ]);

        return Inertia::render('admin/users/index', [
            'users' => $users,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'role' => $filters['role'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
            'roles' => Role::query()->orderBy('id')->get(['name', 'label']),
        ]);
    }
}
