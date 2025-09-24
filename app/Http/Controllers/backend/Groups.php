<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Groups;

class GroupsController extends Controller
{
    public function index(){
        $allList = Groups::all();
        return view('groups.index', compact('allList'));
    }
}
