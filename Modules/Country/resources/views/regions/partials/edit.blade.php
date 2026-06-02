@php
    $countryId = $region->city?->country_id;
    $formId = 'edit-form-' . $region->id;
@endphp

<tr class="edit-inline-row" data-edit-form-id="{{ $region->id }}">
    <td class="d-none" aria-hidden="true">
        <form id="{{ $formId }}" action="{{ url('/admin/regions/' . $region->id) }}" method="POST"
            class="edit-inline-form">
            @csrf
        </form>
    </td>

    <td class="w-10px pe-2">
        <div class="form-check form-check-sm form-check-custom form-check-solid">
            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $region->id }}" disabled />
        </div>
    </td>

    <td>
        <input form="{{ $formId }}" type="text" name="title_en"
            class="form-control form-control-solid form-control-sm"
            value="{{ $region->getTranslation('title', 'en') }}" />
        <div class="invalid-feedback d-block" data-error="title_en"></div>
    </td>

    <td>
        <input form="{{ $formId }}" type="text" name="title_ar"
            class="form-control form-control-solid form-control-sm"
            value="{{ $region->getTranslation('title', 'ar') }}" />
        <div class="invalid-feedback d-block" data-error="title_ar"></div>
    </td>

    <td>
        <select form="{{ $formId }}" name="country_id" class="form-select form-select-solid form-select-sm">
            <option value="" disabled>Select country</option>
            @foreach ($countryOptions as $opt)
                <option value="{{ $opt['value'] }}" @selected($countryId === $opt['value'])>
                    {{ $opt['label'] }}
                </option>
            @endforeach
        </select>
        <div class="invalid-feedback d-block" data-error="country_id"></div>
    </td>

    <td>
        <select form="{{ $formId }}" name="city_id" class="form-select form-select-solid form-select-sm"
            data-depends-on="country_id"
            data-depends-url="{{ url('/admin/cities/select-options') }}?country_id=:value"
            data-depends-placeholder="Select city">
            <option value="" disabled>Select city</option>
            @if ($region->city)
                <option value="{{ $region->city_id }}" selected>{{ $region->city->title }}</option>
            @endif
        </select>
        <div class="invalid-feedback d-block" data-error="city_id"></div>
    </td>

    <td>
        <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
            <input form="{{ $formId }}" class="form-check-input" type="checkbox" name="is_active" value="1"
                {{ $region->is_active ? 'checked' : '' }} />
        </label>
    </td>

    <td>
        <span class="text-gray-700 fw-semibold">{{ $region->created_at }}</span>
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
