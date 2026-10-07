<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudentRecordController extends Controller
{
    private const PER_PAGE_OPTIONS = [10, 25, 50, 100];

    /** Filters shared by the list and by "group" actions. */
    private const FILTER_KEYS = ['search', 'program', 'year_level', 'block', 'internship', 'archived_as', 'batch'];

    /** Disks checked when removing uploaded files on permanent delete. */
    private const DISKS = ['local', 'public'];

    // ─────────────────────────────────────────────
    //  INDEX  (?tab=active | archived)
    // ─────────────────────────────────────────────
    public function index(Request $request)
    {
        $tab      = $request->input('tab') === 'archived' ? 'archived' : 'active';
        $archived = $tab === 'archived';

        $perPage = in_array((int) $request->input('per_page'), self::PER_PAGE_OPTIONS, true)
            ? (int) $request->input('per_page')
            : 10;

        $base = fn () => $archived ? Student::onlyArchived() : Student::query();

        $filtered = $this->applyFilters($base(), $request, $archived);

        // Used by the Passed modal for "select all matching" group actions.
        $matchingCompleted = (clone $filtered)->whereNotNull('internship_completed_at')->count();

        $students = (clone $filtered)
            ->with($archived ? ['user', 'archivedBy'] : ['user'])
            ->when(
                $archived,
                fn ($q) => $q->orderByDesc('archived_at'),
                fn ($q) => $q->orderBy('last_name')->orderBy('first_name')
            )
            ->paginate($perPage)
            ->withQueryString();

        $completedIds = $students->getCollection()
            ->filter(fn ($s) => $s->internship_completed_at !== null)
            ->pluck('id')
            ->values();

        // Filter dropdown options come from the set being viewed.
        $programs = $base()->whereNotNull('program')->where('program', '!=', '')
            ->distinct()->orderBy('program')->pluck('program');

        $blocks = $base()->whereNotNull('block')->where('block', '!=', '')
            ->distinct()->orderBy('block')->pluck('block');

        $hasNoBlock = $base()->where(fn ($q) => $q->whereNull('block')->orWhere('block', ''))->exists();

        $yearLevels = $base()->whereNotNull('year_level')->distinct()->orderBy('year_level')->pluck('year_level');

        $batches = $archived
            ? Student::onlyArchived()
                ->whereNotNull('archive_batch')
                ->selectRaw('archive_batch, archive_reason, MAX(archived_at) as archived_at, COUNT(*) as total')
                ->groupBy('archive_batch', 'archive_reason')
                ->orderByDesc('archived_at')
                ->get()
            : collect();

        $counts = [
            'active'    => Student::count(),
            'completed' => Student::whereNotNull('internship_completed_at')->count(),
            'archived'  => Student::onlyArchived()->count(),
            'passed'    => Student::onlyArchived()->where('archive_reason', 'passed')->count(),
            'dropped'   => Student::onlyArchived()->where('archive_reason', 'dropped')->count(),
        ];

        $hasFilter = $this->hasFilter($request, $archived);

        return view('admin.student-records.index', compact(
            'tab', 'students', 'completedIds', 'matchingCompleted',
            'programs', 'blocks', 'hasNoBlock', 'yearLevels', 'batches',
            'counts', 'hasFilter', 'perPage'
        ));
    }

    // ─────────────────────────────────────────────
    //  ARCHIVE  (single, selected, or all matching filters)
    // ─────────────────────────────────────────────
    public function archive(Request $request)
    {
        $this->validateTargets($request);

        $data = $request->validate([
            'reason'  => ['required', Rule::in(['passed', 'dropped'])],
            'remarks' => [
                Rule::requiredIf(fn () => $request->input('reason') === 'dropped'),
                'nullable', 'string', 'max:500',
            ],
        ], [
            'remarks.required' => 'Please add a short remark explaining why the student was dropped.',
        ]);

        $query = $this->resolveTargets($request, false);

        if ($query === null) {
            return $this->noFilterError();
        }

        $students = $query->with('user')->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No matching students were found.');
        }

        // Passed requires a completed Internship. Dropped is allowed for anyone.
        $eligible = $data['reason'] === 'passed'
            ? $students->filter(fn ($s) => $s->internship_completed_at !== null)
            : $students;

        $skipped = $students->count() - $eligible->count();

        if ($eligible->isEmpty()) {
            return back()->with(
                'error',
                "None of the selected students completed the Internship, so they can't be archived as Passed. Use Dropped instead."
            );
        }

        $batch = (string) Str::uuid();

        DB::transaction(function () use ($eligible, $data, $batch) {
            foreach ($eligible as $student) {
                $user = $student->user;

                $student->forceFill([
                    'archived_at'                => now(),
                    'archived_by'                => auth()->id(),
                    'archive_reason'             => $data['reason'],
                    'archive_remarks'            => $data['remarks'] ?? null,
                    'archive_batch'              => $batch,
                    'user_status_before_archive' => $user ? (bool) $user->status : null,
                ])->save();

                // Disable the login. The middleware also kills open sessions.
                $user?->update(['status' => false]);
            }
        });

        $label   = $data['reason'] === 'passed' ? 'Passed' : 'Dropped';
        $message = $eligible->count() . ' ' . Str::plural('student', $eligible->count()) . " archived as {$label}.";

        if ($skipped > 0) {
            $message .= " {$skipped} skipped (Internship not completed).";
        }

        return $this->backToList($request, 'active')->with('success', $message);
    }

    // ─────────────────────────────────────────────
    //  RESTOR
    // ─────────────────────────────────────────────
    public function restore(Request $request)
    {
        $this->validateTargets($request);

        $query = $this->resolveTargets($request, true);

        if ($query === null) {
            return $this->noFilterError();
        }

        $students = $query->with('user')->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No matching archived students were found.');
        }

        DB::transaction(function () use ($students) {
            foreach ($students as $student) {
                $user = $student->user;

                // Put the login back to whatever it was before archiving.
                $user?->update(['status' => $student->user_status_before_archive ?? true]);

                $student->forceFill([
                    'archived_at'                => null,
                    'archived_by'                => null,
                    'archive_reason'             => null,
                    'archive_remarks'            => null,
                    'archive_batch'              => null,
                    'user_status_before_archive' => null,
                ])->save();
            }
        });

        $n = $students->count();

        return $this->backToList($request, 'archived')->with(
            'success',
            $n . ' ' . Str::plural('student', $n) . ' restored. Accounts and records are active again.'
        );
    }

    // ─────────────────────────────────────────────
    //  PERMANENT DELETE  (archived students only)
    // ─────────────────────────────────────────────
    public function forceDelete(Request $request)
    {
        $this->validateTargets($request);

        if ($request->input('confirm') !== 'DELETE') {
            return back()->with('error', 'Permanent delete cancelled. Type DELETE exactly to confirm.');
        }

        $query = $this->resolveTargets($request, true);

        if ($query === null) {
            return $this->noFilterError();
        }

        $students = $query->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No matching archived students were found.');
        }

        $studentIds = $students->pluck('id')->all();
        $userIds    = $students->pluck('user_id')->all();

        // Collect file paths BEFORE the rows disappear (raw queries bypass the archive scopes).
        $files = $this->collectFilePaths($students);

        try {
            DB::transaction(function () use ($studentIds, $userIds) {
                // Child tables (deployments, requirements, daily_logs, evaluations,
                // lesson_plans, observation_schedules, field_study_requests,
                // other_evaluator_results) are removed by ON DELETE CASCADE.
                DB::table('students')->whereIn('id', $studentIds)->delete();
                DB::table('users')->whereIn('id', $userIds)->where('role', 'student')->delete();
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Permanent delete failed and nothing was removed. Please check the logs.');
        }

        // Only after the database delete succeeded.
        $this->deleteFiles($files);

        $n = count($studentIds);

        return $this->backToList($request, 'archived')->with(
            'success',
            $n . ' ' . Str::plural('student', $n) . ' permanently deleted.'
        );
    }

    // ═════════════════════════════════════════════
    //  Helpers
    // ═════════════════════════════════════════════

    private function applyFilters(Builder $query, Request $request, bool $archived): Builder
    {
        if ($request->filled('search')) {
            $s = trim((string) $request->input('search'));

            $query->where(function ($q) use ($s) {
                $q->where('student_number', 'like', "%{$s}%")
                    ->orWhere('first_name', 'like', "%{$s}%")
                    ->orWhere('last_name', 'like', "%{$s}%")
                    ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", ["%{$s}%"]);
            });
        }

        $programs = array_values(array_filter((array) $request->input('program')));
        if ($programs) {
            $query->whereIn('program', $programs);
        }

        if ($request->filled('year_level')) {
            $query->where('year_level', (int) $request->input('year_level'));
        }

        if ($request->filled('block')) {
            if ($request->input('block') === 'none') {
                $query->where(fn ($q) => $q->whereNull('block')->orWhere('block', ''));
            } else {
                $query->where('block', $request->input('block'));
            }
        }

        match ($request->input('internship')) {
            'completed'  => $query->whereNotNull('internship_completed_at'),
            'incomplete' => $query->whereNull('internship_completed_at'),
            default      => null,
        };

        if ($archived) {
            if (in_array($request->input('archived_as'), ['passed', 'dropped'], true)) {
                $query->where('archive_reason', $request->input('archived_as'));
            }

            if ($request->filled('batch')) {
                $query->where('archive_batch', $request->input('batch'));
            }
        }

        return $query;
    }

    private function hasFilter(Request $request, bool $archived): bool
    {
        if (array_filter((array) $request->input('program'))) {
            return true;
        }

        $keys = ['search', 'year_level', 'block', 'internship'];

        if ($archived) {
            $keys = array_merge($keys, ['archived_as', 'batch']);
        }

        foreach ($keys as $key) {
            if ($request->filled($key)) {
                return true;
            }
        }

        return false;
    }

    private function validateTargets(Request $request): void
    {
        $request->validate([
            'mode'  => ['required', Rule::in(['selected', 'filtered'])],
            'ids'   => ['required_if:mode,selected', 'array'],
            'ids.*' => ['integer'],
        ]);
    }

    /**
     * Returns the students an action should apply to, or null when a group
     * ("all matching") action was requested without any filter (safety net).
     */
    private function resolveTargets(Request $request, bool $archived): ?Builder
    {
        $query = $archived ? Student::onlyArchived() : Student::query();

        if ($request->input('mode') === 'filtered') {
            return $this->hasFilter($request, $archived)
                ? $this->applyFilters($query, $request, $archived)
                : null;
        }

        return $query->whereIn('id', array_map('intval', (array) $request->input('ids', [])));
    }

    private function noFilterError()
    {
        return back()->with(
            'error',
            'Choose at least one filter (program, year level, block, or search) before using a group action.'
        );
    }

    private function backToList(Request $request, string $fallbackTab)
    {
        $tab = in_array($request->input('tab'), ['active', 'archived'], true)
            ? $request->input('tab')
            : $fallbackTab;

        $filters = array_filter(
            $request->only(self::FILTER_KEYS),
            fn ($v) => $v !== null && $v !== '' && $v !== []
        );

        return redirect()->route('admin.student-records.index', array_merge(['tab' => $tab], $filters));
    }

    private function collectFilePaths($students): array
    {
        $studentIds = $students->pluck('id')->all();
        $userIds    = $students->pluck('user_id')->all();

        return collect()
            ->merge(
                DB::table('requirements')->whereIn('student_id', $studentIds)
                    ->whereNotNull('file_path')->pluck('file_path')
            )
            ->merge(
                DB::table('lesson_plans')->whereIn('student_id', $studentIds)
                    ->whereNotNull('file_path')->pluck('file_path')
            )
            ->merge(
                DB::table('other_evaluator_results as r')
                    ->join('evaluations as e', 'e.id', '=', 'r.evaluation_id')
                    ->whereIn('e.student_id', $studentIds)
                    ->whereNotNull('r.grade_image_path')
                    ->pluck('r.grade_image_path')
            )
            ->merge($students->pluck('enrollment_form_path'))
            ->merge(
                DB::table('users')->whereIn('id', $userIds)
                    ->whereNotNull('profile_photo')->pluck('profile_photo')
            )
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function deleteFiles(array $paths): void
    {
        $dirs = [];

        foreach ($paths as $path) {
            foreach (self::DISKS as $disk) {
                try {
                    Storage::disk($disk)->delete($path);
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $dirs[dirname($path)] = true;
        }

        // Remove a folder only when it is now completely empty.
        foreach (array_keys($dirs) as $dir) {
            if ($dir === '.' || $dir === '') {
                continue;
            }

            foreach (self::DISKS as $disk) {
                try {
                    $storage = Storage::disk($disk);

                    if ($storage->exists($dir) && empty($storage->allFiles($dir))) {
                        $storage->deleteDirectory($dir);
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }
    }
}