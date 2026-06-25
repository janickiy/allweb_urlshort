<?php

namespace App\Services;

use App\Repositories\DomainRepository;
use App\Repositories\LinkRepository;
use App\Repositories\PlanRepository;
use App\Repositories\StatRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class HomeService
{
    /**
     * Inject dependencies used by public home page operations.
     */
    public function __construct(
        private readonly DomainRepository $domains,
        private readonly LinkRepository $links,
        private readonly PlanRepository $plans,
        private readonly StatRepository $stats,
        private readonly UserRepository $users,
    ) {
    }

    /**
     * Resolve the redirect target for a custom domain index page.
     */
    public function domainIndexRedirect(Request $request): ?string
    {
        $localHost = parse_url(config('app.url'), PHP_URL_HOST);
        $remoteHost = $request->getHost();

        if ($localHost === $remoteHost) {
            return null;
        }

        $domain = $this->domains->findByHost($remoteHost);

        return $domain?->index_page;
    }

    /**
     * Resolve whether the home route should redirect before rendering.
     *
     * @return array{url: string, status: int}|null
     */
    public function landingRedirect(Request $request, bool $authenticated): ?array
    {
        if ($authenticated) {
            return ['url' => route('dashboard'), 'status' => 302];
        }

        $configuredIndex = config('settings.index');

        if (is_string($configuredIndex) && $configuredIndex !== '') {
            return ['url' => $configuredIndex, 'status' => 301];
        }

        if ($redirect = $this->domainIndexRedirect($request)) {
            return ['url' => $redirect, 'status' => 301];
        }

        return null;
    }

    /**
     * Build data required by the public landing page.
     *
     * @return array{plans: mixed, stats: array<string, mixed>}
     */
    public function landingData(): array
    {
        return [
            'plans' => config('settings.stripe') ? $this->plans->visible() : null,
            'stats' => [
                'links' => $this->links->maxId(),
                'redirects' => $this->stats->maxId(),
                'users' => $this->users->maxId(),
            ],
        ];
    }
}
