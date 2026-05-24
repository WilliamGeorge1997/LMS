@php
    $sessionTenantId = session('admin_tenant_id');
    $superAdminRole = \Modules\Admin\Enums\Role::SUPER_ADMIN->value;
@endphp
<div class="card shadow-sm border-0">
    <div class="card-header border-0 py-4">
        <h3 class="card-title fw-bold fs-5 m-0">{{ __('admin::attributes.create_admin') }}</h3>
    </div>
    <div class="card-body pt-0">
        <form id="create-form" action="{{ url('/admin/admins') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-6">
                <div class="col-md-6">
                    <label for="create-name" class="required form-label">{{ __('admin::attributes.name') }}</label>
                    <input id="create-name" type="text" name="name" class="form-control form-control-solid"
                        placeholder="{{ __('admin::attributes.enter_name') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="name"></div>
                </div>

                <div class="col-md-6">
                    <label for="create-email" class="required form-label">{{ __('admin::attributes.email') }}</label>
                    <input id="create-email" type="text" name="email" class="form-control form-control-solid"
                        placeholder="{{ __('admin::attributes.enter_email') }}" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="email"></div>
                </div>

                <div class="col-md-6">
                    <label for="create-role" class="required form-label">{{ __('admin::attributes.role') }}</label>
                    <select id="create-role" name="role" class="form-select form-select-solid">
                        <option value="" disabled selected>{{ __('admin::attributes.select_role') }}</option>
                        @foreach (\Modules\Admin\Enums\Role::cases() as $role)
                            @if (!($sessionTenantId && $role->value == $superAdminRole))
                                <option value="{{ $role->value }}">{{ $role->value }}</option>
                            @endif
                        @endforeach

                    </select>
                    <div class="invalid-feedback d-block" data-error="role"></div>
                </div>

                <div class="col-md-6">
                    <label for="create-password"
                        class="required form-label">{{ __('admin::attributes.password') }}</label>
                    <input id="create-password" type="password" name="password" class="form-control form-control-solid"
                        placeholder="{{ __('admin::attributes.enter_password') }}" autocomplete="new-password" />
                    <div class="invalid-feedback d-block" data-error="password"></div>
                </div>

                <div class="col-md-6">
                    <label for="create-image" class="form-label">{{ __('admin::attributes.image') }}</label>
                    <input id="create-image" type="file" name="image" class="form-control form-control-solid"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" />
                    <div class="invalid-feedback d-block" data-error="image"></div>
                </div>

                <div class="col-md-6">
                    <label for="create-is-active" class="form-label">{{ __('admin::attributes.is_active') }}</label>
                    <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid mt-3">
                        <input id="create-is-active" class="form-check-input" type="checkbox" name="is_active"
                            value="1" />
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-8">
                <button type="button" id="create-cancel" class="btn btn-light">
                    {{ __('admin::attributes.cancel') }}
                </button>
                <button type="submit" id="create-submit" class="btn btn-primary">
                    <span class="indicator-label">{{ __('admin::attributes.submit') }}</span>
                    <span class="indicator-progress">
                        <span class="spinner-border spinner-border-sm align-middle"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
