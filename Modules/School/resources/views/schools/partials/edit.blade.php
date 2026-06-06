@php($formId = 'edit-form-' . $school->id)

<tr class="edit-inline-row" data-edit-form-id="{{ $school->id }}">
    <td class="d-none" aria-hidden="true">
        <form id="{{ $formId }}" action="{{ url('/admin/schools/' . $school->id) }}" method="POST"
            class="edit-inline-form">
            @csrf
        </form>
    </td>

    <td class="w-10px pe-2">
        <div class="form-check form-check-sm form-check-custom form-check-solid">
            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $school->id }}" disabled />
        </div>
    </td>

    <td>
        <input form="{{ $formId }}" type="text" name="title_en"
            class="form-control form-control-solid form-control-sm"
            value="{{ $school->getTranslation('title', 'en') }}"
            placeholder="{{ __('school::placeholders.enter_title_en') }}" />
        <div class="invalid-feedback d-block" data-error="title_en"></div>
    </td>

    <td>
        <input form="{{ $formId }}" type="text" name="title_ar"
            class="form-control form-control-solid form-control-sm"
            value="{{ $school->getTranslation('title', 'ar') }}"
            placeholder="{{ __('school::placeholders.enter_title_ar') }}" />
        <div class="invalid-feedback d-block" data-error="title_ar"></div>
    </td>

    <td>
        <input form="{{ $formId }}" type="text" name="phone"
            class="form-control form-control-solid form-control-sm" value="{{ $school->phone }}"
            placeholder="{{ __('school::placeholders.enter_phone') }}" />
        <div class="invalid-feedback d-block" data-error="phone"></div>
    </td>

    <td>
        <input form="{{ $formId }}" type="email" name="email"
            class="form-control form-control-solid form-control-sm" value="{{ $school->email }}"
            placeholder="{{ __('school::placeholders.enter_email') }}" />
        <div class="invalid-feedback d-block" data-error="email"></div>
    </td>

    <td>
        <select form="{{ $formId }}" name="country_id" class="form-select form-select-solid form-select-sm">
            <option value="" disabled>{{ __('school::placeholders.select_country') }}</option>
            @foreach ($viewModel->countries() as $country)
                <option value="{{ $country->id }}" @selected($school->country_id === $country->id)>
                    {{ $country->getTranslation('title', 'en') }} - {{ $country->getTranslation('title', 'ar') }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback d-block" data-error="country_id"></div>
    </td>

    <td>
        <select form="{{ $formId }}" name="city_id" class="form-select form-select-solid form-select-sm"
            data-depends-on="country_id"
            data-depends-url="{{ url('/admin/schools/ajax_city') }}?country_id=:value"
            data-depends-placeholder="{{ __('school::placeholders.select_city') }}">
            <option value="" disabled>{{ __('school::placeholders.select_city') }}</option>
            @if ($school->city)
                <option value="{{ $school->city_id }}" selected>
                    {{ $school->city->getTranslation('title', 'en') }} - {{ $school->city->getTranslation('title', 'ar') }}
                </option>
            @endif
        </select>
        <div class="invalid-feedback d-block" data-error="city_id"></div>
    </td>

    <td>
        <select form="{{ $formId }}" name="region_id" class="form-select form-select-solid form-select-sm"
            data-depends-on="city_id"
            data-depends-url="{{ url('/admin/schools/ajax_region') }}?city_id=:value"
            data-depends-placeholder="{{ __('school::placeholders.select_region') }}">
            <option value="" disabled>{{ __('school::placeholders.select_region') }}</option>
            @if ($school->region)
                <option value="{{ $school->region_id }}" selected>
                    {{ $school->region->getTranslation('title', 'en') }} - {{ $school->region->getTranslation('title', 'ar') }}
                </option>
            @endif
        </select>
        <div class="invalid-feedback d-block" data-error="region_id"></div>
    </td>

    @if (auth('admin')->user()->hasRole(\Modules\Admin\Enums\Role::SUPER_ADMIN))
        <td>
            <span class="fw-bold text-gray-800">
                {{ $school->tenant?->getTranslation('name', 'en') }} - {{ $school->tenant?->getTranslation('name', 'ar') }}
            </span>
        </td>
    @endif

    <td>
        <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
            <input form="{{ $formId }}" class="form-check-input" type="checkbox" name="is_active" value="1"
                {{ $school->is_active ? 'checked' : '' }} />
        </label>
    </td>

    <td>
        <span class="text-gray-700 fw-semibold">{{ $school->created_at }}</span>
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
