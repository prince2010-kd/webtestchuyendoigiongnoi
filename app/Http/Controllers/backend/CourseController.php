<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Stage;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('stage')->get();
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $stages = Stage::all();
        return view('courses.create', compact('stages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_stage' => 'required|exists:stages,id',
            'code' => 'required|string',
            'title' => 'required|string',
            'des' => 'nullable|string',
            'active' => 'required|integer',
            'stt' => 'nullable|integer',
        ]);

        Course::create($data);
        return redirect()->route('courses.index')->with('success', 'Course created successfully');
    }

    public function edit(Course $course)
    {
        $stages = Stage::all();
        return view('courses.edit', compact('course', 'stages'));
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'id_stage' => 'required|exists:stages,id',
            'code' => 'required|string',
            'title' => 'required|string',
            'des' => 'nullable|string',
            'active' => 'required|integer',
            'stt' => 'nullable|integer',
        ]);

        $course->update($data);
        return redirect()->route('courses.index')->with('success', 'Course updated successfully');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Course deleted successfully');
    }
}
