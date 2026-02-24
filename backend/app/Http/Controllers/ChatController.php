<?php

namespace App\Http\Controllers;

use App\Services\RAGService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $ragService;

    public function __construct(RAGService $ragService)
    {
        $this->ragService = $ragService;
    }

    public function chat(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:1000'
        ]);

        $response = $this->ragService->answer(
            $request->input('query'),
            $request->user()->id
        );

        return response()->json([
            'response' => $response
        ]);
    }
}
