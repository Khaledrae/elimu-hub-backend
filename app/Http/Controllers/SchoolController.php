<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        return response()->json(School::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
        ]);

        $school = School::create($validated);
        return response()->json($school, 201);
    }
    public function show(School $school)
    {
        return response()->json(['data' => $school->load(['county', 'admins.user'])]);
    }
    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
        ]);

        $school->update($validated);
        return response()->json($school);
    }

    public function destroy(School $school)
    {
        $school->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
