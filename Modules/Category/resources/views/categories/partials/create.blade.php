<div class="card shadow-sm border-0">
    <div class="card-header border-0 py-4">
        <h3 class="card-title fw-bold fs-5 m-0">{{ __('category::buttons.create') }}</h3>
    </div>
    <div class="card-body pt-0">
        <form id="create-form" action="{{ url('/admin/categories') }}" method="POST">
            @csrf
            <div class="row g-6">

                {{-- Title EN --}}
                <div class="col-md-6">
                    <label class="required form-label">{{ __('category::attributes.title_en') }}</label>
                    <input type="text" name="title_en" class="form-control form-control-solid"
                        placeholder="{{ __('category::placeholders.enter_title_en') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="title_en"></div>
                </div>

                {{-- Title AR --}}
                <div class="col-md-6">
                    <label class="required form-label">{{ __('category::attributes.title_ar') }}</label>
                    <input type="text" name="title_ar" class="form-control form-control-solid"
                        placeholder="{{ __('category::placeholders.enter_title_ar') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="title_ar"></div>
                </div>


                {{-- Publisher --}}
                <div class="col-md-6">
                    <label class="required form-label">{{ __('category::attributes.publisher_id') }}</label>
                    <select name="publisher_id" class="form-select form-select-solid">
                        <option value="" disabled selected>
                            {{ __('category::placeholders.select_publisher') }}</option>
                        @foreach ($viewModel->publishersByTenant() as $publisher)
                            <option value="{{ $publisher->id }}">{{ $publisher->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback d-block" data-error="publisher_id"></div>
                </div>

                {{-- Is Active --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('category::attributes.is_active') }}</label>
                    <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" />
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end gap-3 mt-8">
                <button type="button" id="create-cancel" class="btn btn-light">
                    {{ __('category::buttons.cancel') }}
                </button>
                <button type="submit" id="create-submit" class="btn btn-primary">
                    <span class="indicator-label">{{ __('category::buttons.submit') }}</span>
                    <span class="indicator-progress">
                        <span class="spinner-border spinner-border-sm align-middle"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
