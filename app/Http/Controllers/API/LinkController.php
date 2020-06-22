<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\CreateLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Http\Resources\LinkCollectionResource;
use App\Http\Resources\LinkResource;
use App\Link;
use App\Traits\LinkTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    use LinkTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $user = Auth::user();

        return LinkCollectionResource::collection(Link::where('user_id', $user->id)->orderBy('id', 'desc')->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateLinkRequest $request
     * @return LinkResource
     */
    public function store(CreateLinkRequest $request)
    {
        if (!$request->input('multi_link')) {
            $created = $this->linkCreate($request);

            if ($created) {
                return LinkResource::make($created);
            }
        }

        return response()->json([
            'message' => 'Resource not found.',
            'status' => 404
        ], 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return LinkResource
     */
    public function show($id)
    {
        $user = Auth::user();

        $link = Link::where([['id', '=', $id], ['user_id', $user->id]])->first();

        if ($link) {
            return LinkResource::make($link);
        }

        return response()->json([
            'message' => 'Resource not found.',
            'status' => 404
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLinkRequest $request
     * @param $id
     * @return LinkResource
     */
    public function update(UpdateLinkRequest $request, $id)
    {
        $user = Auth::user();

        $link = Link::where([['id', '=', $id], ['user_id', '=', $user->id]])->firstOrFail();

        $updated = $this->linkUpdate($request, $link);

        if ($updated) {
            return LinkResource::make($updated);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $link = Link::where([['id', '=', $id], ['user_id', '=', $user->id]])->first();

        if ($link) {
            $link->delete();

            return response()->json([
                'id' => $link->id,
                'object' => 'link',
                'deleted' => true,
                'status' => 200
            ], 200);
        }

        return response()->json([
            'message' => 'Resource not found.',
            'status' => 404
        ], 404);
    }
}
