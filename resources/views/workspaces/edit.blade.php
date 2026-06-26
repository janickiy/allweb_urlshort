@section('site_title', formatTitle([__('Edit'), __('Workspace'), config('settings.title')]))

@if(! isset($admin))
    @include('shared.breadcrumbs', ['breadcrumbs' => [
        ['url' => route('dashboard'), 'title' => __('Home')],
        ['url' => route('workspaces'), 'title' => __('Workspaces')],
        ['title' => __('Edit')],
    ]])

    <div class="d-flex">
        <h2 class="mb-3 text-break">{{ __('Edit') }}</h2>
    </div>
@endif

<form action="{{ isset($admin) ? route('admin.workspaces.edit', $workspace->id) : route('workspaces.edit', $workspace->id) }}" method="post" enctype="multipart/form-data">
    @csrf

    @if(isset($admin))
        <input type="hidden" name="user_id" value="{{ $workspace->user->id }}">
    @endif

    <div class="{{ isset($admin) ? 'card card-primary card-outline shadow-sm mb-0 admin-form-card admin-workspace-edit-card' : 'card border-0 shadow-sm' }}">
        <div class="card-header">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md">
                    @if(isset($admin))
                        <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                            @include('icons.workspace', ['class' => 'fill-current icon-text'])
                            {{ __('Workspace') }}
                        </h3>
                    @else
                        <div class="font-weight-medium py-1">{{ __('Workspace') }}</div>
                    @endif
                </div>

                <div class="col-12 col-md-auto">
                    @if(isset($admin))
                        <a href="{{ route('admin.links', ['workspace_id' => $workspace->id]) }}" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-2">
                            @include('icons.link', ['class' => 'fill-current icon-button-sm'])
                            {{ __('Links') }}
                            <span class="badge text-bg-primary">{{ number_format($stats['links'], 0, __('.'), __(',')) }}</span>
                        </a>
                    @else
                        <a href="{{ route('links', ['workspace' => $workspace->id]) }}" class="btn btn-outline-primary btn-sm">{{ __('View') }}</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body">
            @include('shared.message')

            <div class="row g-3">
                <div class="{{ isset($admin) ? 'col-12 col-lg-6' : 'col-12' }}">
                    <div class="mb-3">
                        <label class="{{ isset($admin) ? 'form-label' : '' }}" for="i_name">{{ __('Name') }}</label>
                        <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" id="i_name" value="{{ $workspace->name }}">
                        @if ($errors->has('name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="{{ isset($admin) ? 'col-12 col-lg-6' : 'col-12' }}">
                    <div class="mb-3">
                        <label class="{{ isset($admin) ? 'form-label' : '' }}" for="{{ isset($admin) ? 'i_color' : 'i_color1' }}">{{ __('Color') }}</label>
                        @if(isset($admin))
                            @php
                                $workspaceColors = formatWorkspace();
                                $selectedColor = (int) old('color', $workspace->color);
                                $selectedColorClass = $workspaceColors[$selectedColor] ?? 'primary';
                            @endphp

                            <input type="hidden" name="color" id="i_color" value="{{ $selectedColor }}">
                            <div class="dropdown workspace-color-dropdown">
                                <button
                                    type="button"
                                    id="workspace_color_dropdown"
                                    class="btn btn-outline-secondary dropdown-toggle w-100 d-flex align-items-center justify-content-between{{ $errors->has('color') ? ' is-invalid' : '' }}"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    <span class="d-inline-flex align-items-center gap-2">
                                        <span id="workspace_color_preview" class="icon-label bg-{{ $selectedColorClass }} rounded-circle"></span>
                                        <span id="workspace_color_label">{{ __(ucfirst($selectedColorClass)) }}</span>
                                    </span>
                                </button>
                                <ul class="dropdown-menu w-100" aria-labelledby="workspace_color_dropdown">
                                    @foreach($workspaceColors as $key => $value)
                                        <li>
                                            <button
                                                type="button"
                                                class="dropdown-item d-flex align-items-center gap-2 workspace-color-option @if((int) $key === $selectedColor) active @endif"
                                                data-color-value="{{ $key }}"
                                                data-color-class="{{ $value }}"
                                                data-color-label="{{ __(ucfirst($value)) }}"
                                            >
                                                <span class="icon-label bg-{{ $value }} rounded-circle"></span>
                                                <span>{{ __(ucfirst($value)) }}</span>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                                @if ($errors->has('color'))
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $errors->first('color') }}</strong>
                                    </span>
                                @endif
                            </div>
                        @else
                            <div class="d-flex flex-wrap gap-3">
                                @foreach(formatWorkspace() as $key => $value)
                                    <div class="form-check form-check-inline m-0">
                                        <input type="radio" id="i_color{{ $key }}" name="color" class="form-check-input" value="{{ $key }}" @if($key == $workspace->color) checked @endif>
                                        <label class="form-check-label d-inline-flex align-items-center gap-2" for="i_color{{ $key }}">
                                            <span class="icon-label bg-{{ $value }} rounded-circle cursor-pointer"></span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if(isset($admin))
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const colorSelect = document.getElementById('i_color');
                        const colorPreview = document.getElementById('workspace_color_preview');
                        const colorLabel = document.getElementById('workspace_color_label');
                        const colorOptions = document.querySelectorAll('.workspace-color-option');

                        if (!colorSelect || !colorPreview || !colorLabel || colorOptions.length === 0) {
                            return;
                        }

                        const updateColorPreview = (option) => {
                            const colorClass = option.dataset.colorClass || 'primary';
                            const colorValue = option.dataset.colorValue || '1';
                            const colorText = option.dataset.colorLabel || option.textContent.trim();

                            colorSelect.value = colorValue;
                            colorPreview.className = `icon-label bg-${colorClass} rounded-circle`;
                            colorLabel.textContent = colorText;

                            colorOptions.forEach((item) => item.classList.toggle('active', item === option));
                        };

                        colorOptions.forEach((option) => {
                            option.addEventListener('click', () => updateColorPreview(option));
                        });
                    });
                </script>
            @endif

            @if(! isset($admin))
                <div class="row mt-3">
                    <div class="col">
                        <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteModal">{{ __('Delete') }}</button>
                    </div>
                </div>
            @endif
        </div>

        @if(isset($admin))
            <div class="card-footer bg-body">
                <div class="d-flex flex-wrap justify-content-between gap-2">
                    <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        @include('icons.delete', ['class' => 'fill-current icon-button-sm'])
                        {{ __('Delete') }}
                    </button>
                    <button type="submit" name="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                        @include('icons.checkmark', ['class' => 'fill-current icon-button-sm'])
                        {{ __('Save') }}
                    </button>
                </div>
            </div>
        @endif
    </div>
