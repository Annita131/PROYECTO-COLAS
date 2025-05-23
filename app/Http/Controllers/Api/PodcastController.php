<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ProcessPodcast; 
use Illuminate\Support\Facades\Validator; 

class PodcastController extends Controller
{
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $podcastData = $request->only(['title', 'url']);

        // Despachar el job a la cola
        ProcessPodcast::dispatch($podcastData);

        return response()->json([
            'message' => 'Podcast recibido y encolado para procesamiento.',
            'data' => $podcastData
        ], 202); // 202 Accepted es un buen código para tareas encoladas
    }
}
