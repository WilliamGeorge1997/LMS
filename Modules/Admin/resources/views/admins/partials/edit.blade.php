@php
    $formId = 'edit-form-' . $admin->id;
    $currentRole = $admin->roles->first()?->name;
@endphp

<tr class="edit-inline-row" data-edit-form-id="{{ $admin->id }}">
    <form id="{{ $formId }}" action="{{ url('/admin/admins/'.$admin->id) }}" method="POST" class="edit-inline-form"
        enctype="multipart/form-data">
        @csrf

        <td class="w-10px pe-2">
            <div class="form-check form-check-sm form-check-custom form-check-solid">
                <input class="form-check-input row-checkbox" type="checkbox" value="{{ $admin->id }}" disabled />
            </div>
        </td>

        <td>
            <input type="text" name="name" class="form-control form-control-solid form-control-sm"
                value="{{ $admin->name }}" />
            <div class="invalid-feedback d-block" data-error="name"></div>
        </td>

        <td>
            <input type="text" name="email" class="form-control form-control-solid form-control-sm"
                value="{{ $admin->email }}" />
            <div class="invalid-feedback d-block" data-error="email"></div>
        </td>

        <td>
            <select name="role" class="form-select form-select-solid form-select-sm">
                @foreach (\Modules\Admin\Enums\Role::cases() as $role)
                    <option value="{{ $role->value }}" @selected($currentRole === $role->value)>{{ $role->value }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback d-block" data-error="role"></div>
        </td>

        <td>
            <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                    {{ $admin->is_active ? 'checked' : '' }} />
            </label>
        </td>

        <td>
            <span class="text-gray-700 fw-semibold">{{ $admin->created_at }}</span>
        </td>

        <td class="text-end">
            <button type="submit" form="{{ $formId }}" class="btn btn-icon btn-sm btn-light-success save-btn">
                <span class="indicator-label">
                    <i class="ki-duotone ki-check fs-4"><span class="path1"></span><span class="path2"></span></i>
                </span>
                <span class="indicator-progress">
                    <span class="spinner-border spinner-border-sm"></span>
                </span>
            </button>
            <button type="button" class="btn btn-icon btn-sm btn-light-danger cancel-btn">
                <i class="ki-duotone ki-cross fs-4"><span class="path1"></span><span class="path2"></span></i>
            </button>
        </td>
    </form>
</tr>

<tr class="edit-extra-row" data-extra-edit-form-for="{{ $admin->id }}">
    <td colspan="8" class="p-0 border-0">
        <div class="additional-fields-panel rounded-2 p-5 m-2">
            <h6 class="fw-bold text-gray-800 mb-4">Additional Fields</h6>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label"
                        for="password">{{ __('admin::attributes.password') }}</label>
                    <input id="password" type="password" name="password"
                        class="form-control form-control-solid form-control-sm"
                        placeholder="{{ __('admin::attributes.leave_blank_to_keep_current') }}"
                        autocomplete="new-password" />
                    <div class="invalid-feedback d-block" data-error="password"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label"
                        for="image">{{ __('admin::attributes.image') }}</label>
                    <input id="image" type="file" name="image"
                        class="form-control form-control-solid form-control-sm"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" />
                    <div class="invalid-feedback d-block" data-error="image"></div>
                </div>
            </div>
        </div>
    </td>
</tr>
