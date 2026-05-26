<div class="card shadow-sm border-0">
    <div class="card-header border-0 py-4">
        <h3 class="card-title fw-bold fs-5 m-0">Create City</h3>
    </div>
    <div class="card-body pt-0">
        <form id="create-form" action="{{ route('cities.store') }}" method="POST">
            @csrf
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="required form-label">Country</label>
                    <select name="country_id" class="form-select form-select-solid">
                        <option value="" disabled selected>Select country</option>
                        @foreach ($countryOptions as $opt)
                            <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback d-block" data-error="country_id"></div>
                </div>
                <div class="col-md-6">
                    <label class="required form-label">Title (EN)</label>
                    <input type="text" name="title_en" class="form-control form-control-solid"
                        placeholder="English title" />
                    <div class="invalid-feedback d-block" data-error="title_en"></div>
                </div>
                <div class="col-md-6">
                    <label class="required form-label">Title (AR)</label>
                    <input type="text" name="title_ar" class="form-control form-control-solid"
                        placeholder="Arabic title" />
                    <div class="invalid-feedback d-block" data-error="title_ar"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('attributes.is_active') }}</label>
                    <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" />
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-3 mt-8">
                <button type="button" id="create-cancel" class="btn btn-light">Cancel</button>
                <button type="submit" id="create-submit" class="btn btn-primary">
                    <span class="indicator-label">Submit</span>
                    <span class="indicator-progress">
                        <span class="spinner-border spinner-border-sm align-middle"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
