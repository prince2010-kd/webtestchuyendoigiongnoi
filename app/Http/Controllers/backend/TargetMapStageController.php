<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Stage;
use App\Models\Target;
use App\Models\TargetMapStage;
use Illuminate\Http\Request;

class TargetMapStageController extends Controller
{
    public function index()
    {
        $mappings = TargetMapStage::with(['stage', 'target'])->get();
        return view('target_stage.index', compact('mappings'));
    }

    public function create()
    {
        $stages = Stage::all();
        $targets = Target::all();
        return view('target_stage.create', compact('stages', 'targets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_stage' => 'required|exists:stages,id',
            'id_target' => 'required|exists:targets,id',
        ]);

        TargetMapStage::create($data);
        return redirect()->route('target-stages.index')->with('success', 'Mapping created successfully');
    }

    public function destroy(TargetMapStage $targetMapStage)
    {
        $targetMapStage->delete();
        return redirect()->route('target-stages.index')->with('success', 'Mapping deleted successfully');
    }
}
