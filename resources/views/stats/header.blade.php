<div class="bg-base-0 pt-3">
    <div class="container pt-3">
        @include('shared.breadcrumbs', ['breadcrumbs' => [
            ['url' => route('dashboard'), 'title' => __('Home')],
            ['title' => __('Stats')]
        ]])

        <div class="d-flex">
            <h2 class="mb-0 flex-grow-1 text-break">{{ str_replace(['http://', 'https://'], '', (isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias))) }}</h2>

            <div class="d-flex align-items-center flex-grow-0">
                @include('shared.buttons.copy_link')
                @include('shared.dropdowns.link', ['options' => ['dropdown' => ['button' => true, 'share' => true, 'preview' => true, 'open' => true]]])
            </div>
        </div>

        <div class="text-truncate text-muted mt-2 d-flex align-items-center"><div class="d-flex align-items-center {{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}"><img src="https://www.google.com/s2/favicons?domain={{ parse_url($link->url)['host'] }}" rel="noreferrer" class="icon-label"></div><div class="text-truncate">{{ $link->url }}</div></div>

        @include('stats.menu')
    </div>
</div>