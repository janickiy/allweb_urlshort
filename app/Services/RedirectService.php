<?php

namespace App\Services;

use App\DTO\RedirectResult;
use App\DTO\StatData;
use App\Enums\RedirectDecision;
use App\Models\Link;
use App\Repositories\DomainRepository;
use App\Repositories\LinkRepository;
use App\Repositories\StatRepository;
use GeoIp2\Database\Reader as GeoIP;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use WhichBrowser\Parser as UserAgent;

class RedirectService
{
    /**
     * Inject dependencies used by redirect resolution.
     */
    public function __construct(
        private readonly DomainRepository $domains,
        private readonly LinkRepository $links,
        private readonly StatRepository $stats,
    ) {
    }

    /**
     * Resolve a short-link alias into a redirect decision.
     *
     * @param Request $request
     * @param string $alias
     * @return RedirectResult
     */
    public function resolve(Request $request, string $alias): RedirectResult
    {
        $link = $this->findLink($request, $alias);

        if (!$link) {
            return new RedirectResult(RedirectDecision::NotFound, target: $this->notFoundRedirect($request));
        }

        if ($request->segments()[1] ?? null) {
            return new RedirectResult(RedirectDecision::Preview, $link);
        }

        if ($link->user_id === null) {
            return new RedirectResult(RedirectDecision::Redirect, $link, $link->url);
        }

        if ($link->ends_at && Carbon::now()->greaterThan($link->ends_at)) {
            return $link->expiration_url
                ? new RedirectResult(RedirectDecision::Redirect, $link, $link->expiration_url)
                : new RedirectResult(RedirectDecision::Expired, $link);
        }

        if ($link->password) {
            return new RedirectResult(RedirectDecision::Password, $link);
        }

        if ($link->disabled) {
            return new RedirectResult(RedirectDecision::Disabled, $link);
        }

        if ($this->containsBannedWord($link->url)) {
            return new RedirectResult(RedirectDecision::Banned, $link);
        }

        $userAgent = new UserAgent($this->headers($request));

        if ($userAgent->device->type === 'bot') {
            return new RedirectResult(RedirectDecision::Redirect, $link, $link->url);
        }

        $stat = $this->recordStat($request, $link, $userAgent);
        $this->links->incrementClicks($link);

        foreach ($link->platform_target ?? [] as $platform) {
            if ($stat->platform === $platform->key) {
                return new RedirectResult(RedirectDecision::Redirect, $link, $platform->value);
            }
        }

        foreach ($link->geo_target ?? [] as $geo) {
            if ($stat->country === $geo->key) {
                return new RedirectResult(RedirectDecision::Redirect, $link, $geo->value);
            }
        }

        return new RedirectResult(RedirectDecision::Redirect, $link, $link->url);
    }

    /**
     * Find the link matching the requested alias and host.
     *
     * @param Request $request
     * @param string $alias
     * @return Link|null
     */
    private function findLink(Request $request, string $alias): ?Link
    {
        $localHost = parse_url(config('app.url'), PHP_URL_HOST);
        $remoteHost = $request->getHost();

        if ($localHost !== $remoteHost) {
            $domain = $this->domains->findByHost($remoteHost);

            return $domain ? $this->links->findByAliasForDomain($alias, $domain->id) : null;
        }

        return $this->links->findByAliasForDomain($alias, null);
    }

    /**
     * Return the custom not-found redirect target for a host.
     */
    private function notFoundRedirect(Request $request): ?string
    {
        $localHost = parse_url(config('app.url'), PHP_URL_HOST);
        $remoteHost = $request->getHost();

        if ($localHost === $remoteHost) {
            return null;
        }

        return $this->domains->findByHost($remoteHost)?->not_found_page;
    }

    /**
     * Check whether a URL contains a banned word.
     */
    private function containsBannedWord(string $url): bool
    {
        $words = preg_split('/\n|\r/', config('settings.short_bad_words'), -1, PREG_SPLIT_NO_EMPTY);

        foreach ($words as $word) {
            if (strpos($url, $word) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Record a click statistic for a link.
     *
     * @param Request $request
     * @param Link $link
     * @param UserAgent $userAgent
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function recordStat(Request $request, Link $link, UserAgent $userAgent)
    {
        $referrer = $request->headers->get('referer');

        return $this->stats->createFromDto(StatData::fromArray([
            'link_id' => $link->id,
            'user_id' => $link->user_id,
            'referrer' => $referrer ? (parse_url($referrer, PHP_URL_HOST) ?: null) : null,
            'platform' => $userAgent->os->name ?? null,
            'browser' => $userAgent->browser->name ?? null,
            'device' => $userAgent->device->type ?? null,
            'country' => $this->country($request),
            'language' => $request->server('HTTP_ACCEPT_LANGUAGE') ? substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2) : null,
        ]));
    }

    /**
     * Resolve the visitor country code from request data.
     */
    private function country(Request $request): ?string
    {
        $path = storage_path('app/geoip/GeoLite2-Country.mmdb');

        if (!file_exists($path)) {
            return null;
        }

        try {
            return (new GeoIP($path))->country($request->ip())->country->isoCode;
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Extract custom GeoIP headers from the request.
     *
     * @return array<string, string>
     */
    private function headers(Request $request): array
    {
        $headers = [];

        foreach ($request->headers->all() as $name => $values) {
            $headers[$name] = implode(', ', $values);
        }

        return $headers;
    }
}
