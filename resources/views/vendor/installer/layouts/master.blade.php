@extends('layouts.wrapper')
@section('body')
<body class="d-flex flex-column">
    <div class="bg-base-1 flex-fill">
        <div class="container">
            <div class="row h-100 justify-content-center align-items-center py-5">
                <div class="col-lg-6">
                    @yield('menu')

                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</body>
@endsection
