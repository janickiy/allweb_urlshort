<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DevelopersController extends Controller
{
    /**
     * Display the developer documentation page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('developers.index');
    }
}
