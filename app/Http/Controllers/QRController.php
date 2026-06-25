<?php

namespace App\Http\Controllers;

use App\Repositories\LinkRepository;
use Illuminate\View\View;

class QRController extends Controller
{
    /**
     * Inject link repository used to resolve QR targets.
     */
    public function __construct(private readonly LinkRepository $links)
    {
    }

    /**
     * Generate a QR code response for a link owned by the user.
     */
    public function index(int|string $id): View
    {
        return view('qr.content', ['link' => $this->links->findOrFail($id)]);
    }
}
