@php
    $is_super_admin = auth('admin')->user()->hasRole(\Modules\Admin\Enums\Role::SUPER_ADMIN);
    $formId = 'edit-form-' . $publisher->id;
@endphp

<tr class="edit-inline-row" data-edit-form-id="{{ $publisher->id }}">
    <td class="d-none" aria-hidden="true">
        <form id="{{ $formId }}" action="{{ url('/admin/publishers/' . $publisher->id) }}" method="POST"
            class="edit-inline-form">
            @csrf
        </form>
    </td>

    <td class="w-10px pe-2">
        <div class="form-check form-check-sm form-check-custom form-check-solid">
            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $publisher->id }}" disabled />
        </div>
    </td>

    <td>
        <input form="{{ $formId }}" type="text" name="name_en" class="form-control form-control-solid form-control-sm"
            value="{{ $publisher->getTranslation('name', 'en') }}" />
        <div class="invalid-feedback d-block" data-error="name_en"></div>
    </td>

    <td>
        <input form="{{ $formId }}" type="text" name="name_ar" class="form-control form-control-solid form-control-sm"
            value="{{ $publisher->getTranslation('name', 'ar') }}" />
        <div class="invalid-feedback d-block" data-error="name_ar"></div>
    </td>

    <td>
        <span class="text-gray-700 fw-semibold">{{ $publisher->tenant?->getTranslation('name', 'en') ?? '' }}
            - {{ $publisher->tenant?->getTranslation('name', 'ar') ?? '' }}</span>
    </td>

    <td>
        <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
            <input form="{{ $formId }}" class="form-check-input" type="checkbox" name="is_active" value="1"
                {{ $publisher->is_active ? 'checked' : '' }} />
        </label>
    </td>

    <td>
        <span class="text-gray-700 fw-semibold">{{ $publisher->created_at }}</span>
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
</tr>
