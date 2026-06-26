@section('site_title', formatTitle([__('ui.actions.new'), __('ui.workspaces.singular'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('dashboard'), 'title' => __('ui.nav.home')],
    ['url' => route('workspaces'), 'title' => __('ui.nav.workspaces')],
    ['title' => __('ui.actions.new')],
]])

<h2 class="mb-3 d-inline-block">{{ __('ui.actions.new') }}</h2>

<div class="card border-0 shadow-sm">
    <div class="card-header">
        <div class="row">
            <div class="col">
                <div class="font-weight-medium py-1">{{ __('ui.workspaces.singular') }}</div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @include('shared.message')

        <form action="{{ route('workspaces.new') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="i_name">{{ __('ui.workspaces.name') }}</label>
                <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" id="i_name" value="{{ old('name') }}">
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i_name">{{ __('ui.workspaces.color') }}</label>
                <div class="form-row">
                    @foreach(formatWorkspace() as $key => $value)
                        <div class="col-4 col-sm">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="i_color{{ $key }}" name="color" class="custom-control-input" value="{{ $key }}" @if($key == 1) checked @endif>
                                <label class="custom-control-label d-flex align-items-center" for="i_color{{ $key }}"><span class="icon-label bg-{{ $value }} rounded-circle cursor-pointer"></span></label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('ui.actions.save') }}</button>
        </form>
    </div>
</div>
