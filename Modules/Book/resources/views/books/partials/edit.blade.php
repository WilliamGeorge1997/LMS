@php
    $is_super_admin = auth('admin')->user()->hasRole(\Modules\Admin\Enums\Role::SUPER_ADMIN);
    $formId = 'edit-form-' . $book->id;
    $colspan = $is_super_admin ? 12 : 11;
@endphp

<tr class="edit-inline-row" data-edit-form-id="{{ $book->id }}">
    <form id="{{ $formId }}" action="{{ url('/admin/books/' . $book->id) }}" method="POST" class="edit-inline-form"
        enctype="multipart/form-data">
        @csrf

        <td class="w-10px pe-2">
            <div class="form-check form-check-sm form-check-custom form-check-solid">
                <input class="form-check-input row-checkbox" type="checkbox" value="{{ $book->id }}" disabled />
            </div>
        </td>

        <td>
            <input type="text" name="title_en" class="form-control form-control-solid form-control-sm"
                value="{{ $book->getTranslation('title', 'en') }}"
                placeholder="{{ __('book::placeholders.enter_title_en') }}" />
            <div class="invalid-feedback d-block" data-error="title_en"></div>
        </td>

        <td>
            <input type="text" name="title_ar" class="form-control form-control-solid form-control-sm"
                value="{{ $book->getTranslation('title', 'ar') }}"
                placeholder="{{ __('book::placeholders.enter_title_ar') }}" />
            <div class="invalid-feedback d-block" data-error="title_ar"></div>
        </td>

        <td>
            <input type="text" name="isbn" class="form-control form-control-solid form-control-sm"
                value="{{ $book->isbn }}" placeholder="{{ __('book::placeholders.enter_isbn') }}" />
            <div class="invalid-feedback d-block" data-error="isbn"></div>
        </td>

        <td>
            <select name="publisher_id" class="form-select form-select-solid form-select-sm">
                <option value="" disabled>{{ __('book::placeholders.select_publisher') }}</option>
                @foreach ($viewModel->publishersByTenant() as $publisher)
                    <option value="{{ $publisher->id }}" @selected($book->publisher_id === $publisher->id)>
                        {{ $publisher->getTranslation('name', 'en') }} -
                        {{ $publisher->getTranslation('name', 'ar') }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback d-block" data-error="publisher_id"></div>
        </td>

        <td>
            <select name="category_id" class="form-select form-select-solid form-select-sm"
                data-depends-on="publisher_id"
                data-depends-url="{{ url('/admin/categories/ajax_category') }}?publisher_id=:value"
                data-depends-placeholder="{{ __('book::placeholders.select_category') }}">
                <option value="" disabled>{{ __('book::placeholders.select_category') }}</option>
                @foreach ($viewModel->categoriesByPublisher($book->publisher_id) as $category)
                    <option value="{{ $category->id }}" @selected($book->category_id === $category->id)>
                        {{ $category->getTranslation('title', 'en') }} -
                        {{ $category->getTranslation('title', 'ar') }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback d-block" data-error="category_id"></div>
        </td>

        <td>
            <select name="level_id" class="form-select form-select-solid form-select-sm" data-depends-on="category_id"
                data-depends-url="{{ url('/admin/levels/ajax_level') }}?category_id=:value"
                data-depends-placeholder="{{ __('book::placeholders.select_level') }}">
                <option value="" disabled>{{ __('book::placeholders.select_level') }}</option>
                @foreach ($viewModel->levelsByCategory($book->category_id) as $level)
                    <option value="{{ $level->id }}" @selected($book->level_id === $level->id)>
                        {{ $level->getTranslation('title', 'en') }} - {{ $level->getTranslation('title', 'ar') }}
                    </option>
                @endforeach
            </select>
            <div class="invalid-feedback d-block" data-error="level_id"></div>
        </td>

        @if ($is_super_admin)
            <td>
                <span class="text-gray-700 fw-semibold">
                    {{ $book->tenant?->getTranslation('name', 'en') ?? '' }}
                    - {{ $book->tenant?->getTranslation('name', 'ar') ?? '' }}
                </span>
            </td>
        @endif

        <td>
            <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                    {{ $book->is_active ? 'checked' : '' }} />
            </label>
        </td>

        <td>
            <span class="text-gray-700 fw-semibold">{{ $book->created_at }}</span>
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

<tr class="edit-extra-row" data-extra-edit-form-for="{{ $book->id }}">
    <td colspan="{{ $colspan }}" class="p-0 border-0">
        <div class="additional-fields-panel rounded-2 p-5 m-2">
            <h6 class="fw-bold text-gray-800 mb-4">{{ __('book::attributes.description') }}</h6>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">{{ __('book::attributes.description_en') }}</label>
                    <textarea name="description_en" rows="3" class="form-control form-control-solid form-control-sm"
                        placeholder="{{ __('book::placeholders.enter_description_en') }}">{{ $book->getTranslation('description', 'en') }}</textarea>
                    <div class="invalid-feedback d-block" data-error="description_en"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('book::attributes.description_ar') }}</label>
                    <textarea name="description_ar" rows="3" class="form-control form-control-solid form-control-sm"
                        placeholder="{{ __('book::placeholders.enter_description_ar') }}">{{ $book->getTranslation('description', 'ar') }}</textarea>
                    <div class="invalid-feedback d-block" data-error="description_ar"></div>
                </div>
            </div>
        </div>
    </td>
</tr>
