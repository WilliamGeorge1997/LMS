@php($is_super_admin = auth('admin')->user()->hasRole(\Modules\Admin\Enums\Role::SUPER_ADMIN))

<tr class="edit-inline-row" data-edit-form-id="{{ $category->id }}">
    <form id="edit-form-{{ $category->id }}" action="{{ route('categories.update', $category) }}" method="POST"
        class="edit-inline-form" enctype="multipart/form-data">
        @csrf

        <td class="w-10px pe-2">
            <div class="form-check form-check-sm form-check-custom form-check-solid">
                <input class="form-check-input row-checkbox" type="checkbox" value="{{ $category->id }}" disabled />
            </div>
        </td>

        <td>
            <input type="text" name="name_en" class="form-control form-control-solid form-control-sm"
                value="{{ $category->getTranslation('title', 'en') }}"
                placeholder="{{ __('category::placeholders.enter_name_en') }}" />
            <div class="invalid-feedback d-block" data-error="name_en"></div>
        </td>

        <td>
            <select name="publisher_id" class="form-select form-select-solid form-select-sm"
                @if ($is_super_admin) data-depends-on="manager_id" data-depends-url="/admin/managers/:value/publishers"
                data-value-key="id" data-label-key="name"
                data-depends-placeholder="{{ __('category::placeholders.select_publisher') }}" @endif>
                <option value="" disabled>{{ __('category::placeholders.select_publisher') }}</option>
                @if (! $is_super_admin)
                    @foreach ($viewModel->activePublishers() as $publisher)
                        <option value="{{ $publisher->id }}" @selected($category->publisher_id === $publisher->id)>
                            {{ $publisher->name }}
                        </option>
                    @endforeach
                @else
                    @if ($category->publisher)
                        <option value="{{ $category->publisher_id }}" selected>{{ $category->publisher->name }}</option>
                    @endif
                @endif
            </select>
            <div class="invalid-feedback d-block" data-error="publisher_id"></div>
        </td>

        @if ($is_super_admin)
            <td>
                <select name="manager_id" class="form-select form-select-solid form-select-sm">
                    <option value="" disabled>{{ __('category::placeholders.select_manager') }}</option>
                    @foreach ($viewModel->activeManagers() as $manager)
                        <option value="{{ $manager->id }}" @selected(($category->manager_id ?? null) === $manager->id)>
                            {{ $manager->name }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback d-block" data-error="manager_id"></div>
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

<tr class="edit-extra-row" data-extra-edit-form-for="{{ $category->id }}">
    <td colspan="{{ $is_super_admin ? 8 : 7 }}" class="p-0 border-0">
        <div class="additional-fields-panel rounded-2 p-5 m-2">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">{{ __('category::attributes.name_ar') }}</label>
                    <input dir="rtl" type="text" name="name_ar" class="form-control form-control-solid form-control-sm"
                        value="{{ $category->getTranslation('title', 'ar') }}"
                        placeholder="{{ __('category::placeholders.enter_name_ar') }}" />
                    <div class="invalid-feedback d-block" data-error="name_ar"></div>
                </div>
            </div>
        </div>
    </td>
</tr>
