<div class="card shadow-sm border-0">
    <div class="card-header border-0 py-4">
        <h3 class="card-title fw-bold fs-5 m-0">{{ __('level::buttons.create') }}</h3>
    </div>
    <div class="card-body pt-0">
        <form id="create-form" action="{{ url('/admin/levels') }}" method="POST">
            @csrf
            <div class="row g-6">

                <div class="col-md-6">
                    <label class="required form-label">{{ __('level::attributes.title_en') }}</label>
                    <input type="text" name="title_en" class="form-control form-control-solid"
                        placeholder="{{ __('level::placeholders.enter_title_en') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="title_en"></div>
                </div>

                <div class="col-md-6">
                    <label class="required form-label">{{ __('level::attributes.title_ar') }}</label>
                    <input type="text" name="title_ar" class="form-control form-control-solid"
                        placeholder="{{ __('level::placeholders.enter_title_ar') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="title_ar"></div>
                </div>

                <div class="col-md-6">
                    <label class="required form-label">{{ __('level::attributes.publisher_id') }}</label>
                    <select name="publisher_id" class="form-select form-select-solid">
                        <option value="" disabled selected>
                            {{ __('level::placeholders.select_publisher') }}</option>
                        @foreach ($viewModel->publishersByTenant() as $publisher)
                            <option value="{{ $publisher->id }}">{{ $publisher->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback d-block" data-error="publisher_id"></div>
                </div>

                <div class="col-md-6">
                    <label for="category_id" class="required form-label">{{ __('level::attributes.category_id') }}</label>
                    <select name="category_id" id="category_id" class="form-select form-select-solid" data-depends-on="publisher_id"
                        data-depends-url="{{ url('/admin/categories/ajax_category') }}?publisher_id=:value"
                        data-depends-placeholder="{{ __('level::placeholders.select_category') }}" disabled>
                        <option value="" disabled selected>{{ __('level::placeholders.select_category') }}</option>
                    </select>
                    <div class="invalid-feedback d-block" data-error="category_id"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('level::attributes.is_active') }}</label>
                    <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" />
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end gap-3 mt-8">
                <button type="button" id="create-cancel" class="btn btn-light">
                    {{ __('level::buttons.cancel') }}
                </button>
                <button type="submit" id="create-submit" class="btn btn-primary">
                    <span class="indicator-label">{{ __('level::buttons.submit') }}</span>
                    <span class="indicator-progress">
                        <span class="spinner-border spinner-border-sm align-middle"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
