<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Coordinator;
use App\Models\Supervisor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Only Coordinator and Supervisor accounts are managed here.
     * Admin and Student accounts are never touched.
     */
    private const MANAGED_ROLES = ['coordinator', 'supervisor'];

    // ─────────────────────────────────────────────
    //  INDEX
    // ─────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = User::whereIn('role', self::MANAGED_ROLES)
            ->orderBy('created_at', 'desc');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($role = $request->input('role')) {
            if (in_array($role, self::MANAGED_ROLES)) {
                $query->where('role', $role);
            }
        }

        // Status filter  (boolean: 1 = active, 0 = inactive)
        if ($request->filled('status')) {
            $query->where('status', (bool) $request->input('status'));
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    // ─────────────────────────────────────────────
    //  CREATE
    // ─────────────────────────────────────────────
    public function create()
    {
        return view('admin.users.create');
    }

    // ─────────────────────────────────────────────
    //  STORE
    // ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],
            'middle_name'   => ['nullable', 'string', 'max:100'],
            'email'         => ['required', 'email', 'max:255', 'unique:users,email'],
            'mobile_number' => ['nullable', 'string', 'max:20'],
            'role'          => ['required', Rule::in(self::MANAGED_ROLES)],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | Create User Account
            |--------------------------------------------------------------------------
            */
            $user = User::create([
                'first_name'           => $validated['first_name'],
                'last_name'            => $validated['last_name'],
                'middle_name'          => $validated['middle_name'] ?? null,
                'name'                 => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'email'                => $validated['email'],
                'mobile_number'        => $validated['mobile_number'] ?? null,
                'role'                 => $validated['role'],
                'password'             => Hash::make($validated['password']),
                'status'               => true,
                'must_change_password' => false,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Coordinator Profile (linked via user_id)
            |--------------------------------------------------------------------------
            */
            if ($validated['role'] === 'coordinator') {

                $user->coordinator()->create([
                    'employee_number' => null,
                    'first_name'      => $validated['first_name'],
                    'middle_name'     => $validated['middle_name'] ?? null,
                    'last_name'       => $validated['last_name'],
                    'department'      => null,
                    'position'        => null,
                    'status'          => true,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Create Supervisor Profile
            |--------------------------------------------------------------------------
            */
            if ($validated['role'] === 'supervisor') {

                $user->supervisor()->create([
                    'employee_number' => null,
                    'first_name'      => $validated['first_name'],
                    'middle_name'     => $validated['middle_name'] ?? null,
                    'last_name'       => $validated['last_name'],
                    'department'      => null,
                    'status'          => true,
                ]);
            }

            // Record Admin activity
            ActivityLog::record(
                'Created account',
                'Users',
                "Created {$validated['role']} account for {$user->name}.",
                $user
            );
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Account created successfully.');
    }

    // ─────────────────────────────────────────────
    //  EDIT
    // ─────────────────────────────────────────────
    public function edit(User $user)
    {
        $this->authorizeManaged($user);

        return view('admin.users.edit', compact('user'));
    }

    // ─────────────────────────────────────────────
    //  UPDATE
    // ─────────────────────────────────────────────
    public function update(Request $request, User $user)
    {
        $this->authorizeManaged($user);

        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],
            'middle_name'   => ['nullable', 'string', 'max:100'],
            'email'         => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'mobile_number' => ['nullable', 'string', 'max:20'],
            'role'          => ['required', Rule::in(self::MANAGED_ROLES)],
            'status'        => ['required', 'boolean'],
        ]);

        // Save old role before updating
        $oldRole = $user->role;

        // Update User
        $user->update([
            'first_name'    => $validated['first_name'],
            'last_name'     => $validated['last_name'],
            'middle_name'   => $validated['middle_name'] ?? null,
            'name'          => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'email'         => $validated['email'],
            'mobile_number' => $validated['mobile_number'] ?? null,
            'role'          => $validated['role'],
            'status'        => (bool) $validated['status'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Role Changed?
        |--------------------------------------------------------------------------
        */

        if ($oldRole !== $validated['role']) {

            // Remove old profile
            if ($oldRole === 'coordinator' && $user->coordinator) {
                $user->coordinator()->delete();
            }

            if ($oldRole === 'supervisor' && $user->supervisor) {
                $user->supervisor()->delete();
            }

            // Create Coordinator profile
            if ($validated['role'] === 'coordinator') {

                $user->coordinator()->create([
                    'employee_number' => 'COORD-' . time(),
                    'first_name'      => $validated['first_name'],
                    'middle_name'     => $validated['middle_name'] ?? null,
                    'last_name'       => $validated['last_name'],
                    'department'      => 'College of Teacher Education',
                    'position'        => 'Coordinator',
                    'status'          => (bool) $validated['status'],
                ]);
            }

            // Create Supervisor profile
            if ($validated['role'] === 'supervisor') {

                $user->supervisor()->create([
                    'employee_number' => 'SUP-' . time(),
                    'first_name'      => $validated['first_name'],
                    'middle_name'     => $validated['middle_name'] ?? null,
                    'last_name'       => $validated['last_name'],
                    'department'      => 'College of Teacher Education',
                    'status'          => (bool) $validated['status'],
                ]);
            }

        } else {

            // Update existing Coordinator profile
            if ($user->role === 'coordinator' && $user->coordinator) {

                $user->coordinator->update([
                    'first_name'  => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name'   => $validated['last_name'],
                    'status'      => (bool) $validated['status'],
                ]);
            }

            // Update existing Supervisor profile
            if ($user->role === 'supervisor' && $user->supervisor) {

                $user->supervisor->update([
                    'first_name'  => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?? null,
                    'last_name'   => $validated['last_name'],
                    'status'      => (bool) $validated['status'],
                ]);
            }
        }

        // Record Admin activity
        ActivityLog::record(
            'Updated account',
            'Users',
            "Updated the account of {$user->name}.",
            $user
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Account updated successfully.');
    }

    // ─────────────────────────────────────────────
    //  DESTROY
    // ─────────────────────────────────────────────
    public function destroy(User $user)
    {
        $this->authorizeManaged($user);

        // Record Admin activity before deleting the user
        ActivityLog::record(
            'Deleted account',
            'Users',
            "Deleted the {$user->role} account of {$user->name} ({$user->email}).",
            $user
        );

        if ($user->coordinator) {
            $user->coordinator->delete();
        }

        if ($user->supervisor) {
            $user->supervisor->delete();
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Account deleted successfully.');
    }

    // ─────────────────────────────────────────────
    //  ACTIVATE
    // ─────────────────────────────────────────────
    public function activate(User $user)
    {
        $this->authorizeManaged($user);

        $user->update([
            'status' => true,
        ]);

        if ($user->coordinator) {
            $user->coordinator->update([
                'status' => true,
            ]);
        }

        if ($user->supervisor) {
            $user->supervisor->update([
                'status' => true,
            ]);
        }

        // Record Admin activity
        ActivityLog::record(
            'Activated account',
            'Users',
            "Activated the account of {$user->name}.",
            $user
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', "{$user->name}'s account has been activated.");
    }

    // ─────────────────────────────────────────────
    //  DEACTIVATE
    // ─────────────────────────────────────────────
    public function deactivate(User $user)
    {
        $this->authorizeManaged($user);

        $user->update([
            'status' => false,
        ]);

        if ($user->coordinator) {
            $user->coordinator->update([
                'status' => false,
            ]);
        }

        if ($user->supervisor) {
            $user->supervisor->update([
                'status' => false,
            ]);
        }

        // Record Admin activity
        ActivityLog::record(
            'Deactivated account',
            'Users',
            "Deactivated the account of {$user->name}.",
            $user
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', "{$user->name}'s account has been deactivated.");
    }

    // ─────────────────────────────────────────────
    //  RESET PASSWORD
    // ─────────────────────────────────────────────
    public function resetPassword(User $user)
    {
        $this->authorizeManaged($user);

        $random    = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $temporary = "Intern@{$random}";

        $user->update([
            'password'            => Hash::make($temporary),
            'must_change_password' => true,
        ]);

        // Record Admin activity
        // Password itself is deliberately NOT logged.
        ActivityLog::record(
            'Reset password',
            'Users',
            "Reset the password of {$user->name}.",
            $user
        );

        return redirect()
            ->route('admin.users.index')
            ->with('password_reset', [
                'name'     => $user->name,
                'password' => $temporary,
            ]);
    }

    // ─────────────────────────────────────────────
    //  GUARD: only allow coordinator / supervisor
    // ─────────────────────────────────────────────
    private function authorizeManaged(User $user): void
    {
        if (! in_array($user->role, self::MANAGED_ROLES)) {
            abort(403, 'This account cannot be managed here.');
        }
    }
}
