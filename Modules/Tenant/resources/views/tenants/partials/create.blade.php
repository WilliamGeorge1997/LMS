<div class="card shadow-sm border-0">
    <div class="card-header border-0 py-4">
        <h3 class="card-title fw-bold fs-5 m-0">{{ __('tenant::text.create') }}</h3>
    </div>
    <div class="card-body pt-0">
        <form id="create-form" action="{{ route('tenants.store') }}" method="POST">
            @csrf
            <div class="row g-6">
                {{-- Name EN --}}
                <div class="col-md-6 ">
                    <label for="name_en" class="required form-label">{{ __('tenant::attributes.name_en') }}</label>
                    <input id="name_en" type="text" name="name_en" class="form-control form-control-solid"
                        placeholder="{{ __('tenant::placeholders.enter_name_en') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="name_en"></div>
                </div>


                {{-- Name AR --}}
                <div class="col-md-6">
                    <label for="name_ar" class="required form-label">{{ __('tenant::attributes.name_ar') }}</label>
                    <input id="name_ar" type="text" name="name_ar" class="form-control form-control-solid"
                        placeholder="{{ __('tenant::placeholders.enter_name_ar') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="name_ar"></div>
                </div>


                {{-- Domain --}}
                <div class="col-md-6">
                    <label for="domain" class="required form-label">{{ __('tenant::attributes.domain') }}</label>
                    <div class="input-group input-group-solid">
                        <input id="domain" type="text" name="domain" class="form-control form-control-solid"
                            placeholder="{{ __('tenant::placeholders.enter_domain') }}" autocomplete="off" />
                        <span class="input-group-text">.{{ config('tenancy.central_domains')[0] }}</span>
                    </div>
                    <div class="invalid-feedback d-block" data-error="domain"></div>
                </div>

                {{-- Is Active --}}
                <div class="col-md-6">
                    <label for="is_active" class="form-label">{{ __('tenant::attributes.is_active') }}</label>
                    <div class="form-check form-switch form-switch-sm form-check-custom mt-3">
                        <input id="is_active" class="form-check-input form-check-solid" type="checkbox" name="is_active"
                            value="1" />
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end gap-3 mt-8">
                <button type="button" id="create-cancel" class="btn btn-light">
                    {{ __('tenant::buttons.cancel') }}
                </button>
                <button type="submit" id="create-submit" class="btn btn-primary">
                    <span class="indicator-label">{{ __('tenant::buttons.submit') }}</span>
                    <span class="indicator-progress">
                        <span class="spinner-border spinner-border-sm align-middle"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
