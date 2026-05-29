<div class="card shadow-sm border-0">
    <div class="card-header border-0 py-4">
        <h3 class="card-title fw-bold fs-5 m-0">{{ __('book::buttons.create') }}</h3>
    </div>
    <div class="card-body pt-0">
        <form id="create-form" action="{{ url('/admin/books') }}" method="POST">
            @csrf
            <div class="row g-6">

                <div class="col-md-6">
                    <label class="required form-label">{{ __('book::attributes.title_en') }}</label>
                    <input type="text" name="title_en" class="form-control form-control-solid"
                        placeholder="{{ __('book::placeholders.enter_title_en') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="title_en"></div>
                </div>

                <div class="col-md-6">
                    <label class="required form-label">{{ __('book::attributes.title_ar') }}</label>
                    <input type="text" name="title_ar" class="form-control form-control-solid"
                        placeholder="{{ __('book::placeholders.enter_title_ar') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="title_ar"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('book::attributes.description_en') }}</label>
                    <textarea name="description_en" rows="3" class="form-control form-control-solid"
                        placeholder="{{ __('book::placeholders.enter_description_en') }}"></textarea>
                    <div class="invalid-feedback d-block" data-error="description_en"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('book::attributes.description_ar') }}</label>
                    <textarea name="description_ar" rows="3" class="form-control form-control-solid"
                        placeholder="{{ __('book::placeholders.enter_description_ar') }}"></textarea>
                    <div class="invalid-feedback d-block" data-error="description_ar"></div>
                </div>

                <div class="col-md-6">
                    <label class="required form-label">{{ __('book::attributes.isbn') }}</label>
                    <input type="text" name="isbn" class="form-control form-control-solid"
                        placeholder="{{ __('book::placeholders.enter_isbn') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="isbn"></div>
                </div>

                <div class="col-md-4">
                    <label class="required form-label">{{ __('book::attributes.publisher_id') }}</label>
                    <select name="publisher_id" class="form-select form-select-solid">
                        <option value="" disabled selected>{{ __('book::placeholders.select_publisher') }}
                        </option>
                        @foreach ($viewModel->publishersByTenant() as $publisher)
                            <option value="{{ $publisher->id }}">{{ $publisher->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback d-block" data-error="publisher_id"></div>
                </div>

                <div class="col-md-4">
                    <label class="required form-label">{{ __('book::attributes.category_id') }}</label>
                    <select name="category_id" class="form-select form-select-solid" data-depends-on="publisher_id"
                        data-depends-url="{{ url('/admin/categories/ajax_category') }}?publisher_id=:value"
                        data-depends-placeholder="{{ __('book::placeholders.select_category') }}" disabled>
                        <option value="" disabled selected>{{ __('book::placeholders.select_category') }}</option>
                    </select>
                    <div class="invalid-feedback d-block" data-error="category_id"></div>
                </div>

                <div class="col-md-4">
                    <label class="required form-label">{{ __('book::attributes.level_id') }}</label>
                    <select name="level_id" class="form-select form-select-solid" data-depends-on="category_id"
                        data-depends-url="{{ url('/admin/levels/ajax_level') }}?category_id=:value"
                        data-depends-placeholder="{{ __('book::placeholders.select_level') }}" disabled>
                        <option value="" disabled selected>{{ __('book::placeholders.select_level') }}</option>
                    </select>
                    <div class="invalid-feedback d-block" data-error="level_id"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('book::attributes.is_active') }}</label>
                    <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" />
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end gap-3 mt-8">
                <button type="button" id="create-cancel" class="btn btn-light">
                    {{ __('book::buttons.cancel') }}
                </button>
                <button type="submit" id="create-submit" class="btn btn-primary">
                    <span class="indicator-label">{{ __('book::buttons.submit') }}</span>
                    <span class="indicator-progress">
                        <span class="spinner-border spinner-border-sm align-middle"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
