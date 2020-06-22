@php
    $parameters = [
        [
            'name' => 'url',
            'type' => $type,
            'format' => 'string',
            'description' => __('The link to be shortened.')
        ],
        [
            'name' => 'alias',
            'type' => 0,
            'format' => 'string',
            'description' => __('The link alias.')
        ],
        [
            'name' => 'password',
            'type' => 0,
            'format' => 'string',
            'description' => __('The link password.')
        ],
        [
            'name' => 'space',
            'type' => 0,
            'format' => 'integer',
            'description' => __('The space id the link to be saved under.')
        ],
        [
            'name' => 'domain',
            'type' => 0,
            'format' => 'integer',
            'description' => __('The domain id the link to be saved under.')
        ],
        [
            'name' => 'disabled',
            'type' => 0,
            'format' => 'integer',
            'description' => __('Whether the link is disabled or not, defaults to :value.', ['value' => '<code>0</code>'])
        ],
        [
            'name' => 'public',
            'type' => 0,
            'format' => 'integer',
            'description' => __('Whether the link stats are public or not, defaults to :value.', ['value' => '<code>0</code>'])
        ],
        [
            'name' => 'expiration_url',
            'type' => 0,
            'format' => 'string',
            'description' => __('The link where the user will be redirected once the link has expired.')
        ],
        [
            'name' => 'expiration_date',
            'type' => 0,
            'format' => 'string',
            'description' => __('The link expiration date in :format format.', ['format' => '<code>YYYY-MM-DD</code>'])
        ],
        [
            'name' => 'expiration_time',
            'type' => 0,
            'format' => 'string',
            'description' => __('The link expiration time in :format format.', ['format' => '<code>HH:MM</code>'])
        ],
        [
            'name' => 'geo[index][key]',
            'type' => 0,
            'format' => 'string',
            'description' => __('The code of the targeted country. The code must be in :standard standard.', ['standard' => '<a href="https://wikipedia.org/wiki/ISO_3166-1_alpha-2#Officially_assigned_code_elements" target="_blank" rel="nofollow">ISO 3166-1 alpha-2</a>'])
        ],
        [
            'name' => 'geo[index][value]',
            'type' => 0,
            'format' => 'string',
            'description' => __('The country link where the user will be redirected to.')
        ],
        [
            'name' => 'platform[index][key]',
            'type' => 0,
            'format' => 'string',
            'description' => __('The name of the targeted platform. Possible values are :platforms.', ['platforms' => '<code>'.implode('</code>, <code>', config('platforms')).'</code>'])
        ],
        [
            'name' => 'platform[index][value]',
            'type' => 0,
            'format' => 'string',
            'description' => __('The platform link where the user will be redirected to.')
        ]
    ];
@endphp

<div class="list-group list-group-flush mb-n3">
    <div class="list-group-item px-0 text-muted">
        <div class="row align-items-center">
            <div class="col-12 col-lg-3">{{ __('Parameter') }}</div>
            <div class="col-12 col-lg-2">{{ __('Type') }}</div>
            <div class="col-12 col-lg-7">{{ __('Description') }}</div>
        </div>
    </div>

    @foreach($parameters as $parameter)
        <div class="list-group-item px-0">
            <div class="row align-items-center">
                <div class="col-12 col-lg-3"><code>{{ $parameter['name'] }}</code></div>
                <div class="col-12 col-lg-2">@if($parameter['type'])<span class="badge badge-danger">{{ mb_strtolower(__('Required')) }}</span>@else<span class="badge badge-primary">{{ mb_strtolower(__('Optional')) }}</span>@endif <span class="badge badge-secondary">{{ $parameter['format'] }}</span></div>
                <div class="col-12 col-lg-7">{!! $parameter['description']  !!}</div>
            </div>
        </div>
    @endforeach
</div>