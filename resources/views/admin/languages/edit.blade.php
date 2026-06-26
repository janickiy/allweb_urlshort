@include('shared.message')

<form action="{{ route('admin.languages.update', $language->code) }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="card card-primary card-outline shadow-sm mb-0 admin-form-card admin-language-edit-card">
        <div class="card-header">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md">
                    <h3 class="card-title d-flex align-items-center gap-2 mb-0">
                        @include('icons.language', ['class' => 'fill-current icon-text'])
                        {{ __('Language') }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-3 mb-4">
                <div class="col-12 col-lg-6">
                    <div class="text-muted small mb-1">{{ __('Name') }}</div>
                    <div class="fw-medium">{{ $language->name }}</div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="text-muted small mb-1">{{ __('Code') }}</div>
                    <div class="fw-medium">{{ $language->code }}</div>
                </div>
            </div>

            <div class="border-top pt-3">
                <label class="form-label">{{ __('Settings') }}</label>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="default" id="i_default" value="1" @if($language->default) checked disabled @endif>
                    <label class="form-check-label" for="i_default">{{ __('Default') }}</label>
                </div>
            </div>
        </div>

        <div class="card-footer bg-body">
            <div class="d-flex flex-wrap justify-content-between gap-2">
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" @if($language->default) disabled @endif>{{ __('Delete') }}</button>
                <button type="submit" name="submit" class="btn btn-primary d-inline-flex align-items-center gap-2">
                    @include('icons.checkmark', ['class' => 'fill-current icon-button-sm'])
                    {{ __('Save') }}
                </button>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteLanguageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h6 class="modal-title" id="deleteLanguageModalLabel">{{ __('Delete') }}</h6>
                <button type="button" class="close d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close')</span>
                </button>
            </div>
            <div class="modal-body">
                {{ __('Are you sure you want to delete :name?', ['name' => $language->name]) }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <form action="{{ route('admin.languages.delete', $language->code) }}" method="post" enctype="multipart/form-data">

                    @csrf

                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
