<div class="position-absolute m-auto qr-container">
    {!! QrCode::size(250)->margin(0)->generate(isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias)); !!}
</div>