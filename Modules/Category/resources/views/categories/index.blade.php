@extends('common::layouts.master')
@section('title', config('app.name') . ' - ' . __('category::attributes.categories_list'))

@section('css')
    @if (app()->getLocale() === 'ar')
        <link href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.rtl.css') }}" rel="stylesheet"
            type="text/css" />
    @else
        <link href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
            type="text/css" />
    @endif
@endsection

@php($is_super_admin = auth('admin')->user()->hasRole(\Modules\Admin\Enums\Role::SUPER_ADMIN))

@section('toolbar')
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack my-3">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                {{ __('category::attributes.categories') }}
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="index.html"
                        class="text-muted text-hover-primary">{{ __('category::attributes.categories') }}</a>
                </li>
                <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                <li class="breadcrumb-item text-muted">{{ __('category::attributes.categories') }}</li>
            </ul>
        </div>

        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="#create-collapse" id="create-toggle" class="btn btn-sm fw-bold btn-primary" data-bs-toggle="collapse"
                aria-expanded="false" aria-controls="create-collapse">
                <span id="create-toggle-label">+ {{ __('category::buttons.create') }}</span>
            </a>
        </div>
    </div>

    <div class="app-container container-fluid mb-7">
        <div id="create-collapse" class="accordion-collapse collapse">
            <div class="card shadow-sm border-0">
                <div class="card-header border-0 py-4">
                    <h3 class="card-title fw-bold fs-5 m-0">{{ __('category::buttons.create') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <form id="create-form" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="row g-6">

                            {{-- Name AR --}}
                            <div class="col-md-6">
                                <label class="required form-label">{{ __('category::attributes.name_en') }}</label>
                                <input type="text" name="name_en" class="form-control form-control-solid"
                                    placeholder="{{ __('category::placeholders.enter_name_en') }}" autocomplete="off"
                                    data-rule-required data-rule-max="255"
                                    data-msg-required="{{ __('category::validations.name_en_required') }}"
                                    data-msg-max="{{ __('category::validations.name_max') }}" />
                                <div class="invalid-feedback d-block" data-field-error="name_en"></div>
                            </div>

                            {{-- Name EN --}}
                            <div class="col-md-6">
                                <label class="required form-label">{{ __('category::attributes.name_ar') }}</label>
                                <input dir="rtl" type="text" name="name_ar" class="form-control form-control-solid"
                                    placeholder="{{ __('category::placeholders.enter_name_ar') }}" autocomplete="off"
                                    data-rule-required data-rule-max="255"
                                    data-msg-required="{{ __('category::validations.name_ar_required') }}"
                                    data-msg-max="{{ __('category::validations.name_max') }}" />
                                <div class="invalid-feedback d-block" data-field-error="name_ar"></div>
                            </div>

                            @if ($is_super_admin)
                                {{-- Manager (super admin only) --}}
                                <div class="col-md-6">
                                    <label class="required form-label">{{ __('category::attributes.manager_id') }}</label>
                                    <select name="manager_id" class="form-select form-select-solid" data-rule-required
                                        data-msg-required="{{ __('category::validations.manager_required') }}">
                                        <option value="" disabled selected>
                                            {{ __('category::placeholders.select_manager') }}</option>
                                        @foreach ($viewModel->activeManagers() as $manager)
                                            <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback d-block" data-field-error="manager_id"></div>
                                </div>

                                {{-- Publisher — depends on manager (super admin) --}}
                                <div class="col-md-6">
                                    <label
                                        class="required form-label">{{ __('category::attributes.publisher_id') }}</label>
                                    <select name="publisher_id" class="form-select form-select-solid"
                                        data-depends-on="manager_id" data-depends-url="/admin/managers/:value/publishers"
                                        data-value-key="id" data-label-key="name"
                                        data-depends-placeholder="{{ __('category::placeholders.select_publisher') }}"
                                        data-rule-required
                                        data-msg-required="{{ __('category::validations.publisher_required') }}">
                                        <option value="" disabled selected>
                                            {{ __('category::placeholders.select_publisher') }}</option>
                                    </select>
                                    <div class="invalid-feedback d-block" data-field-error="publisher_id"></div>
                                </div>
                            @else
                                {{-- Publisher — static, scoped to manager (manager role) --}}
                                <div class="col-md-6">
                                    <label
                                        class="required form-label">{{ __('category::attributes.publisher_id') }}</label>
                                    <select name="publisher_id" class="form-select form-select-solid" data-rule-required
                                        data-msg-required="{{ __('category::validations.publisher_required') }}">
                                        <option value="" disabled selected>
                                            {{ __('category::placeholders.select_publisher') }}</option>
                                        @foreach ($viewModel->activePublishers() as $publisher)
                                            <option value="{{ $publisher->id }}">{{ $publisher->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback d-block" data-field-error="publisher_id"></div>
                                </div>
                            @endif

                            {{-- Is Active --}}
                            <div class="col-md-6">
                                <label class="form-label">{{ __('category::attributes.is_active') }}</label>
                                <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" />
                                </div>
                                <div class="invalid-feedback d-block" data-field-error="is_active"></div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end gap-3 mt-8">
                            <button type="button" id="create-cancel"
                                class="btn btn-light">{{ __('category::buttons.cancel') }}</button>
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
        </div>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <input type="text" data-kt-docs-table-filter="search"
                        class="form-control form-control-solid w-250px ps-13"
                        placeholder="{{ __('category::placeholders.search_categories') }}" />
                </div>
            </div>
        </div>

        <div class="card-body py-4">
            <table id="kt_datatable" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th></th>
                        <th class="w-10px pe-2">
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                    data-kt-check-target="#kt_datatable .row-checkbox" value="1" />
                            </div>
                        </th>
                        <th class="min-w-175px">{{ __('category::attributes.name') }}</th>
                        <th class="min-w-125px">{{ __('category::attributes.publisher_id') }}</th>
                        @if ($is_super_admin)
                            <th class="min-w-125px">{{ __('category::attributes.manager_id') }}</th>
                        @endif
                        <th class="min-w-125px">{{ __('category::attributes.is_active') }}</th>
                        <th class="min-w-125px">{{ __('category::attributes.created_at') }}</th>
                        <th class="text-end min-w-100px">{{ __('category::attributes.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold"></tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/ajax.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/notify.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/validator.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/input-builder.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/dependent-dropdown.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/create-plugin.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/edit-plugin.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/toggle-plugin.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/delete-plugin.js') }}"></script>

    <script>
        "use strict";

        KTUtil.onDOMContentLoaded(function() {

            // ── 1. Datatable ────────────────────────────────────────────────────
            var esc = PluginInputBuilder.escapeHtml;

            var dt = $("#kt_datatable").DataTable({
                searchDelay: 500,
                processing: true,
                serverSide: true,
                stateSave: false,
                pageLength: 10,
                lengthMenu: [10, 25, 50],
                order: [
                    [0, "desc"]
                ],
                ajax: {
                    url: "{{ route('categories.index') }}"
                },
                columns: [{
                        data: "id"
                    },
                    {
                        data: "id"
                    },
                    {
                        data: "name_display"
                    },
                    {
                        data: "publisher.name"
                    },
                    @if ($is_super_admin)
                        {
                            data: "manager.name"
                        },
                    @endif {
                        data: "is_active"
                    },
                    {
                        data: "created_at"
                    },
                    {
                        data: null
                    },
                ],
                columnDefs: [{
                        targets: 0,
                        visible: false,
                        orderable: true,
                        searchable: false
                    },
                    {
                        targets: 1,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input row-checkbox" type="checkbox" value="${esc(data)}" />
                                    </div>`;
                        }
                    },
                    {
                        targets: 2,
                        orderable: false,
                        searchable: true,
                        render: function(data) {
                            return `<span class="text-gray-800 fw-bold fs-6">${esc(data || '')}</span>`;
                        }
                    },
                    {
                        targets: 3,
                        orderable: false,
                        searchable: true,
                        render: function(data) {
                            return `<span class="fw-bold text-gray-800">${esc(data || '')}</span>`;
                        }
                    },
                    @if ($is_super_admin)
                        {
                            targets: 4,
                            orderable: false,
                            searchable: true,
                            render: function(data) {
                                return `<span class="fw-bold text-gray-800">${esc(data || '')}</span>`;
                            }
                        },
                    @endif {
                        targets: -3,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var checked = data ? "checked" : "";
                            return `<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input active-toggle" type="checkbox" ${checked} data-id="${esc(row.id)}" />
                                    </label>`;
                        }
                    },
                    {
                        targets: -2,
                        orderable: true,
                        searchable: false,
                        render: function(data) {
                            return `<span class="text-gray-700 fw-semibold">${esc(data || '')}</span>`;
                        }
                    },
                    {
                        targets: -1,
                        orderable: false,
                        searchable: false,
                        className: "text-end",
                        render: function(data, type, row) {
                            var id = esc(row.id);
                            return `<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-btn"
                                        title="Edit" data-id="${id}">
                                        <i class="ki-duotone ki-pencil fs-5"></i>
                                    </a>
                                    <button type="button"
                                        class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete-btn"
                                        title="Delete" data-id="${id}">
                                        <i class="ki-duotone ki-trash fs-5"></i>
                                    </button>`;
                        }
                    }
                ]
            });

            // ── 2. Search ───────────────────────────────────────────────────────
            var searchTimer;
            $('[data-kt-docs-table-filter="search"]').on("input", function() {
                var value = this.value;
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    dt.search(value).draw();
                }, 400);
            });

            // ── 3. Create Plugin ────────────────────────────────────────────────
            CreatePlugin.init({
                storeUrl: "{{ route('categories.store') }}",
                datatable: dt,
                labels: {
                    create: "+ {{ __('category::buttons.create') }}",
                    cancel: "× {{ __('category::buttons.cancel') }}"
                }
            });

            @if ($is_super_admin)
                PluginDependentDropdown.bind($("#create-form"));
            @endif

            // ── 4. Edit Plugin ──────────────────────────────────────────────────
            EditPlugin.init({
                updateUrl: "{{ route('categories.update', ['category' => ':id']) }}",
                datatable: dt,
                selector: ".edit-btn",

                columns: [{
                        field: "name_ar",
                        type: "text",
                        target: 2,
                        placeholder: "{{ __('category::placeholders.enter_name_ar') }}",
                        rules: {
                            required: true,
                            max: 255
                        },
                        messages: {
                            required: "{{ __('category::validations.name_ar_required') }}",
                            max: "{{ __('category::validations.name_max') }}",
                        },
                    },
                    {
                        field: "name_en",
                        type: "text",
                        target: 2,
                        placeholder: "{{ __('category::placeholders.enter_name_en') }}",
                        rules: {
                            required: true,
                            max: 255
                        },
                        messages: {
                            required: "{{ __('category::validations.name_en_required') }}",
                            max: "{{ __('category::validations.name_max') }}",
                        },
                    },
                    @if ($is_super_admin)
                        {
                            field: "manager_id",
                            type: "select",
                            target: 4,
                            valueKey: "manager.id",
                            options: [
                                @foreach ($viewModel->activeManagers() as $manager)
                                    {
                                        value: {{ $manager->id }},
                                        label: "{{ $manager->name }}"
                                    },
                                @endforeach
                            ],
                            rules: {
                                required: true
                            },
                            messages: {
                                required: "{{ __('category::validations.manager_required') }}",
                            },
                        },
                    @endif {
                        field: "publisher_id",
                        type: "select",
                        target: 3,
                        valueKey: "publisher.id",
                        @if ($is_super_admin)
                            options: [],
                            attrs: {
                                "data-depends-on": "manager_id",
                                "data-depends-url": "/admin/managers/:value/publishers",
                                "data-value-key": "id",
                                "data-label-key": "name",
                                "data-depends-placeholder": "{{ __('category::placeholders.select_publisher') }}",
                            },
                        @else
                            options: [
                                @foreach ($viewModel->activePublishers() as $publisher)
                                    {
                                        value: {{ $publisher->id }},
                                        label: "{{ $publisher->name }}"
                                    },
                                @endforeach
                            ],
                        @endif
                        rules: {
                            required: true
                        },
                        messages: {
                            required: "{{ __('category::validations.publisher_required') }}",
                        },
                    },
                    {
                        field: "is_active",
                        type: "toggle",
                        target: -3,
                    },
                ],

                mapRow: function(response) {
                    return response.data;
                },

                notifications: {
                    successTitle: "{{ __('category::messages.updated') }}",
                    successText: "{{ __('category::messages.updated_successfully') }}",
                    errorTitle: "{{ __('category::messages.error') }}",
                    errorText: "{{ __('category::messages.something_went_wrong') }}",
                },
            });

            // ── 5. Toggle Plugin ────────────────────────────────────────────────
            TogglePlugin.init({
                toggleUrl: "{{ route('categories.toggle-activate', ['category' => ':id']) }}",
                selector: ".active-toggle"
            });

            // ── 6. Delete Plugin ────────────────────────────────────────────────
            DeletePlugin.init({
                deleteUrl: "{{ route('categories.destroy', ['category' => ':id']) }}",
                datatable: dt,
                selector: ".delete-btn"
            });
        });
    </script>
@endsection
