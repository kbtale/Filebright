<?php

namespace App\Http\Controllers;

use App\Models\DocumentMetadata;
use App\Jobs\ProcessDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $documents = DocumentMetadata::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($documents);
    }

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

    public function destroy(Request $request, $id)
    {
        $document = DocumentMetadata::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        // Delete the stored file from disk
        if ($document->path && Storage::disk('private')->exists($document->path)) {
            Storage::disk('private')->delete($document->path);
        }

        $document->delete();

        return response()->json(['message' => 'Document deleted successfully']);
    }

    public function rename(Request $request, $id)
    {
        $request->validate([
            'filename' => 'required|string|max:255',
        ]);

        $document = DocumentMetadata::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        $document->filename = $request->input('filename');
        $document->save();

        return response()->json([
            'message' => 'Document renamed successfully',
            'document' => $document
        ]);
    }
}
