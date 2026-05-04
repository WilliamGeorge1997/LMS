@extends('common::layouts.master')
@section('title', config('app.name') . ' - Admins List')

@section('css')
<link href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

{{-- ============================================================
     TOOLBAR — page title + create button + create form
     ============================================================ --}}
@section('toolbar')
<div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack my-3">

    {{-- Page title & breadcrumb --}}
    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
            Admins
        </h1>
        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
            <li class="breadcrumb-item text-muted">
                <a href="index.html" class="text-muted text-hover-primary">User Management</a>
            </li>
            <li class="breadcrumb-item">
                <span class="bullet bg-gray-500 w-5px h-2px"></span>
            </li>
            <li class="breadcrumb-item text-muted">Admins</li>
        </ul>
    </div>

    {{-- Create toggle button --}}
    <div class="d-flex align-items-center gap-2 gap-lg-3">
        <a href="#create-collapse"
            id="create-toggle"
            class="btn btn-sm fw-bold btn-primary"
            data-bs-toggle="collapse"
            aria-expanded="false"
            aria-controls="create-collapse">
            <span id="create-toggle-label">+ Create Admin</span>
        </a>
    </div>

</div>

{{-- Create form accordion --}}
<div class="app-container container-fluid mb-7">
    <div id="create-collapse" class="accordion-collapse collapse">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 py-4">
                <h3 class="card-title fw-bold fs-5 m-0">Create Admin</h3>
            </div>
            <div class="card-body pt-0">

                <form id="create-form" novalidate enctype="multipart/form-data">
                    @csrf
                    <div class="row g-6">

                        {{-- Name --}}
                        <div class="col-md-6">
                            <label class="required form-label">{{ __('attributes.name') }}</label>
                            <input type="text" name="name"
                                class="form-control form-control-solid"
                                placeholder="Enter name"
                                autocomplete="off"
                                data-rule-required
                                data-rule-max="255"
                                data-msg-required="Name is required."
                                data-msg-max="Name must not exceed 255 characters." />
                            <div class="invalid-feedback d-block" data-field-error="name"></div>
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="required form-label">{{ __('attributes.email') }}</label>
                            <input type="text" name="email"
                                class="form-control form-control-solid"
                                placeholder="Enter email"
                                autocomplete="off"
                                data-rule-required
                                data-rule-email
                                data-rule-max="255"
                                data-msg-required="Email is required."
                                data-msg-email="Please enter a valid email."
                                data-msg-max="Email must not exceed 255 characters." />
                            <div class="invalid-feedback d-block" data-field-error="email"></div>
                        </div>

                        {{-- Role --}}
                        <div class="col-md-6">
                            <label class="required form-label">Role</label>
                            <select name="role"
                                class="form-select form-select-solid"
                                data-rule-required
                                data-msg-required="Please select a role.">
                                <option value="" disabled selected>Select role</option>
                                <option value="{{ \Modules\Admin\Enums\Role::SUPER_ADMIN->value }}">
                                    {{ \Modules\Admin\Enums\Role::SUPER_ADMIN->value }}
                                </option>
                            </select>
                            <div class="invalid-feedback d-block" data-field-error="role"></div>
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6">
                            <label class="required form-label">{{ __('attributes.password') }}</label>
                            <input type="password" name="password" id="create-password"
                                class="form-control form-control-solid"
                                placeholder="Enter password"
                                autocomplete="new-password"
                                data-rule-required
                                data-rule-min="6"
                                data-msg-required="Password is required."
                                data-msg-min="Password must be at least 6 characters." />
                            <div class="invalid-feedback d-block" data-field-error="password"></div>
                        </div>

                        {{-- Image (optional) --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('attributes.image') }}</label>
                            <input type="file" name="image"
                                class="form-control form-control-solid"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" />
                            <div class="invalid-feedback d-block" data-field-error="image"></div>
                        </div>

                        {{-- Is Active (optional, no validation needed) --}}
                        <div class="col-md-6">
                            <label class="form-label">{{ __('attributes.is_active') }}</label>
                            <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked />
                            </div>
                            <div class="invalid-feedback d-block" data-field-error="is_active"></div>
                        </div>

                    </div>

                    {{-- Form actions --}}
                    <div class="d-flex justify-content-end gap-3 mt-8">
                        <button type="button" id="create-cancel" class="btn btn-light">Cancel</button>
                        <button type="submit" id="create-submit" class="btn btn-primary">
                            <span class="indicator-label">Submit</span>
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
                <input type="text"
                    data-kt-docs-table-filter="search"
                    class="form-control form-control-solid w-250px ps-13"
                    placeholder="Search Admins" />
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
                            <input class="form-check-input" type="checkbox"
                                data-kt-check="true"
                                data-kt-check-target="#kt_datatable .form-check-input"
                                value="1" />
                        </div>
                    </th>
                    <th class="min-w-125px">{{ __('attributes.name') }}</th>
                    <th class="min-w-125px">{{ __('attributes.email') }}</th>
                    <th class="min-w-125px">{{ __('attributes.is_active') }}</th>
                    <th class="min-w-125px">{{ __('attributes.created_at') }}</th>
                    <th class="text-end min-w-100px">Actions</th>
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
<script src="{{ asset('dashboard/assets/js/plugins/core/ajax.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/plugins/core/notify.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/plugins/core/validator.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/plugins/core/dependent-dropdown.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/plugins/create-plugin.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/plugins/toggle-plugin.js') }}"></script>
<script src="{{ asset('dashboard/assets/js/plugins/delete-plugin.js') }}"></script>

