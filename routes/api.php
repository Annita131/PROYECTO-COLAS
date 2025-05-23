<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PodcastController; // Importa el controlador

Route::post('/podcasts', [PodcastController::class, 'submit']);

// Esta ruta es útil para verificar que la API responde
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); // Si usas Sanctum, si no, puedes quitar el middleware por ahora.