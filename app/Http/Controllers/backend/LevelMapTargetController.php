<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\LevelMapTarget;
use App\Models\Target;
use Illuminate\Http\Request;

class LevelMapTargetController extends Controller
{
    public function index()
    {
        $mappings = LevelMapTarget::with(['level', 'target'])->get();
        return view('level_target.index', compact('mappings'));
    }

    public function create()
    {
        $levels = Level::all();
        $targets = Target::all();
        return view('level_target.create', compact('levels', 'targets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_level' => 'required|exists:levels,id',
            'id_target' => 'required|exists:targets,id',
        ]);

        LevelMapTarget::create($data);
        return redirect()->route('level-targets.index')->with('success', 'Mapping created successfully');
    }

    public function destroy(LevelMapTarget $levelMapTarget)
    {
        $levelMapTarget->delete();
        return redirect()->route('level-targets.index')->with('success', 'Mapping deleted successfully');
    }
}
