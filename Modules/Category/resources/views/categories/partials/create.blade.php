@php($is_super_admin = auth('admin')->user()->hasRole(\Modules\Admin\Enums\Role::SUPER_ADMIN))
<div class="card shadow-sm border-0">
    <div class="card-header border-0 py-4">
        <h3 class="card-title fw-bold fs-5 m-0">{{ __('category::buttons.create') }}</h3>
    </div>
    <div class="card-body pt-0">
        <form id="create-form" action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-6">

                <div class="col-md-6">
                    <label class="required form-label">{{ __('category::attributes.name_en') }}</label>
                    <input type="text" name="name_en" class="form-control form-control-solid"
                        placeholder="{{ __('category::placeholders.enter_name_en') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="name_en"></div>
                </div>

                <div class="col-md-6">
                    <label class="required form-label">{{ __('category::attributes.name_ar') }}</label>
                    <input dir="rtl" type="text" name="name_ar" class="form-control form-control-solid"
                        placeholder="{{ __('category::placeholders.enter_name_ar') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="name_ar"></div>
                </div>

                @if ($is_super_admin)
                    <div class="col-md-6">
                        <label class="required form-label">{{ __('category::attributes.manager_id') }}</label>
                        <select name="manager_id" class="form-select form-select-solid">
                            <option value="" disabled selected>
                                {{ __('category::placeholders.select_manager') }}</option>
                            @foreach ($viewModel->activeManagers() as $manager)
                                <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback d-block" data-error="manager_id"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="required form-label">{{ __('category::attributes.publisher_id') }}</label>
                        <select name="publisher_id" class="form-select form-select-solid"
                            data-depends-on="manager_id" data-depends-url="/admin/managers/:value/publishers"
                            data-value-key="id" data-label-key="name"
                            data-depends-placeholder="{{ __('category::placeholders.select_publisher') }}">
                            <option value="" disabled selected>
                                {{ __('category::placeholders.select_publisher') }}</option>
                        </select>
                        <div class="invalid-feedback d-block" data-error="publisher_id"></div>
                    </div>
                @else
                    <div class="col-md-6">
                        <label class="required form-label">{{ __('category::attributes.publisher_id') }}</label>
                        <select name="publisher_id" class="form-select form-select-solid">
                            <option value="" disabled selected>
                                {{ __('category::placeholders.select_publisher') }}</option>
                            @foreach ($viewModel->activePublishers() as $publisher)
                                <option value="{{ $publisher->id }}">{{ $publisher->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback d-block" data-error="publisher_id"></div>
                    </div>
                @endif

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
