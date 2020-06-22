<?php


namespace App\Traits;

use App\Domain;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait DomainTrait
{
    /**
     * Store a new domain
     *
     * @param Request $request
     * @return Domain
     */
    protected function domainCreate(Request $request)
    {
        $user = Auth::user();

        $domain = new Domain;

        $domain->name = str_replace('https://', '', $request->input('name'));
        $domain->user_id = $user->id;
        $domain->index_page = $request->input('index_page');
        $domain->not_found_page = $request->input('not_found_page');
        $domain->save();

        return $domain;
    }

    /**
     * Update the domain
     *
     * @param Request $request
     * @param Model $domain
     * @return Domain|Model
     */
    protected function domainUpdate(Request $request, Model $domain)
    {
        $domain->index_page = $request->input('index_page');
        $domain->not_found_page = $request->input('not_found_page');
        $domain->save();

        return $domain;
    }
}