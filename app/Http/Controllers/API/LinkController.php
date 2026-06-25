<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LinksController\CreateLinkRequest;
use App\Http\Requests\LinksController\UpdateLinkRequest;
use App\Http\Resources\LinkCollectionResource;
use App\Http\Resources\LinkResource;
use App\Models\User;
use App\Repositories\LinkRepository;
use App\Services\LinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LinkController extends Controller
{
    /**
     * Inject link repository and service dependencies for API actions.
     */
    public function __construct(
        private readonly LinkRepository $links,
        private readonly LinkService $linkService,
    ) {
    }

    /**
     * Return the authenticated user paginated links as API resources.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return LinkCollectionResource::collection(
            $this->links->paginateLatestForUser($this->user($request)->id)
        );
    }

    /**
     * Create a single link for the authenticated API user.
     */
    public function store(CreateLinkRequest $request): LinkResource|JsonResponse
    {
        if ($request->boolean('multi_link')) {
            return $this->notFoundResponse();
        }

        return LinkResource::make(
            $this->linkService->create($request->validated(), $this->user($request))
        );
    }

    /**
     * Return one link owned by the authenticated API user.
     */
    public function show(Request $request, mixed $id): LinkResource|JsonResponse
    {
        $link = $this->links->findForUser($id, $this->user($request)->id);

        if ($link) {
            return LinkResource::make($link);
        }

        return $this->notFoundResponse();
    }

    /**
     * Update one link owned by the authenticated API user.
     */
    public function update(UpdateLinkRequest $request, mixed $id): LinkResource
    {
        $link = $this->links->findForUserOrFail($id, $this->user($request)->id);

        return LinkResource::make(
            $this->linkService->update($link, $request->validated())
        );
    }

    /**
     * Delete one link owned by the authenticated API user.
     */
    public function destroy(Request $request, mixed $id): JsonResponse
    {
        $link = $this->links->findForUser($id, $this->user($request)->id);

        if (!$link) {
            return $this->notFoundResponse();
        }

        $this->linkService->delete($link);

        return response()->json([
            'id' => $link->id,
            'object' => 'link',
            'deleted' => true,
            'status' => 200,
        ]);
    }

    /**
     * Resolve the authenticated API user from the current request.
     */
    private function user(Request $request): User
    {
        return $request->user();
    }

    /**
     * Build the standard JSON response for missing API resources.
     */
    private function notFoundResponse(): JsonResponse
    {
        return response()->json([
            'message' => 'Resource not found.',
            'status' => 404,
        ], 404);
    }
}
