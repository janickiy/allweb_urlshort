<?php

namespace App\Http\Controllers;

use App\Repositories\PageRepository;

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
     */
    public function index(mixed $url): mixed
    {
        return view('page.page', ['page' => $this->pages->findBySlugOrFail($url)]);
    }
}
