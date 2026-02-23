<?php

namespace App\Http\Controllers;

use App\Models\DocumentMetadata;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,txt|max:102400', // 100MB limit
        ]);

        $file = $request->file('file');
        
        // Store in storage/app/private/documents
        $path = $file->store('documents', 'private');

        $document = DocumentMetadata::create([
            'user_id' => $request->user()->id,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'document' => $document
        ], 201);
    }
}
