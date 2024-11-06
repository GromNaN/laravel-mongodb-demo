<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MovieSearchController
{
    public function index(Request $request): View
    {
        if ($request->has('search')) {
            $movies = Movie::search($request->search)->paginate(20);
        } else {
            $movies = Movie::paginate(20);
        }

        return view('movies', ['movies' => $movies]);
    }
}
