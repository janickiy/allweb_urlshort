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
                @php
                    $workspaceColors = formatWorkspace();
                    $selectedColor = (int) old('color', 1);
                    $selectedColor = array_key_exists($selectedColor, $workspaceColors) ? $selectedColor : 1;
                    $selectedColorClass = $workspaceColors[$selectedColor] ?? 'primary';
                @endphp

                <label for="workspace_color_dropdown">{{ __('ui.workspaces.color') }}</label>
                <input type="hidden" name="color" id="i_color" value="{{ $selectedColor }}">
                <div class="dropdown workspace-color-dropdown">
                    <button
                        type="button"
                        id="workspace_color_dropdown"
                        class="btn btn-outline-secondary dropdown-toggle w-100 d-flex align-items-center justify-content-between{{ $errors->has('color') ? ' is-invalid' : '' }}"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <span class="d-inline-flex align-items-center">
                            <span id="workspace_color_preview" class="icon-label bg-{{ $selectedColorClass }} rounded-circle mr-2"></span>
                            <span id="workspace_color_label">{{ __(ucfirst($selectedColorClass)) }}</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu w-100" aria-labelledby="workspace_color_dropdown">
                        @foreach($workspaceColors as $key => $value)
                            <li>
                                <button
                                    type="button"
                                    class="dropdown-item d-flex align-items-center workspace-color-option @if((int) $key === $selectedColor) active @endif"
                                    data-color-value="{{ $key }}"
                                    data-color-class="{{ $value }}"
                                    data-color-label="{{ __(ucfirst($value)) }}"
                                >
                                    <span class="icon-label bg-{{ $value }} rounded-circle mr-2"></span>
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
            </div>

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
                        colorPreview.className = `icon-label bg-${colorClass} rounded-circle mr-2`;
                        colorLabel.textContent = colorText;

                        colorOptions.forEach((item) => item.classList.toggle('active', item === option));
                    };

                    colorOptions.forEach((option) => {
                        option.addEventListener('click', () => updateColorPreview(option));
                    });
                });
            </script>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('ui.actions.save') }}</button>
        </form>
    </div>
</div>
