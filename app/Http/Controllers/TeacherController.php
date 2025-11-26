<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    //
    public function availableManagers()
    {
        $managers = User::where('role', 'teacher')
            ->select('id', 'first_name', 'last_name', 'email')
            ->get();

        return response()->json([
            'data' => $managers
        ]);
    }
     /**
     * Get all teachers with filters
     */
    public function index(Request $request)
    {
        $query = Teacher::with(['user', 'school', 'courses']);

        // Filter by school
        if ($request->has('school_name')) {
            $query->where('school_name', $request->school_name);
        }

        // Filter by subject specialization
        if ($request->has('subject_specialization')) {
            $query->where('subject_specialization', $request->subject_specialization);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by gender
        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter by experience range
        if ($request->has('min_experience')) {
            $query->where('experience_years', '>=', $request->min_experience);
        }
        if ($request->has('max_experience')) {
            $query->where('experience_years', '<=', $request->max_experience);
        }

        // Search by name, staff number, or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('staff_number', 'like', "%{$search}%");
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $teachers = $query->paginate($perPage);

        return response()->json($teachers);
    }

    /**
     * Get teacher statistics
     */
    public function statistics(Request $request)
    {
        $query = Teacher::query();

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
            'by_subject' => (clone $query)->groupBy('subject_specialization')
                ->select('subject_specialization', DB::raw('count(*) as count'))
                ->pluck('count', 'subject_specialization'),
            'avg_experience' => (clone $query)->avg('experience_years'),
        ];

        return response()->json($stats);
    }
}
