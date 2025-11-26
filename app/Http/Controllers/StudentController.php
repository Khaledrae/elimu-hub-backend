<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
   
     /**
     * Get all students with filters
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'guardian', 'class']);

        // Filter by school
        if ($request->has('school_name')) {
            $query->where('school_name', $request->school_name);
        }

        // Filter by grade level
        if ($request->has('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by gender
        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }

        // Search by name, admission number, or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('admission_number', 'like', "%{$search}%");
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $students = $query->paginate($perPage);

        return StudentResource::collection($students);
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'grade_level' => 'nullable|string|max:50',
            'school_name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'guardian_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $student = Student::create($validator->validated());
        return new StudentResource($student->load(['user', 'guardian']));
    }

    /**
     * Display the specified student.
     */
    public function show($id)
    {
        $student = Student::with(['user', 'guardian'])->find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }
        return new StudentResource($student);
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'grade_level' => 'nullable|string|max:50',
            'school_name' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'guardian_id' => 'nullable|exists:users,id',
            'status' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $student->update($validator->validated());
        return new StudentResource($student->load(['user', 'guardian']));
    }

    /**
     * Remove the specified student.
     */
    public function destroy($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $user = auth('api')->user();
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $student->delete();
        return response()->json(['message' => 'Student deleted successfully']);
    }
     /**
     * Get student statistics
     */
    public function statistics(Request $request)
    {
        $query = Student::query();

        if ($request->has('school_name')) {
            $query->where('school_name', $request->school_name);
        }

        $stats = [
            'total' => $query->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'inactive' => (clone $query)->where('status', 'inactive')->count(),
            'by_gender' => (clone $query)->groupBy('gender')
                ->select('gender', DB::raw('count(*) as count'))
                ->pluck('count', 'gender'),
            'by_grade' => (clone $query)->groupBy('grade_level')
                ->select('grade_level', DB::raw('count(*) as count'))
                ->pluck('count', 'grade_level'),
        ];

        return response()->json($stats);
    }
}
