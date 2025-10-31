<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentStoreRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\StudentService;
use App\Services\TeacherService;
use App\Http\Requests\TeacherStoreRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    protected $teacherService;
    protected $studentService;

    public function __construct(StudentService $studentService, TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
        $this->studentService = $studentService;
    }

    public function register(Request $request)
    {
        $role = $request->input('role', 'student');
        $isStudent = $role === 'student';

        // Shared base validation (for all users)
        $baseValidator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|string|in:student,teacher,admin',
        ]);

        if ($baseValidator->fails()) {
            return response()->json($baseValidator->errors(), 422);
        }

        // ✅ If student, validate extra fields via StudentStoreRequest
        if ($isStudent) {
            $studentRequest = new StudentStoreRequest();
            $studentRequest->merge($request->all());
            /*
            $studentValidator = Validator::make(
                $studentRequest->all(),
                $studentRequest->rules()
            );

            if ($studentValidator->fails()) {
                return response()->json($studentValidator->errors(), 422);
            }
*/
            // Pass all data to the service
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
        } else {
            // ✅ Default user (teacher/admin)
            $data = $baseValidator->validated();
            $data['password'] = bcrypt($data['password']);
            $data['status'] = 'active';

            $user = User::create($data);
        }

        $token = auth('api')->login($user);

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user->load(['student', 'teacher'])),
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}
