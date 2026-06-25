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
    public function __construct(
        private readonly DomainRepository $domains,
        private readonly LinkRepository $links,
        private readonly PlanRepository $plans,
        private readonly StatRepository $stats,
        private readonly UserRepository $users,
    ) {
    }

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
     * @return array{plans: mixed, stats: array<string, mixed>}
     */
    public function landingData(): array
    {
        return [
            'plans' => config('settings.stripe') ? $this->plans->visible() : null,
            'stats' => [
                'links' => $this->links->query()->max('id'),
                'redirects' => $this->stats->query()->max('id'),
                'users' => $this->users->query()->max('id'),
            ],
        ];
    }
}
