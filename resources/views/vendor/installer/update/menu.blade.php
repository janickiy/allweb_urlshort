@section('menu')
    <div class="nav flex-column">
        <ul class="nav nav-pills d-flex justify-content-center mb-4">
            <li class="nav-item mx-2">
                <a href="{{ route('LaravelUpdater::welcome') }}" class="btn d-flex align-items-center {{ isActive('LaravelUpdater::welcome') ? ' btn-primary' : (Request::is('update/overview') ? ' text-primary' : ' disabled') }}">
                    @include('icons.home', ['class' => 'icon-button fill-current'])&#8203;
                </a>
            </li>

            <li class="nav-item mx-2">
                <a href="{{ route('LaravelUpdater::overview') }}" class="btn d-flex align-items-center {{ isActive('LaravelUpdater::overview') ? ' btn-primary' : ' disabled' }}">
                    @include('icons.update', ['class' => 'icon-button fill-current'])&#8203;
                </a>
            </li>

            <li class="nav-item {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}">
                <a href="{{ route('LaravelUpdater::final') }}" class="btn d-flex align-items-center {{ isActive('LaravelUpdater::final') ? ' btn-primary' : ' disabled' }}">
                    @include('icons.checkmark', ['class' => 'icon-button fill-current'])&#8203;
                </a>
            </li>
        </ul>
    </div>
@endsection