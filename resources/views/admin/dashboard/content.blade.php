@extends('layouts.admin')

@section('site_title', formatTitle([__('Dashboard'), config('settings.title')]))

@section('admin_content')
    @php
        $totalLinks = (int) ($stats['links'] ?? 0);
        $publishedLinks = (int) ($stats['published_links'] ?? 0);
        $blockedLinks = (int) ($stats['blocked_links'] ?? 0);
        $pendingReview = (int) ($stats['pending_review'] ?? 0);
        $publishedPercent = $totalLinks > 0 ? round(($publishedLinks / $totalLinks) * 100) : 0;
        $blockedPercent = $totalLinks > 0 ? round(($blockedLinks / $totalLinks) * 100) : 0;

        $summaryCards = [
            [
                'class' => 'primary',
                'title' => __('Total links'),
                'value' => $totalLinks,
                'caption' => __('Catalog records'),
                'route' => route('admin.links'),
                'icon' => 'icons.link',
            ],
            [
                'class' => 'success',
                'title' => __('Categories'),
                'value' => $stats['workspaces'],
                'caption' => __('Sections and topics'),
                'route' => route('admin.workspaces'),
                'icon' => 'icons.workspace',
            ],
            [
                'class' => 'warning',
                'title' => __('Messages'),
                'value' => $stats['messages'],
                'caption' => __('User requests'),
                'route' => route('admin.settings.contact'),
                'icon' => 'icons.email',
            ],
            [
                'class' => 'review',
                'title' => __('Pending review'),
                'value' => $pendingReview,
                'caption' => __('New submissions'),
                'route' => route('admin.links'),
                'icon' => 'icons.expire',
            ],
        ];

        $quickActions = [
            ['title' => __('Add link'), 'route' => route('links'), 'icon' => 'icons.add'],
            ['title' => __('Import links'), 'route' => route('admin.links'), 'icon' => 'icons.decrease'],
            ['title' => __('Export links'), 'route' => route('admin.links'), 'icon' => 'icons.increase'],
            ['title' => __('Add category'), 'route' => route('workspaces.new'), 'icon' => 'icons.workspace'],
        ];
    @endphp

    <div class="admin-dashboard">
        <div class="row g-3 admin-dashboard-summary">
            @foreach($summaryCards as $card)
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="admin-summary-card admin-summary-card-{{ $card['class'] }}">
                        <div class="admin-summary-card-body">
                            <div>
                                <div class="admin-summary-value">{{ number_format($card['value'], 0, __('.'), __(',')) }}</div>
                                <div class="admin-summary-title">{{ $card['title'] }}</div>
                                <div class="admin-summary-caption">{{ $card['caption'] }}</div>
                            </div>
                            @include($card['icon'], ['class' => 'admin-summary-icon fill-current'])
                        </div>
                        <a href="{{ $card['route'] }}" class="admin-summary-link">
                            {{ __('Open section') }}
                            @include((__('lang_dir') == 'rtl' ? 'icons.chevron_left' : 'icons.chevron_right'), ['class' => 'admin-summary-link-icon fill-current'])
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-xl-4">
                <div class="card admin-dashboard-card h-100">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Link statuses') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="admin-status-item">
                            <div class="admin-status-row">
                                <span>{{ __('pending review') }}</span>
                                <span>{{ number_format($pendingReview, 0, __('.'), __(',')) }}</span>
                            </div>
                            <div class="progress admin-status-progress">
                                <div class="progress-bar bg-secondary" style="width: 0%"></div>
                            </div>
                        </div>

                        <div class="admin-status-item">
                            <div class="admin-status-row">
                                <span>{{ __('published') }}</span>
                                <span>{{ number_format($publishedLinks, 0, __('.'), __(',')) }}</span>
                            </div>
                            <div class="progress admin-status-progress">
                                <div class="progress-bar bg-primary" style="width: {{ $publishedPercent }}%">{{ $publishedPercent }}%</div>
                            </div>
                        </div>

                        <div class="admin-status-item mb-0">
                            <div class="admin-status-row">
                                <span>{{ __('blocked') }}</span>
                                <span>{{ number_format($blockedLinks, 0, __('.'), __(',')) }}</span>
                            </div>
                            <div class="progress admin-status-progress">
                                <div class="progress-bar bg-danger" style="width: {{ $blockedPercent }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card admin-dashboard-card h-100">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Quick actions') }}</h3>
                    </div>
                    <div class="list-group list-group-flush admin-action-list">
                        @foreach($quickActions as $action)
                            <a href="{{ $action['route'] }}" class="list-group-item list-group-item-action admin-action-item">
                                <span>{{ $action['title'] }}</span>
                                @include($action['icon'], ['class' => 'admin-action-icon fill-current'])
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card admin-dashboard-card h-100">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Top views') }}</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle admin-dashboard-table mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Site') }}</th>
                                    <th class="text-end">{{ __('Views') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topLinks as $link)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.links.edit', $link->id) }}" class="admin-dashboard-link">{{ $link->title ?? $link->alias }}</a>
                                            <div class="text-muted small">{{ $link->workspace?->name ?? __('Uncategorized') }}</div>
                                        </td>
                                        <td class="text-end fw-semibold">{{ number_format((int) $link->clicks, 0, __('.'), __(',')) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted py-4">{{ __('No data.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-xl-8">
                <div class="card admin-dashboard-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title">{{ __('Latest links') }}</h3>
                        <a href="{{ route('admin.links') }}" class="btn btn-outline-primary btn-sm">{{ __('All links') }}</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle admin-dashboard-table admin-latest-links-table mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end">{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($links as $link)
                                    @php
                                        $endsAt = $link->ends_at ? \Illuminate\Support\Carbon::parse($link->ends_at) : null;
                                        $createdAt = $link->created_at ? \Illuminate\Support\Carbon::parse($link->created_at) : null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.links.edit', $link->id) }}" class="admin-dashboard-link">{{ $link->title ?? $link->alias }}</a>
                                            <div class="text-muted small text-truncate">{{ $link->url }}</div>
                                        </td>
                                        <td>{{ $link->workspace?->name ?? __('Uncategorized') }}</td>
                                        <td>
                                            @if($link->disabled)
                                                <span class="badge text-bg-danger">{{ __('blocked') }}</span>
                                            @elseif($endsAt?->isPast())
                                                <span class="badge text-bg-secondary">{{ __('expired') }}</span>
                                            @else
                                                <span class="badge text-bg-primary">{{ __('published') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end text-muted">{{ $createdAt?->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">{{ __('No data.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card admin-dashboard-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title">{{ __('Latest messages') }}</h3>
                        <a href="{{ route('admin.settings.contact') }}" class="btn btn-outline-primary btn-sm">{{ __('All') }}</a>
                    </div>
                    <div class="card-body admin-empty-messages">
                        {{ __('No messages yet') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
