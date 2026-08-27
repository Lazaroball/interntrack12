{{-- resources/views/admin/users/_modals.blade.php --}}
{{-- Include once per row inside the @foreach loop --}}

{{-- ── DEACTIVATE modal ── --}}
<div x-data x-on:open-modal.window="$event.detail === 'deactivate-{{ $user->id }}' && ($el.style.display='flex')"
     style="display:none"
     class="fixed inset-0 z-50 items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 w-full max-w-md p-6 space-y-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ea580c"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Deactivate Account</h3>
                <p class="text-sm text-slate-500">{{ $user->name }}</p>
            </div>
        </div>
        <p class="text-sm text-slate-600">
            This will prevent <span class="font-semibold">{{ $user->name }}</span> from signing in.
            You can reactivate the account at any time.
        </p>
        <div class="flex justify-end gap-3">
            <button onclick="this.closest('[x-data]').style.display='none'"
                    class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold
                           text-slate-600 hover:bg-slate-50 transition-colors duration-150">
                Cancel
            </button>
            <form method="POST" action="{{ route('admin.users.deactivate', $user) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white
                               hover:bg-orange-600 transition-colors duration-150">
                    Deactivate
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ── ACTIVATE modal ── --}}
<div x-data x-on:open-modal.window="$event.detail === 'activate-{{ $user->id }}' && ($el.style.display='flex')"
     style="display:none"
     class="fixed inset-0 z-50 items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 w-full max-w-md p-6 space-y-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#16a34a"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Activate Account</h3>
                <p class="text-sm text-slate-500">{{ $user->name }}</p>
            </div>
        </div>
        <p class="text-sm text-slate-600">
            <span class="font-semibold">{{ $user->name }}</span> will be able to sign in again after activation.
        </p>
        <div class="flex justify-end gap-3">
            <button onclick="this.closest('[x-data]').style.display='none'"
                    class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold
                           text-slate-600 hover:bg-slate-50 transition-colors duration-150">
                Cancel
            </button>
            <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white
                               hover:bg-green-700 transition-colors duration-150">
                    Activate
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ── RESET PASSWORD modal ── --}}
<div x-data x-on:open-modal.window="$event.detail === 'reset-{{ $user->id }}' && ($el.style.display='flex')"
     style="display:none"
     class="fixed inset-0 z-50 items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 w-full max-w-md p-6 space-y-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#2563eb"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Reset Password</h3>
                <p class="text-sm text-slate-500">{{ $user->name }}</p>
            </div>
        </div>
        <p class="text-sm text-slate-600">
            A temporary password (format: <code class="font-mono bg-slate-100 px-1 rounded">Intern@XXXXXX</code>)
            will be generated. The user will be required to change it on next login.
        </p>
        <div class="flex justify-end gap-3">
            <button onclick="this.closest('[x-data]').style.display='none'"
                    class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold
                           text-slate-600 hover:bg-slate-50 transition-colors duration-150">
                Cancel
            </button>
            <form method="POST" action="{{ route('admin.users.resetPassword', $user) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white
                               hover:bg-blue-700 transition-colors duration-150">
                    Reset Password
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ── DELETE modal ── --}}
<div x-data x-on:open-modal.window="$event.detail === 'delete-{{ $user->id }}' && ($el.style.display='flex')"
     style="display:none"
     class="fixed inset-0 z-50 items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 w-full max-w-md p-6 space-y-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#dc2626"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    <path d="M10 11v6"/><path d="M14 11v6"/>
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Delete Account</h3>
                <p class="text-sm text-slate-500">{{ $user->name }}</p>
            </div>
        </div>
        <div class="rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700 font-medium">
            ⚠️ This action is permanent and cannot be undone.
        </div>
        <p class="text-sm text-slate-600">
            The account for <span class="font-semibold">{{ $user->name }}</span> ({{ $user->email }})
            will be permanently removed from the system.
        </p>
        <div class="flex justify-end gap-3">
            <button onclick="this.closest('[x-data]').style.display='none'"
                    class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold
                           text-slate-600 hover:bg-slate-50 transition-colors duration-150">
                Cancel
            </button>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                @csrf @method('DELETE')
                <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white
                               hover:bg-red-700 transition-colors duration-150">
                    Delete Permanently
                </button>
            </form>
        </div>
    </div>
</div>
