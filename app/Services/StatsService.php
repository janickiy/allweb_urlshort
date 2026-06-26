<?php

namespace App\Services;

use App\Models\Link;
use App\Repositories\LinkRepository;
use App\Repositories\StatRepository;
use App\Repositories\UserRepository;
use App\Traits\UserFeaturesTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class StatsService
{
    use UserFeaturesTrait;

    private const SOCIAL_DOMAINS = [
        'l.facebook.com' => 'Facebook',
        't.co' => 'X',
        'l.instagram.com' => 'Instagram',
        'out.reddit.com' => 'Reddit',
        'www.youtube.com' => 'YouTube',
        'away.vk.com' => 'VK',
        't.umblr.com' => 'Tumblr',
    ];

    /**
     * Inject dependencies used by statistics operations.
     */
    public function __construct(
        private readonly LinkRepository $links,
        private readonly StatRepository $stats,
        private readonly UserRepository $users,
    ) {
    }

    /**
     * Build general statistics data for a link.
     *
     * @return array<string, mixed>
     */
    public function general(int|string $id): array
    {
        $data = $this->baseData($id);
        $clicks = null;

        if ($data['canViewStats']) {
            $clicks = $this->stats->paginateForLink($data['link']->id);
        }

        return array_merge($data, [
            'view' => 'general',
            'clicks' => $clicks,
            'stats' => $this->summary($data['link']),
        ]);
    }

    /**
     * Build geographic statistics data for a link.
     *
     * @return array<string, mixed>
     */
    public function geographic(int|string $id): array
    {
        $data = $this->baseData($id);
        $countriesChart = $countries = null;

        if ($data['canViewStats']) {
            $countriesChart = $this->stats->groupForLink($data['link']->id, 'country', null, false);
            $countries = $this->stats->groupForLink($data['link']->id, 'country');
        }

        return array_merge($data, [
            'view' => 'geographic',
            'countries' => $countries,
            'countriesChart' => $countriesChart,
        ]);
    }

    /**
     * Build grouped statistics data for a link.
     *
     * @return array<string, mixed>
     */
    public function grouped(int|string $id, string $view, string $column, string $resultKey): array
    {
        $data = $this->baseData($id);
        $result = null;

        if ($data['canViewStats']) {
            $result = $this->stats->groupForLink($data['link']->id, $column);
        }

        return array_merge($data, [
            'view' => $view,
            $resultKey => $result,
        ]);
    }

    /**
     * Build social referrer statistics data for a link.
     *
     * @return array<string, mixed>
     */
    public function social(int|string $id): array
    {
        $data = $this->baseData($id);
        $socials = $totalCount = null;

        if ($data['canViewStats']) {
            $socials = $this->stats->groupForLink($data['link']->id, 'referrer', array_keys(self::SOCIAL_DOMAINS));
            $totalCount = $socials->getCollection()->sum('count');
        }

        return array_merge($data, [
            'view' => 'social',
            'domains' => self::SOCIAL_DOMAINS,
            'socials' => $socials,
            'totalCount' => $totalCount,
        ]);
    }

    /**
     * Build shared statistics view data for a link.
     *
     * @return array<string, mixed>
     */
    private function baseData(int|string $id): array
    {
        /** @var Link $link */
        $link = $this->links->findOrFail($id);

        $this->guard($link);

        $user = $this->users->findOrFail($link->user_id);
        $remoteUserFeatures = $this->getFeatures($user);

        return [
            'link' => $link,
            'user' => $user,
            'remoteUserFeatures' => $remoteUserFeatures,
            'canViewStats' => $user->can('stats', [Link::class, $remoteUserFeatures['option_stats']]),
        ];
    }

    /**
     * Build daily, weekly, monthly, and total click summaries.
     *
     * @return array<string, array<string, int>>
     */
    private function summary(Link $link): array
    {
        return [
            'Last 24 hours' => [
                'current' => $this->stats->countForLinkSince($link->id, Carbon::now()->subDay()),
                'previous' => $this->stats->countForLinkBetween($link->id, Carbon::now()->subDays(2), Carbon::now()->subDay()),
            ],
            'Last 30 days' => [
                'current' => $this->stats->countForLinkSince($link->id, Carbon::now()->subDays(30)),
                'previous' => $this->stats->countForLinkBetween($link->id, Carbon::now()->subDays(60), Carbon::now()->subDays(30)),
            ],
            'All time' => [
                'current' => (int) $link->clicks,
            ],
        ];
    }

    /**
     * Abort when the current user cannot view link statistics.
     */
    private function guard(Link $link): void
    {
        if ($link->public) {
            return;
        }

        $user = Auth::user();

        if ($user === null || ($user->id !== $link->user_id && $user->role !== 1)) {
            abort(403);
        }
    }
}
