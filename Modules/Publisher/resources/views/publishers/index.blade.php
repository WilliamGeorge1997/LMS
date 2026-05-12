@extends('common::layouts.master')
@section('title', config('app.name') . ' - ' . __('publisher::attributes.publishers_list'))

@section('css')
    @if (app()->getLocale() === 'ar')
        <link href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.rtl.css') }}" rel="stylesheet"
            type="text/css" />
    @else
        <link href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
            type="text/css" />
    @endif



    @yield('css')

@endsection
@php($is_super_admin = auth('admin')->user()->hasRole(\Modules\Admin\Enums\Role::SUPER_ADMIN));

{{-- ============================================================
     TOOLBAR — page title + create button + create form
     ============================================================ --}}
@section('toolbar')
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack my-3">

        {{-- Page title & breadcrumb --}}
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                {{ __('publisher::attributes.publishers') }}
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="index.html"
                        class="text-muted text-hover-primary">{{ __('publisher::attributes.publishers') }}</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">{{ __('publisher::attributes.publishers') }}</li>
            </ul>
        </div>

        {{-- Create toggle button --}}
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="#create-collapse" id="create-toggle" class="btn btn-sm fw-bold btn-primary" data-bs-toggle="collapse"
                aria-expanded="false" aria-controls="create-collapse">
                <span id="create-toggle-label">+ {{ __('publisher::buttons.create') }}</span>
            </a>
        </div>

    </div>

    {{-- Create form accordion --}}
    <div class="app-container container-fluid mb-7">
        <div id="create-collapse" class="accordion-collapse collapse">
            <div class="card shadow-sm border-0">
                <div class="card-header border-0 py-4">
                    <h3 class="card-title fw-bold fs-5 m-0">{{ __('publisher::buttons.create') }}</h3>
                </div>
                <div class="card-body pt-0">

                    <form id="create-form" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="row g-6">

                            {{-- Name --}}
                            <div class="col-md-6">
                                <label class="required form-label">{{ __('publisher::attributes.name') }}</label>
                                <input type="text" name="name" class="form-control form-control-solid"
                                    placeholder="{{ __('publisher::placeholders.enter_name') }}" autocomplete="off"
                                    data-rule-required data-rule-max="255" data-msg-required="Name is required."
                                    data-msg-max="Name must not exceed 255 characters." />
                                <div class="invalid-feedback d-block" data-field-error="name"></div>
                            </div>

                            @if ($is_super_admin)

                                {{-- Managers --}}
                                <div class="col-md-6">
                                    <label class="required form-label">{{ __('publisher::attributes.manager_id') }}</label>
                                    <select name="manager_id" class="form-select form-select-solid" data-rule-required
                                        data-msg-required="Please select a manager.">
                                        <option value="" disabled selected>
                                            {{ __('publisher::attributes.select_manager') }}
                                        </option>
                                        @foreach ($viewModel->activeManagers() as $manager)
                                            <option value="{{ $manager->id }}">
                                                {{ $manager->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback d-block" data-field-error="manager_id"></div>
                                </div>

                            @endif



                            {{-- Is Active (optional, no validation needed) --}}
                            <div class="col-md-6">
                                <label class="form-label">{{ __('publisher::attributes.is_active') }}</label>
                                <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" />
                                </div>
                                <div class="invalid-feedback d-block" data-field-error="is_active"></div>
                            </div>

                        </div>

                        {{-- Form actions --}}
                        <div class="d-flex justify-content-end gap-3 mt-8">
                            <button type="button" id="create-cancel"
                                class="btn btn-light">{{ __('publisher::buttons.cancel') }}</button>
                            <button type="submit" id="create-submit" class="btn btn-primary">
                                <span class="indicator-label">{{ __('publisher::buttons.submit') }}</span>
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

{{-- ============================================================
     CONTENT — datatable
     ============================================================ --}}
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
                        placeholder="{{ __('admin::attributes.search_admins') }}" />
                </div>
            </div>
        </div>

        <div class="card-body py-4">
            <table id="kt_datatable" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th></th> {{-- index 0: hidden id for ordering --}}
                        <th class="w-10px pe-2"> {{-- index 1: checkbox --}}
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                    data-kt-check-target="#kt_datatable .form-check-input" value="1" />
                            </div>
                        </th>
                        <th class="min-w-125px">{{ __('admin::attributes.name') }}</th>
                        <th class="min-w-125px">{{ __('admin::attributes.email') }}</th>
                        <th class="min-w-125px">{{ __('admin::attributes.role') }}</th>
                        <th class="min-w-125px">{{ __('admin::attributes.is_active') }}</th>
                        <th class="min-w-125px">{{ __('admin::attributes.created_at') }}</th>
                        <th class="text-end min-w-100px">{{ __('admin::attributes.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold"></tbody>
            </table>
        </div>
    </div>
@endsection

{{-- ============================================================
     JS
     ============================================================ --}}
@section('js')
    <script src="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    {{-- Plugin system --}}
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
                ], // order by hidden id DESC
                ajax: {
                    url: "{{ route('admins.index') }}"
                },
                columns: [{
                        data: "id"
                    }, // 0 — hidden, for ordering
                    {
                        data: "id"
                    }, // 1 — checkbox
                    {
                        data: "name"
                    }, // 2
                    {
                        data: "email"
                    }, // 3
                    {
                        data: "roles.[0].name"
                    }, // 4
                    {
                        data: "is_active"
                    }, // 5
                    {
                        data: "created_at"
                    }, // 6
                    {
                        data: null
                    }, // 7 — actions
                ],
                columnDefs: [
                    // 0 — hidden id
                    {
                        targets: 0,
                        visible: false,
                        orderable: true,
                        searchable: false
                    },
                    // 1 — checkbox
                    {
                        targets: 1,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="${esc(data)}" />
                                </div>`;
                        }
                    },
                    // 2 — name + avatar
                    {
                        targets: 2,
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            var fullName = esc((data || "").trim());
                            var parts = fullName ? fullName.split(/\s+/) : [];
                            var initials = parts.length > 1 ?
                                (parts[0][0] + parts[1][0]).toUpperCase() :
                                (fullName[0] || "A").toUpperCase();
                            var avatar = row.image ?
                                `<div class="symbol-label"><img src="${esc(row.image)}" alt="${fullName}" class="w-100 h-100 object-fit-cover" /></div>` :
                                `<div class="symbol-label fs-5 fw-bold bg-light-primary text-primary">${initials}</div>`;

                            return `<div class="d-flex align-items-center">
                                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">${avatar}</div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-bold fs-6 mb-1">${fullName}</span>
                                        <span class="text-muted fw-semibold fs-7">${esc(row.email || "")}</span>
                                    </div>
                                </div>`;
                        }
                    },
                    // 3 — email
                    {
                        targets: 3,
                        orderable: true,
                        searchable: true,
                        render: function(data) {
                            return `<span class="fw-bold text-gray-800">${esc(data || "")}</span>`;
                        }
                    },
                    // 4 — Role
                    {
                        targets: 4,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<span class="badge badge-light-primary fw-bold">${esc(data || "")}</span>`;

                        }
                    },
                    // 5 — is_active toggle
                    {
                        targets: 5,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var checked = data ? "checked" : "";
                            return `<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                    <input class="form-check-input active-toggle" type="checkbox" ${checked} data-id="${esc(row.id)}" />
                                </label>`;
                        }
                    },
                    // 6 — created_at
                    {
                        targets: 6,
                        orderable: true,
                        searchable: false,
                        render: function(data) {
                            return `<span class="text-gray-700 fw-semibold">${esc(data || "")}</span>`;
                        }
                    },
                    // 7 — actions
                    {
                        targets: 7,
                        orderable: false,
                        searchable: false,
                        className: "text-end",
                        render: function(data, type, row) {
                            var id = PluginInputBuilder.escapeHtml(row.id);
                            return `<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-btn"
                                        title="Edit"
                                        data-id="${id}">
                                        <i class="ki-duotone ki-pencil fs-5"></i>
                                    </a>
                                <button type="button"
                                    class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete-btn"
                                    title="Delete"
                                    data-id="${id}">
                                    <i class="ki-duotone ki-trash fs-5"></i>
                                </button>`;
                        }
                    }
                ]
            });

            // ── 2. Search ───────────────────────────────────────────────────────
            var $search = $('[data-kt-docs-table-filter="search"]');
            var searchTimer;
            $search.on("input", function() {
                var value = this.value;
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    dt.search(value).draw();
                }, 400);
            });

            // ── 3. Create Plugin ────────────────────────────────────────────────
            CreatePlugin.init({
                storeUrl: "{{ route('admins.store') }}",
                datatable: dt,
                labels: {
                    create: "+ {{ __('admin::attributes.create_admin') }}",
                    cancel: "× {{ __('admin::attributes.cancel') }}"
                }
            });

            // ── EditPlugin ────────────────────────────────────────────────────────────
            EditPlugin.init({
                updateUrl: "{{ route('admins.update', ['admin' => ':id']) }}",
                datatable: dt,
                selector: ".edit-btn",

                // actionsTarget: 7, // optional — auto-detected from last visible column

                columns: [{
                        field: "name",
                        type: "text",
                        target: 2, // datatable column index
                        placeholder: "{{ __('admin::attributes.enter_name') }}",
                        rules: {
                            required: true,
                            max: 255
                        },
                        messages: {
                            required: "Name is required.",
                            max: "Max 255 characters."
                        },
                    },
                    {
                        field: "email",
                        type: "text",
                        target: 3,
                        placeholder: "{{ __('admin::attributes.enter_email') }}",
                        rules: {
                            required: true,
                            email: true,
                            max: 255
                        },
                        messages: {
                            required: "Email is required.",
                            email: "Invalid email."
                        },
                    },
                    {
                        field: "role",
                        type: "select",
                        target: 4,
                        options: [{
                            value: "Super Admin",
                            label: "Super Admin"
                        }, ],
                        rules: {
                            required: true
                        },
                        messages: {
                            required: "Please select a role."
                        },
                    },
                    {
                        field: "is_active",
                        type: "toggle",
                        target: 5,
                    },

                    // ── Example: dependent dropdowns ──────────────────────────────────
                    // {
                    //     field   : "country_id",
                    //     type    : "select",
                    //     target  : 5,
                    //     options : [{ value: 1, label: "Egypt" }, { value: 2, label: "USA" }],
                    //     rules   : { required: true },
                    // },
                    // {
                    //     field      : "city_id",
                    //     type       : "select",
                    //     target     : 6,
                    //     options    : [],                                   // starts empty
                    //     dependsOn  : "country_id",                        // parent field name
                    //     dependsUrl : "/ajax/cities?country_id=:value",    // :value is replaced
                    //     rules      : { required: true },
                    // },

                    // ── Hidden fields → rendered in child row below the main row ───────
                    {
                        field: "password",
                        type: "password",
                        hidden: true,
                        optional: true,
                        label: "Password",
                        colClass: "col-md-6",
                        rules: {
                            min: 6
                        },
                        messages: {
                            min: "Password must be at least 6 characters."
                        },
                    },
                    {
                        field: "image",
                        type: "image",
                        hidden: true,
                        optional: true,
                        label: "Image",
                        colClass: "col-md-6",
                    },
                ],

                /**
                 * Map the server response back to the datatable row data object.
                 * Must return the same shape as your datatable ajax response row.
                 */
                mapRow: function(response) {
                    return response.data;
                },

                notifications: {
                    successTitle: "Updated",
                    successText: "Admin updated successfully.",
                    errorTitle: "Error",
                    errorText: "Something went wrong. Please try again.",
                },
            });

            // ── 5. Toggle Plugin ────────────────────────────────────────────────
            TogglePlugin.init({
                toggleUrl: "{{ route('admins.toggle-activate', ['admin' => ':id']) }}",
                selector: ".active-toggle"
            });

            // ── 6. Delete Plugin ────────────────────────────────────────────────
            DeletePlugin.init({
                deleteUrl: "{{ route('admins.destroy', ['admin' => ':id']) }}",
                datatable: dt,
                selector: ".delete-btn"
            });
        });
    </script>
@endsection
