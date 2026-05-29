@php($is_super_admin = auth('admin')->user()->hasRole(\Modules\Admin\Enums\Role::SUPER_ADMIN))

<tr class="edit-inline-row" data-edit-form-id="{{ $category->id }}">
    <form id="edit-form-{{ $category->id }}" action="{{ url('/admin/categories/' . $category->id) }}" method="POST"
        class="edit-inline-form" enctype="multipart/form-data">
        @csrf

        <td class="w-10px pe-2">
            <div class="form-check form-check-sm form-check-custom form-check-solid">
                <input class="form-check-input row-checkbox" type="checkbox" value="{{ $category->id }}" disabled />
            </div>
        </td>

        <td>
            <input type="text" name="title_en" class="form-control form-control-solid form-control-sm"
                value="{{ $category->getTranslation('title', 'en') }}"
                placeholder="{{ __('category::placeholders.enter_title_en') }}" />
            <div class="invalid-feedback d-block" data-error="title_en"></div>
        </td>

        <td>
            <input type="text" name="title_ar" class="form-control form-control-solid form-control-sm"
                value="{{ $category->getTranslation('title', 'ar') }}"
                placeholder="{{ __('category::placeholders.enter_title_ar') }}" />
            <div class="invalid-feedback d-block" data-error="title_ar"></div>
        </td>

        <td>
            <select name="publisher_id" class="form-select form-select-solid form-select-sm">
                <option value="" disabled>{{ __('category::placeholders.select_publisher') }}</option>
                @foreach ($viewModel->publishersByTenant() as $publisher)
                    <option value="{{ $publisher->id }}" @selected($category->publisher_id === $publisher->id)>
                        {{ $publisher->getTranslation('name', 'en') }} - {{ $publisher->getTranslation('name', 'ar') }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback d-block" data-error="publisher_id"></div>
        </td>

        @if ($is_super_admin)
            <td>
                <span class="text-gray-700 fw-semibold">
                    {{ $category->tenant?->getTranslation('name', 'en') ?? '' }}
                    - {{ $category->tenant?->getTranslation('name', 'ar') ?? '' }}
                </span>
            </td>
        @endif

        <td>
            <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                    {{ $category->is_active ? 'checked' : '' }} />
            </label>
        </td>

        <td>
            <span class="text-gray-700 fw-semibold">{{ $category->created_at }}</span>
        </td>

        <td class="text-end">
            <button type="submit" form="edit-form-{{ $category->id }}"
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
