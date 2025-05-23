<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PodcastFormController; 

Route::get('/podcast/submit', [PodcastFormController::class, 'create'])->name('podcast.form');