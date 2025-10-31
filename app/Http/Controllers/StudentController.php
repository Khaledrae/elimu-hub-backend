<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    //
    public function __construct()
    {
        // JWT auth guard
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the students.
     */
    public function index()
    {
        // Only admins or teachers can access
        $user = auth('api')->user();
        if (!in_array($user->role, ['admin', 'teacher'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $students = Student::with(['user', 'guardian'])->latest()->get();
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
}
