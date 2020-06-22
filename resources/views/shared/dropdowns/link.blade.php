@if(isset($options['dropdown']['button']))
    <button type="button" class="btn text-primary btn-sm d-flex align-items-center {{ (__('lang_dir') == 'rtl' ? 'mr-3' : 'ml-3') }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@include('icons.horizontal_menu', ['class' => 'fill-current icon-button'])&#8203;</button>
@endif

<div class="dropdown-menu {{ (__('lang_dir') == 'rtl' ? 'dropdown-menu' : 'dropdown-menu-right') }} border-0 shadow">
    @if(isset($options['dropdown']['edit']))
        <a class="dropdown-item d-flex align-items-center" href="{{ isset($admin) ? route('admin.links.edit', $link->id) : route('links.edit', $link->id) }}">@include('icons.edit', ['class' => 'text-muted fill-current icon-dropdown '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Edit') }}</a>
    @endif

    @if(isset($options['dropdown']['share']))
        <a class="dropdown-item d-flex align-items-center link-share" href="#" data-toggle="modal" data-target="#shareModal" data-url="{{ (isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias)) }}" data-title="{{ $link->title ?? str_replace(['http://', 'https://'], '', $link->url) }}" data-qr="{{ route('qr', $link->id) }}">@include('icons.share', ['class' => 'text-muted fill-current icon-dropdown '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Share') }}</a>
    @endif

    @if(isset($options['dropdown']['stats']))
        <a class="dropdown-item d-flex align-items-center" href="{{ route('stats', $link->id) }}">@include('icons.stats', ['class' => 'text-muted fill-current icon-dropdown '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Stats') }}</a>
    @endif

    @if(isset($options['dropdown']['preview']))
        <a class="dropdown-item d-flex align-items-center" href="{{ isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias) }}/+" target="_blank">@include('icons.preview', ['class' => 'text-muted fill-current icon-dropdown '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Preview') }}</a>
    @endif

    @if(isset($options['dropdown']['open']))
        <a class="dropdown-item d-flex align-items-center" href="{{ isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias) }}" target="_blank">@include('icons.external', ['class' => 'text-muted fill-current icon-dropdown '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Open') }}</a>
    @endif

    @if(isset($options['dropdown']['delete']))
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger d-flex align-items-center" href="#" data-toggle="modal" data-target="#deleteLinkModal" data-action="{{ isset($admin) ? route('admin.links.delete', $link->id) : route('links.delete', $link->id) }}" data-text="{{ __('Are you sure you want to delete :name?', ['name' => (str_replace(['http://', 'https://'], '', (isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias))))]) }}">@include('icons.delete', ['class' => 'fill-current icon-dropdown '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')]) {{ __('Delete') }}</a>
    @endif
</div>