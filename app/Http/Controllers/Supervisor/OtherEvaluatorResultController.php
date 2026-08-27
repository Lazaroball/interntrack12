<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\OtherEvaluatorResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OtherEvaluatorResultController extends Controller
{
    /**
     * Directory (within the "public" disk) where grade images are stored.
     */
    protected string $imageDirectory = 'other_evaluator_results';

    /**
     * Display a listing of other evaluator results for a given evaluation.
     */
    public function index(Evaluation $evaluation): JsonResponse
    {
        $this->authorizeEvaluationOwner($evaluation);

        $results = $evaluation->otherEvaluatorResults()
            ->latest()
            ->get()
            ->map(function (OtherEvaluatorResult $result) {
                $result->grade_image_url = $result->grade_image_url;
                return $result;
            });

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * Show a single other evaluator result.
     */
    public function show(OtherEvaluatorResult $otherEvaluatorResult): JsonResponse
    {
        $this->authorizeEvaluationOwner($otherEvaluatorResult->evaluation);

        $otherEvaluatorResult->grade_image_url = $otherEvaluatorResult->grade_image_url;

        return response()->json([
            'success' => true,
            'data' => $otherEvaluatorResult,
        ]);
    }

    /**
     * Store a newly created evaluator result (with optional image upload).
     */
    public function store(Request $request, Evaluation $evaluation): JsonResponse
    {
        $this->authorizeEvaluationOwner($evaluation);

        $validator = Validator::make($request->all(), [
            'evaluator_name' => ['required', 'string', 'max:255'],
            'final_grade'    => ['required', 'numeric', 'min:0', 'max:999.99'],
            'remarks'        => ['nullable', 'string'],
            'grade_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        DB::beginTransaction();

        $imagePath = null;

        try {
            if ($request->hasFile('grade_image')) {
                $imagePath = $request->file('grade_image')->store($this->imageDirectory, 'public');
            }

            $result = OtherEvaluatorResult::create([
                'evaluation_id'     => $evaluation->id,
                'evaluator_name'    => $validated['evaluator_name'],
                'final_grade'       => $validated['final_grade'],
                'remarks'           => $validated['remarks'] ?? null,
                'grade_image_path'  => $imagePath,
            ]);

            DB::commit();

            $result->grade_image_url = $result->grade_image_url;

            return response()->json([
                'success' => true,
                'message' => 'Evaluator result added successfully.',
                'data'    => $result,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Clean up uploaded file if something went wrong after upload
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            Log::error('Failed to store other evaluator result: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while saving the evaluator result.',
            ], 500);
        }
    }

    /**
     * Update an existing evaluator result (with optional image replacement).
     */
    public function update(Request $request, OtherEvaluatorResult $otherEvaluatorResult): JsonResponse
    {
        $this->authorizeEvaluationOwner($otherEvaluatorResult->evaluation);

        $validator = Validator::make($request->all(), [
            'evaluator_name' => ['sometimes', 'required', 'string', 'max:255'],
            'final_grade'    => ['sometimes', 'required', 'numeric', 'min:0', 'max:999.99'],
            'remarks'        => ['nullable', 'string'],
            'grade_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_image'   => ['nullable', 'boolean'], // allow explicit removal without replacement
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        DB::beginTransaction();

        $oldImagePath = $otherEvaluatorResult->grade_image_path;
        $newImagePath = $oldImagePath;

        try {
            if ($request->hasFile('grade_image')) {
                // Store new image first
                $newImagePath = $request->file('grade_image')->store($this->imageDirectory, 'public');
            } elseif ($request->boolean('remove_image')) {
                $newImagePath = null;
            }

            $otherEvaluatorResult->fill([
                'evaluator_name'   => $validated['evaluator_name'] ?? $otherEvaluatorResult->evaluator_name,
                'final_grade'      => $validated['final_grade'] ?? $otherEvaluatorResult->final_grade,
                'remarks'          => array_key_exists('remarks', $validated) ? $validated['remarks'] : $otherEvaluatorResult->remarks,
                'grade_image_path' => $newImagePath,
            ]);

            $otherEvaluatorResult->save();

            // Delete old image only after successful save, and only if it changed
            if ($oldImagePath && $oldImagePath !== $newImagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }

            DB::commit();

            $otherEvaluatorResult->grade_image_url = $otherEvaluatorResult->grade_image_url;

            return response()->json([
                'success' => true,
                'message' => 'Evaluator result updated successfully.',
                'data'    => $otherEvaluatorResult,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Clean up newly uploaded file if update failed
            if ($newImagePath && $newImagePath !== $oldImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }

            Log::error('Failed to update other evaluator result: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while updating the evaluator result.',
            ], 500);
        }
    }

    /**
     * Remove the specified evaluator result and its associated image.
     */
    public function destroy(OtherEvaluatorResult $otherEvaluatorResult): JsonResponse
    {
        $this->authorizeEvaluationOwner($otherEvaluatorResult->evaluation);

        DB::beginTransaction();

        try {
            $imagePath = $otherEvaluatorResult->grade_image_path;

            $otherEvaluatorResult->delete();

            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Evaluator result deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Failed to delete other evaluator result: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while deleting the evaluator result.',
            ], 500);
        }
    }

    /**
     * Ensure the authenticated user is the supervisor who owns this evaluation.
     * Aborts with 403 if not.
     */
    protected function authorizeEvaluationOwner(Evaluation $evaluation): void
    {
        if ($evaluation->supervisor_id !== auth()->id()) {
            abort(403, 'You are not authorized to manage results for this evaluation.');
        }
    }
}