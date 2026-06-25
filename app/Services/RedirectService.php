<?php

namespace App\Services;

use App\DTO\StatData;
use App\Models\Link;
use App\Models\Stat;
use App\Repositories\DomainRepository;
use App\Repositories\LinkRepository;
use App\Repositories\StatRepository;
use GeoIp2\Database\Reader as GeoIP;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use WhichBrowser\Parser as UserAgent;

class RedirectService
{
    public function __construct(
        private readonly DomainRepository $domains,
        private readonly LinkRepository $links,
        private readonly StatRepository $stats,
    ) {
    }

    public function resolve(Request $request, string $alias): RedirectDecision
    {
        $link = $this->findLink($request, $alias);

        if (!$link) {
            return new RedirectDecision(RedirectDecision::TYPE_NOT_FOUND, target: $this->notFoundRedirect($request));
        }

        if ($request->segments()[1] ?? null) {
            return new RedirectDecision(RedirectDecision::TYPE_PREVIEW, $link);
        }

        if ((int) $link->user_id === 0) {
            return new RedirectDecision(RedirectDecision::TYPE_REDIRECT, $link, $link->url);
        }

        if ($link->ends_at && Carbon::now()->greaterThan($link->ends_at)) {
            return $link->expiration_url
                ? new RedirectDecision(RedirectDecision::TYPE_REDIRECT, $link, $link->expiration_url)
                : new RedirectDecision(RedirectDecision::TYPE_EXPIRED, $link);
        }

        if ($link->password) {
            return new RedirectDecision(RedirectDecision::TYPE_PASSWORD, $link);
        }

        if ($link->disabled) {
            return new RedirectDecision(RedirectDecision::TYPE_DISABLED, $link);
        }

        if ($this->containsBannedWord($link->url)) {
            return new RedirectDecision(RedirectDecision::TYPE_BANNED, $link);
        }

        $userAgent = new UserAgent($this->headers($request));

        if ($userAgent->device->type === 'bot') {
            return new RedirectDecision(RedirectDecision::TYPE_REDIRECT, $link, $link->url);
        }

        $stat = $this->recordStat($request, $link, $userAgent);
        $this->links->incrementClicks($link);

        foreach ($link->platform_target ?? [] as $platform) {
            if ($stat->platform === $platform->key) {
                return new RedirectDecision(RedirectDecision::TYPE_REDIRECT, $link, $platform->value);
            }
        }

        foreach ($link->geo_target ?? [] as $geo) {
            if ($stat->country === $geo->key) {
                return new RedirectDecision(RedirectDecision::TYPE_REDIRECT, $link, $geo->value);
            }
        }

        return new RedirectDecision(RedirectDecision::TYPE_REDIRECT, $link, $link->url);
    }

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

    private function notFoundRedirect(Request $request): ?string
    {
        $localHost = parse_url(config('app.url'), PHP_URL_HOST);
        $remoteHost = $request->getHost();

        if ($localHost === $remoteHost) {
            return null;
        }

        return $this->domains->findByHost($remoteHost)?->not_found_page;
    }

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

    private function recordStat(Request $request, Link $link, UserAgent $userAgent): Stat
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
