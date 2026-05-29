<tr class="edit-inline-row" data-edit-form-id="{{ $city->id }}">
    <form id="edit-form-{{ $city->id }}" action="{{ url('/admin/cities/' . $city->id) }}" method="POST"
        class="edit-inline-form">
        @csrf

        <td class="w-10px pe-2">
            <div class="form-check form-check-sm form-check-custom form-check-solid">
                <input class="form-check-input row-checkbox" type="checkbox" value="{{ $city->id }}" disabled />
            </div>
        </td>

        <td>
            <input type="text" name="title_en" class="form-control form-control-solid form-control-sm"
                value="{{ $city->getTranslation('title', 'en') }}" />
            <div class="invalid-feedback d-block" data-error="title_en"></div>
        </td>

        <td>
            <input type="text" name="title_ar" class="form-control form-control-solid form-control-sm"
                value="{{ $city->getTranslation('title', 'ar') }}" />
            <div class="invalid-feedback d-block" data-error="title_ar"></div>
        </td>

        <td>
            <select name="country_id" class="form-select form-select-solid form-select-sm">
                <option value="" disabled>Select country</option>
                @foreach ($countryOptions as $opt)
                    <option value="{{ $opt['value'] }}" @selected($city->country_id === $opt['value'])>
                        {{ $opt['label'] }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback d-block" data-error="country_id"></div>
        </td>

        <td>
            <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                    {{ $city->is_active ? 'checked' : '' }} />
            </label>
        </td>

        <td>
            <span class="text-gray-700 fw-semibold">{{ $city->created_at }}</span>
        </td>

        <td class="text-end">
            <button type="submit" form="edit-form-{{ $city->id }}"
                class="btn btn-icon btn-sm btn-light-success save-btn">
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
