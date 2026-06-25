<?php

namespace App\Http\Controllers;

use App\Repositories\PageRepository;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Inject page repository used to load static pages.
     */
    public function __construct(private readonly PageRepository $pages)
    {
    }

    /**
     * Display a public static page by slug.
     *
     * @param string $url
     * @return View
     */
    public function index(string $url): View
    {
        return view('page.page', ['page' => $this->pages->findBySlugOrFail($url)]);
    }
}
