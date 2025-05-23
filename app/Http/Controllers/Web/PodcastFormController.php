<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PodcastFormController extends Controller
{
    public function create()
    {
        return view('podcasts.submit_form'); //vista Blade
    }
}