<script>
    "use strict";

    KTUtil.onDOMContentLoaded(function() {

        // ── 1. Datatable ────────────────────────────────────────────────────
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
                    data: "is_active"
                }, // 4
                {
                    data: "created_at"
                }, // 5
                {
                    data: null
                }, // 6 — actions
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
                                    <input class="form-check-input" type="checkbox" value="${data}" />
                                </div>`;
                    }
                },
                // 2 — name + avatar
                {
                    targets: 2,
                    orderable: true,
                    searchable: true,
                    render: function(data, type, row) {
                        var fullName = (data || "").trim();
                        var parts = fullName.split(/\s+/).filter(Boolean);
                        var initials = parts.length > 1 ?
                            (parts[0][0] + parts[1][0]).toUpperCase() :
                            (fullName[0] || "A").toUpperCase();
                        var avatar = row.image ?
                            `<div class="symbol-label"><img src="${row.image}" alt="${fullName}" class="w-100 h-100 object-fit-cover" /></div>` :
                            `<div class="symbol-label fs-5 fw-bold bg-light-primary text-primary">${initials}</div>`;

                        return `<div class="d-flex align-items-center">
                                    <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">${avatar}</div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-bold fs-6 mb-1">${fullName}</span>
                                        <span class="text-muted fw-semibold fs-7">${row.email ?? ""}</span>
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
                        return `<span class="fw-bold text-gray-800">${data ?? ""}</span>`;
                    }
                },
                // 4 — is_active toggle
                {
                    targets: 4,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        var checked = data ? "checked" : "";
                        return `<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                    <input class="form-check-input active-toggle" type="checkbox" ${checked} data-id="${row.id}" />
                                </label>`;
                    }
                },
                // 5 — created_at
                {
                    targets: 5,
                    orderable: true,
                    searchable: false,
                    render: function(data) {
                        return `<span class="text-gray-700 fw-semibold">${data ?? ""}</span>`;
                    }
                },
                // 6 — actions
                {
                    targets: 6,
                    orderable: false,
                    searchable: false,
                    className: "text-end",
                    render: function(data, type, row) {
                        return `<a href="{{ route('admins.edit', ['admin' => ':id']) }}".replace(':id', row.id)
                                   class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                   title="Edit">
                                    <i class="ki-duotone ki-pencil fs-5"></i>
                                </a>
                                <button type="button"
                                    class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete-btn"
                                    title="Delete"
                                    data-id="${row.id}">
                                    <i class="ki-duotone ki-trash fs-5"></i>
                                </button>`;
                    }
                }
            ]
        });

        // ── 2. Search ───────────────────────────────────────────────────────
        var $search = $('[data-kt-docs-table-filter="search"]');
        $search.on("keyup", function() {
            dt.search(this.value).draw();
        });

        // ── 3. Create Plugin ────────────────────────────────────────────────
        CreatePlugin.init({
            storeUrl: "{{ route('admins.store') }}",
            datatable: dt,
            labels: {
                create: "+ Create Admin",
                cancel: "× Cancel"
            }
        });

        // ── 4. Toggle Plugin ────────────────────────────────────────────────
        TogglePlugin.init({
            toggleUrl: "{{ route('admins.toggle', ['admin' => ':id']) }}",
            selector: ".active-toggle"
        });

        // ── 5. Delete Plugin ────────────────────────────────────────────────
        DeletePlugin.init({
            deleteUrl: "{{ route('admins.destroy', ['admin' => ':id']) }}",
            datatable: dt,
            selector: ".delete-btn"
        });

    });
</script>
@endsection
