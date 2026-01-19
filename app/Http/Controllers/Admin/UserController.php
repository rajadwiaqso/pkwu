<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

/**
 * UserController - Menangani user management untuk super admin
 */
class UserController extends Controller
{
    /**
     * List all users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        // Filter by status
        if ($status = $request->get('status')) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($status === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total' => User::count(),
            'buyers' => User::where('role', 'buyer')->count(),
            'admins' => User::whereIn('role', ['admin', 'super_admin'])->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
        ];

        return Inertia::render('Admin/User/Index', [
            'users' => $users,
            'stats' => $stats,
            'filters' => $request->only(['search', 'role', 'status']),
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        return Inertia::render('Admin/User/Create');
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:8',
            'role' => 'required|in:buyer,admin,super_admin',
            'is_active' => 'boolean',
            'email_verified' => 'boolean',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $validated['is_active'] ?? true,
            'email_verified_at' => ($validated['email_verified'] ?? false) ? now() : null,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show edit form
     */
    public function edit(User $user)
    {
        return Inertia::render('Admin/User/Edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:8',
            'role' => 'required|in:buyer,admin,super_admin',
            'is_active' => 'boolean',
            'email_verified' => 'boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'is_active' => $validated['is_active'] ?? true,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        // Handle email verification
        if (($validated['email_verified'] ?? false) && !$user->email_verified_at) {
            $updateData['email_verified_at'] = now();
        } elseif (!($validated['email_verified'] ?? false)) {
            $updateData['email_verified_at'] = null;
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(User $user)
    {
        // Prevent deactivating self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', 'Status user berhasil diperbarui.');
    }

    /**
     * Verify user email manually
     */
    public function verifyEmail(User $user)
    {
        $user->update(['email_verified_at' => now()]);

        return back()->with('success', 'Email user berhasil diverifikasi.');
    }
}
