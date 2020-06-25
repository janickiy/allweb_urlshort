<?php

namespace App\Http\Controllers;

use App\Link;
use Illuminate\Http\Request;

class QRController extends Controller
{
    public function index(Request $request, $id)
    {
        $link = Link::findOrFail($id);

        return view('qr.content', ['link' => $link]);
    }
}
