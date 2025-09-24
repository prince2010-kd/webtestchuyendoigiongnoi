<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $acceptedExtensions = ['gif', 'jpg', 'jpeg', 'png'];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());

            if (!in_array($extension, $acceptedExtensions)) {
                return response()->json(['error' => 'Invalid extension.'], 400);
            }

            $filename = uniqid() . '.' . $extension;
            $path = $file->storeAs('uploads', $filename, 'public');

            return response()->json([
                'filename' => $filename  // TinyMCE expects { location: 'http://...' }
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