</form>

@if(isset($admin) && isset($workspace->user))
    <div class="card card-secondary card-outline shadow-sm mt-3 admin-form-card admin-workspace-user-card">
        <div class="card-header">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md">
                    <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                        @include('icons.user', ['class' => 'fill-current icon-text'])
                        {{ __('User') }}
                    </h3>
                </div>
                <div class="col-12 col-md-auto">
                    <a href="{{ route('admin.users.edit', $workspace->user->id) }}" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-2">
                        @include('icons.edit', ['class' => 'fill-current icon-button-sm'])
                        {{ __('Edit') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div class="text-muted small mb-1">{{ __('Name') }}</div>
                    <div class="fw-medium">{{ $workspace->user->name }}</div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="text-muted small mb-1">{{ __('Email') }}</div>
                    <div class="fw-medium">{{ $workspace->user->email }}</div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteWorkspaceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered admin-workspace-delete-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h6 class="modal-title d-flex align-items-center gap-2" id="deleteWorkspaceModalLabel">
                    @if(isset($admin))
                        @include('icons.delete', ['class' => 'fill-current icon-button-sm text-danger'])
                    @endif
                    {{ __('Delete') }}
                </h6>
                @if(isset($admin))
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                @else
                    <button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close')</span>
                    </button>
                @endif
            </div>
            <div class="modal-body">
                <p class="mb-2">{{ __('Deleting this workspace is permanent, and will remove all the links associated with it.') }}</p>
                <p class="mb-0 text-muted">{{ __('Are you sure you want to delete :name?', ['name' => $workspace->name]) }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @if(isset($admin)) data-bs-dismiss="modal" @else data-dismiss="modal" @endif>{{ __('Close') }}</button>
                <form action="{{ isset($admin) ? route('admin.workspaces.delete', $workspace->id) : route('workspaces.delete', $workspace->id) }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <button type="submit" class="btn btn-danger d-inline-flex align-items-center gap-2">
                        @if(isset($admin))
                            @include('icons.delete', ['class' => 'fill-current icon-button-sm'])
                        @endif
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
