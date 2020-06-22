@section('menu')
<div class="nav flex-column">
    <ul class="nav nav-pills d-flex justify-content-center mb-4">
        <li class="nav-item {{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">
            <a href="{{ route('LaravelInstaller::welcome') }}" class="btn d-flex align-items-center{{ isActive('LaravelInstaller::welcome') ? ' btn-primary' : (Request::is('install/requirements') || Request::is('install/permissions') || Request::is('install/environment') || Request::is('install/environment/wizard') || Request::is('install/environment/classic') ? ' text-primary' : ' disabled') }}">
                @include('icons.home', ['class' => 'icon-button fill-current'])&#8203;
            </a>
        </li>

        <li class="nav-item mx-2">
            <a href="{{ route('LaravelInstaller::requirements') }}" class="btn d-flex align-items-center{{ isActive('LaravelInstaller::requirements') ? ' btn-primary' : (Request::is('install/permissions') || Request::is('install/environment') || Request::is('install/environment/wizard') || Request::is('install/environment/classic') ? ' text-primary' : ' disabled') }}">
                @include('icons.page', ['class' => 'icon-button fill-current'])&#8203;
            </a>
        </li>

        <li class="nav-item mx-2">
            <a href="{{ route('LaravelInstaller::permissions') }}" class="btn d-flex align-items-center {{ isActive('LaravelInstaller::permissions') ? ' btn-primary' : (Request::is('install/environment') || Request::is('install/environment/wizard') || Request::is('install/environment/classic') ? ' text-primary' : ' disabled') }}">
                @include('icons.key', ['class' => 'icon-button fill-current'])&#8203;
            </a>
        </li>

        <li class="nav-item mx-2">
            <a href="{{ route('LaravelInstaller::environmentWizard') }}" class="btn d-flex align-items-center{{ isActive('LaravelInstaller::environment') || isActive('LaravelInstaller::environmentWizard') || isActive('LaravelInstaller::environmentClassic') ? ' btn-primary' : ' disabled' }}">
                @include('icons.settings', ['class' => 'icon-button fill-current'])&#8203;
            </a>
        </li>

        <li class="nav-item {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}">
            <a href="#" class="btn d-flex align-items-center {{ isActive('LaravelInstaller::final') ? ' btn-primary' : ' disabled' }}">
                @include('icons.checkmark', ['class' => 'icon-button fill-current'])&#8203;
            </a>
        </li>
    </ul>
</div>
@endsection