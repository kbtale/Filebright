<?php

namespace App\Http\Controllers;

use App\Models\DocumentMetadata;
use App\Jobs\ProcessDocument;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,txt|max:102400',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'private');

        $document = DocumentMetadata::create([
            'user_id' => $request->user()->id,
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'path' => $path,
            'status' => 'pending',
        ]);

        ProcessDocument::dispatch($document);

        return response()->json([
            'message' => 'File uploaded successfully',
            'document' => $document
        ], 201);
    }
}
