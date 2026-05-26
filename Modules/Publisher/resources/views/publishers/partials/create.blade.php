@php($is_super_admin = auth('admin')->user()->hasRole(\Modules\Admin\Enums\Role::SUPER_ADMIN))
<div class="card shadow-sm border-0">
    <div class="card-header border-0 py-4">
        <h3 class="card-title fw-bold fs-5 m-0">{{ __('publisher::buttons.create') }}</h3>
    </div>
    <div class="card-body pt-0">
        <form id="create-form" action="{{ url('/admin/publishers') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-6">

                <div class="col-md-6">
                    <label class="required form-label">{{ __('publisher::attributes.name_en') }}</label>
                    <input type="text" name="name_en" class="form-control form-control-solid"
                        placeholder="{{ __('publisher::placeholders.enter_name_en') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="name_en"></div>
                </div>

                  <div class="col-md-6">
                    <label class="required form-label">{{ __('publisher::attributes.name_ar') }}</label>
                    <input type="text" name="name_ar" class="form-control form-control-solid"
                        placeholder="{{ __('publisher::placeholders.enter_name_ar') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="name_ar"></div>
                </div>


                <div class="col-md-6">
                    <label class="form-label">{{ __('publisher::attributes.is_active') }}</label>
                    <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" />
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end gap-3 mt-8">
                <button type="button" id="create-cancel" class="btn btn-light">
                    {{ __('publisher::buttons.cancel') }}
                </button>
                <button type="submit" id="create-submit" class="btn btn-primary">
                    <span class="indicator-label">{{ __('publisher::buttons.submit') }}</span>
                    <span class="indicator-progress">
                        <span class="spinner-border spinner-border-sm align-middle"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>