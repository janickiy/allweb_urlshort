<?php

namespace App\Http\Controllers;

use App\Repositories\LinkRepository;
use Illuminate\Http\Request;

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
    public function index(Request $request, mixed $id): mixed
    {
        return view('qr.content', ['link' => $this->links->findOrFail($id)]);
    }
}
