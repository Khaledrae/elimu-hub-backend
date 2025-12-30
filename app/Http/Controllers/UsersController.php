<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStoreRequest;
use App\Http\Requests\StudentStoreRequest;
use App\Http\Requests\TeacherStoreRequest;
use App\Models\User;
use App\Services\AdminService;
use App\Services\StudentService;
use App\Services\TeacherService;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    protected $adminService;
    protected $teacherService;
    protected $studentService;

    public function __construct(
        StudentService $studentService,
        TeacherService $teacherService,
        AdminService $adminService
    ) {
        $this->adminService = $adminService;
        $this->teacherService = $teacherService;
        $this->studentService = $studentService;
    }

    /**
     * Get all users with optional filtering
     */
    public function index(Request $request)
    {
//         logger([
//     'user_id' => auth()->id(),
//     'role' => auth()->user()->role,
// ]);
        // Only allow admin users to access this
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = User::with(['student', 'teacher', 'admin']);

        // Filter by role if provided
        if ($request->has('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'users' => UserResource::collection($users)
        ]);
    }

    /**
     * Get a specific user
     */
    public function show($id)
    {
        // Users can view their own profile, admins can view any
        if (auth()->user()->role !== 'admin' && auth()->user()->id != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = User::with(['student', 'teacher', 'admin'])->find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'user' => new UserResource($user)
        ]);
    }

    /**
     * Create a new user (admin only)
     */
    public function store(Request $request)
    {
        // Only admin can create users
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $role = $request->input('role', 'student');

        // Shared base validation (for all users)
        $baseValidator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'county' => 'nullable|integer|exists:counties,id',
            'phone' => [
                'nullable',
                'string',
                'min:10',
                'max:12',
                'regex:/^\d+$/',
                'unique:users'
            ],
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:student,teacher,admin,parent',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        if ($baseValidator->fails()) {
            return response()->json($baseValidator->errors(), 422);
        }

        try {
            // Create user based on role
            if ($role === 'student') {
                $studentRequest = new StudentStoreRequest();
                $studentRequest->merge($request->all());

                $user = $this->studentService->registerStudent($request->all());
            } elseif ($role === 'teacher') {
                $teacherRequest = new TeacherStoreRequest();
                $teacherRequest->merge($request->all());

                $teacherValidator = Validator::make(
                    $teacherRequest->all(),
                    $teacherRequest->rules()
                );

                if ($teacherValidator->fails()) {
                    return response()->json($teacherValidator->errors(), 422);
                }

                $user = $this->teacherService->registerTeacher($request->all());
            } elseif ($role === 'admin') {
                $adminRequest = new AdminStoreRequest();
                $adminRequest->merge($request->all());

                $adminValidator = Validator::make(
                    $adminRequest->all(),
                    $adminRequest->rules()
                );

                if ($adminValidator->fails()) {
                    return response()->json($adminValidator->errors(), 422);
                }

                $user = $this->adminService->registerAdmin($request->all());
            } else {
                // For parent or other basic roles
                $data = $baseValidator->validated();
                $data['password'] = bcrypt($data['password']);
                $data['status'] = $request->input('status', 'active');

                $user = User::create($data);
            }

            return response()->json([
                'message' => 'User created successfully',
                'user' => new UserResource($user->load(['student', 'teacher', 'admin']))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a user
     */
    public function update(Request $request, $id)
    {
        // Users can update their own profile, admins can update any
        if (auth()->user()->role !== 'admin' && auth()->user()->id != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Base validation rules
        $validationRules = [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name'  => 'sometimes|required|string|max:255',
            'county' => 'nullable|integer|exists:counties,id',
            'phone' => [
                'nullable',
                'string',
                'min:10',
                'max:12',
                'regex:/^\d+$/',
                'unique:users,phone,' . $id
            ],
            'email' => 'sometimes|required|string|email|unique:users,email,' . $id,
            'password' => 'sometimes|nullable|string|min:6|confirmed',
            'status' => 'sometimes|string|in:active,inactive',
        ];

        // Only admin can change role
        if (auth()->user()->role === 'admin') {
            $validationRules['role'] = 'sometimes|string|in:student,teacher,admin,parent';
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $data = $validator->validated();

            // Remove password confirmation from data
            unset($data['password_confirmation']);

            // Hash password if provided
            if (isset($data['password']) && $data['password']) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }

            // Update user
            $user->update($data);

            // If role-specific services have update methods, call them here
            // For now, we'll just update the base user

            return response()->json([
                'message' => 'User updated successfully',
                'user' => new UserResource($user->load(['student', 'teacher', 'admin']))
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a user (admin only)
     */
    public function destroy($id)
    {
        // Only admin can delete users
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Prevent users from deleting themselves
        if (auth()->user()->id == $id) {
            return response()->json(['error' => 'You cannot delete your own account'], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        try {
            $user->delete();

            return response()->json([
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate/Deactivate user (admin only)
     */
    public function updateStatus(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update(['status' => $request->status]);

        return response()->json([
            'message' => 'User status updated successfully',
            'user' => new UserResource($user->load(['student', 'teacher', 'admin']))
        ]);
    }
}
