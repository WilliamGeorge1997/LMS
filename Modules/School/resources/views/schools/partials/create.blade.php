<div class="card shadow-sm border-0">
    <div class="card-header border-0 py-4">
        <h3 class="card-title fw-bold fs-5 m-0">{{ __('school::buttons.create') }}</h3>
    </div>
    <div class="card-body pt-0">
        <form id="create-form" action="{{ url('/admin/schools') }}" method="POST">
            @csrf
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="required form-label">{{ __('school::attributes.title_en') }}</label>
                    <input type="text" name="title_en" class="form-control form-control-solid"
                        placeholder="{{ __('school::placeholders.enter_title_en') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="title_en"></div>
                </div>

                <div class="col-md-6">
                    <label class="required form-label">{{ __('school::attributes.title_ar') }}</label>
                    <input type="text" name="title_ar" class="form-control form-control-solid"
                        placeholder="{{ __('school::placeholders.enter_title_ar') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="title_ar"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('school::attributes.phone') }}</label>
                    <input type="text" name="phone" class="form-control form-control-solid"
                        placeholder="{{ __('school::placeholders.enter_phone') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="phone"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('school::attributes.email') }}</label>
                    <input type="email" name="email" class="form-control form-control-solid"
                        placeholder="{{ __('school::placeholders.enter_email') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="email"></div>
                </div>

                <div class="col-md-4">
                    <label class="required form-label">{{ __('school::attributes.country_id') }}</label>
                    <select name="country_id" class="form-select form-select-solid">
                        <option value="" disabled selected>{{ __('school::placeholders.select_country') }}</option>
                        @foreach ($viewModel->countries() as $country)
                            <option value="{{ $country->id }}">
                                {{ $country->getTranslation('title', 'en') }} - {{ $country->getTranslation('title', 'ar') }}
                            </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback d-block" data-error="country_id"></div>
                </div>

                <div class="col-md-4">
                    <label class="required form-label">{{ __('school::attributes.city_id') }}</label>
                    <select name="city_id" class="form-select form-select-solid" data-depends-on="country_id"
                        data-depends-url="{{ url('/admin/schools/ajax_city') }}?country_id=:value"
                        data-depends-placeholder="{{ __('school::placeholders.select_city') }}" disabled>
                        <option value="" disabled selected>{{ __('school::placeholders.select_city') }}</option>
                    </select>
                    <div class="invalid-feedback d-block" data-error="city_id"></div>
                </div>

                <div class="col-md-4">
                    <label class="required form-label">{{ __('school::attributes.region_id') }}</label>
                    <select name="region_id" class="form-select form-select-solid" data-depends-on="city_id"
                        data-depends-url="{{ url('/admin/schools/ajax_region') }}?city_id=:value"
                        data-depends-placeholder="{{ __('school::placeholders.select_region') }}" disabled>
                        <option value="" disabled selected>{{ __('school::placeholders.select_region') }}</option>
                    </select>
                    <div class="invalid-feedback d-block" data-error="region_id"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('school::attributes.is_active') }}</label>
                    <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" />
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-8">
                <button type="button" id="create-cancel" class="btn btn-light">{{ __('school::buttons.cancel') }}</button>
                <button type="submit" id="create-submit" class="btn btn-primary">
                    <span class="indicator-label">{{ __('school::buttons.submit') }}</span>
                    <span class="indicator-progress">
                        <span class="spinner-border spinner-border-sm align-middle"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
