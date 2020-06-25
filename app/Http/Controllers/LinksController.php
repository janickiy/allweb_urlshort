<?php

namespace App\Http\Controllers;

use App\Domain;
use App\Http\Requests\{CreateLinkRequest,UpdateLinkRequest};
use App\Link;
use App\Space;
use App\Traits\LinkTrait;
use App\Traits\UserFeaturesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LinksController extends Controller
{
    use LinkTrait, UserFeaturesTrait;

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get the user's spaces
        $spaces = Space::where('user_id', $user->id)->get();

        // Get the user's domains
        $domains = Domain::where('user_id', $user->id)->get();

        $search = $request->input('search');
        $space = $request->input('space');
        $domain = $request->input('domain');
        $type = $request->input('type');
        $by = $request->input('by');

        if ($request->input('sort') == 'min') {
            $sort = ['clicks', 'asc'];
        } elseif ($request->input('sort') == 'max') {
            $sort = ['clicks', 'desc'];
        } elseif ($request->input('sort') == 'asc') {
            $sort = ['id', 'asc'];
        } else {
            $sort = ['id', 'desc'];
        }

        // If there's no toast notification
        if (session('toast') == false) {
            // Set the session to a countable object
            session(['toast' => []]);
        }

        $links = Link::where('user_id', $user->id)
            ->when($domain, function($query) use ($domain) {
                return $query->searchDomain($domain);
            })
            ->when($space, function($query) use ($space) {
                return $query->searchSpace($space);
            })
            ->when($type, function($query) use ($type) {
                if($type == 1) {
                    return $query->searchActive();
                } else {
                    return $query->searchExpired();
                }
            })
            ->when($search, function($query) use ($search, $by) {
                if($by == 'url') {
                    return $query->searchUrl($search);

                } elseif ($by == 'alias') {
                    return $query->searchAlias($search);
                }
                return $query->searchTitle($search);
            })
            ->orderBy($sort[0], $sort[1])
            ->paginate(10)
            ->appends(['search' => $search, 'domain' => $domain, 'space' => $space, 'by' => $by, 'sort' => $request->input('sort')]);

        return view('links.content', ['view' => 'list', 'links' => $links, 'spaces' => $spaces, 'domains' => $domains]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function linksEdit($id)
    {
        $user = Auth::user();

        // Get the user's spaces
        $spaces = Space::where('user_id', $user->id)->get();

        // Get the user's domains
        $domains = Domain::where('user_id', $user->id)->get();

        $link = Link::where([['id', '=', $id], ['user_id', '=', $user->id]])->firstOrFail();

        return view('links.content', ['view' => 'edit', 'domains' => $domains, 'spaces' => $spaces, 'link' => $link]);
    }

    /**
     * @param CreateLinkRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createLink(CreateLinkRequest $request)
    {
        $user = Auth::user();

        if ($request->multi_link) {

            $links = $this->linksCreate($request);

            return redirect()->back()->with('toast', Link::where('user_id', '=', $user->id)->orderBy('id', 'desc')->limit(count($links))->get());
        } else {
            $this->linkCreate($request);

            return redirect()->back()->with('toast', Link::where('user_id', '=', $user->id)->orderBy('id', 'desc')->limit(1)->get());
        }
    }

    /**
     * @param UpdateLinkRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateLink(UpdateLinkRequest $request, $id)
    {
        $user = Auth::user();

        $link = Link::where([['id', '=', $id], ['user_id', '=', $user->id]])->firstOrFail();

        $this->linkUpdate($request, $link);

        return redirect()->route('links.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function deleteLink(Request $request, $id)
    {
        $user = Auth::user();

        $link = Link::where([['id', '=', $id], ['user_id', '=', $user->id]])->firstOrFail();

        $link->delete();

        return redirect()->route('links')->with('success', __(':name has been deleted.', ['name' => str_replace(['http://', 'https://'], '', (isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias)))]));
    }
}
