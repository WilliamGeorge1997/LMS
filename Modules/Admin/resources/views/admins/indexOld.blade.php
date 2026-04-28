@extends('common::layouts.master')
@section('title', @config('app.name') . ' - Admins List')
@section('css')
    <link href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
        type="text/css" />
@endsection
@section('toolbar')
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack my-3">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                Admins</h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="index.html" class="text-muted text-hover-primary">User Managment</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Admins</li>
            </ul>

        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="#create-collapse" id="create-toggle" class="btn btn-sm fw-bold btn-primary" data-create-toggle
                data-bs-toggle="collapse" aria-expanded="false" aria-controls="create-collapse">
                <span id="create-toggle-label" data-create-toggle-label>+ Create Admin</span>
            </a>
        </div>
    </div>
    <div class="app-container container-fluid mb-7">
        <div class="accordion" data-create-accordion>
            <div class="accordion-item border-0">
                <div id="create-collapse" class="accordion-collapse collapse" data-create-collapse>
                    <div class="accordion-body px-0 pt-0">
                        <div class="card shadow-sm border-0">
                            <div class="card-header border-0 py-4">
                                <h3 class="card-title fw-bold fs-5 m-0">Create Admin</h3>
                            </div>
                            <div class="card-body pt-0">
                        <form id="create-form" data-create-form novalidate enctype="multipart/form-data">
                            @csrf
                            <div class="row g-6">
                                <div class="col-md-6">
                                    <label class="required form-label">{{ __('attributes.name') }}</label>
                                    <input type="text" name="name" class="form-control form-control-solid"
                                        placeholder="Enter name" autocomplete="off" required maxlength="255" />
                                    <div class="invalid-feedback d-block" data-field-error="name"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="required form-label">{{ __('attributes.email') }}</label>
                                    <input type="email" name="email" class="form-control form-control-solid"
                                        placeholder="Enter email" autocomplete="off" required maxlength="255" />
                                    <div class="invalid-feedback d-block" data-field-error="email"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="required form-label">Role</label>
                                    <select name="role" class="form-select form-select-solid" required>
                                        <option value="" selected disabled>Select role</option>
                                        <option value="{{ \Modules\Admin\Enums\Role::SUPER_ADMIN->value }}">
                                            {{ \Modules\Admin\Enums\Role::SUPER_ADMIN->value }}
                                        </option>
                                    </select>
                                    <div class="invalid-feedback d-block" data-field-error="role"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="required form-label">{{ __('attributes.password') }}</label>
                                    <input type="password" name="password" class="form-control form-control-solid"
                                        placeholder="Enter password" autocomplete="new-password" required minlength="6" />
                                    <div class="invalid-feedback d-block" data-field-error="password"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('attributes.image') }}</label>
                                    <input type="file" name="image" class="form-control form-control-solid"
                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" />
                                    <div class="invalid-feedback d-block" data-field-error="image"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('attributes.is_active') }}</label>
                                    <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            checked />
                                    </div>
                                    <div class="invalid-feedback d-block" data-field-error="is_active"></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-3 mt-8">
                                <button type="button" id="create-cancel" class="btn btn-light" data-create-cancel>Cancel</button>
                                <button type="submit" id="create-submit" class="btn btn-primary" data-create-submit>
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
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <input type="text" data-kt-docs-table-filter="search"
                        class="form-control form-control-solid w-250px ps-13" placeholder="Search Admins" />
                </div>
                <!--end::Search-->
            </div>
            <!--end::Card title-->

            <!--begin::Card toolbar-->
            {{-- <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
                    <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="tooltip" title="Coming Soon">
                        <i class="ki-duotone ki-filter fs-2"><span class="path1"></span><span class="path2"></span></i>
                        Filter
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" title="Coming Soon">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Add Admin
                    </button>
                </div>
                <div class="d-flex justify-content-end align-items-center d-none" data-kt-docs-table-toolbar="selected">
                    <div class="fw-bold me-5">
                        <span class="me-2" data-kt-docs-table-select="selected_count"></span> Selected
                    </div>
                    <button type="button" class="btn btn-danger" data-bs-toggle="tooltip" title="Coming Soon">
                        Selection Action
                    </button>
                </div>
            </div> --}}
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
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
                        <th class="min-w-125px">{{ __('attributes.name') }}</th>
                        <th class="min-w-125px">{{ __('attributes.email') }}</th>
                        <th class="min-w-125px">{{ __('attributes.is_active') }}</th>
                        <th class="min-w-125px">{{ __('attributes.created_at') }}</th>
                        <th class="text-end min-w-100px">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                </tbody>
            </table>
        </div>
        <!--end::Card body-->
    </div>
@endsection

@section('js')
    <script src="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/create-accordion-plugin.js') }}"></script>
    <script>
        "use strict";

        var KTDatatablesServerSide = function() {
            var dt;

            var initDatatable = function() {
                dt = $("#kt_datatable").DataTable({
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    order: [
                        [0, 'desc']
                    ], // default order by hidden id DESC
                    stateSave: false,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50],
                    ajax: {
                        url: "{{ route('admins.index') }}",
                    },
                    columns: [{
                            data: 'id'
                        }, // index 0 — hidden, for ordering
                        {
                            data: 'id'
                        }, // index 1 — checkbox
                        {
                            data: 'name'
                        },
                        {
                            data: 'email'
                        },
                        {
                            data: 'is_active'
                        },
                        {
                            data: 'created_at'
                        },
                        {
                            data: null
                        },
                    ],
                    columnDefs: [{
                            targets: 0,
                            visible: false,
                            orderable: true,
                            searchable: false,
                        },
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
                        {
                            targets: 2,
                            orderable: true,
                            searchable: true,
                            render: function(data, type, row) {
                                const fullName = (data || "").trim();
                                const nameParts = fullName.split(/\s+/).filter(Boolean);
                                const initials = nameParts.length > 1 ?
                                    `${nameParts[0].charAt(0)}${nameParts[1].charAt(0)}`
                                    .toUpperCase() :
                                    fullName.charAt(0).toUpperCase();
                                const avatar = row.image ?
                                    `<div class="symbol-label"><img src="${row.image}" alt="${fullName}" class="w-100 h-100 object-fit-cover" /></div>` :
                                    `<div class="symbol-label fs-5 fw-bold bg-light-primary text-primary">${initials || "A"}</div>`;

                                return `<div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-40px overflow-hidden me-3">${avatar}</div>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-bold fs-6 mb-1">${fullName}</span>
                                                <span class="text-muted fw-semibold fs-7">${row.email ?? ""}</span>
                                            </div>
                                        </div>`;
                            }
                        },
                        {
                            targets: 3,
                            orderable: true,
                            searchable: true,
                            render: function(data) {
                                return `<span class="fw-bold text-gray-800">${data ?? ""}</span>`;
                            }
                        },
                        {
                            targets: 4,
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                const checked = data ? "checked" : "";
                                return `<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                            <input class="form-check-input active-toggle" type="checkbox" ${checked} data-id="${row.id}" />
                                        </label>`;
                            }
                        },
                        {
                            targets: 5,
                            orderable: true,
                            searchable: false,
                            render: function(data) {
                                return `<span class="text-gray-700 fw-semibold">${data ?? ""}</span>`;
                            }
                        },
                        {
                            targets: 6,
                            orderable: false,
                            searchable: false,
                            className: "text-end",
                            render: function(data, type, row) {
                                return `<a href="/admin/admins/${row.id}/edit" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" title="Edit">
                                            <i class="ki-duotone ki-pencil fs-5"></i>
                                        </a>
                                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" title="Delete" data-id="${row.id}">
                                            <i class="ki-duotone ki-trash fs-5"></i>
                                        </button>`;
                            }
                        }
                    ],
                });
            };

            var handleSearchDatatable = function() {
                const filterSearch = document.querySelector('[data-kt-docs-table-filter="search"]');

                if (!filterSearch) return;

                filterSearch.addEventListener("keyup", function(e) {
                    dt.search(e.target.value).draw();
                });
            };

            return {
                init: function() {
                    initDatatable();
                    handleSearchDatatable();
                }
            };
        }();

        KTUtil.onDOMContentLoaded(function() {
            KTDatatablesServerSide.init();
            CreatePlugin.init({
                storeUrl: "{{ route('admins.store') }}",
                labels: {
                    create: '+ Create Admin',
                    cancel: '× Cancel'
                },
                datatable: $('#kt_datatable').DataTable(),
                datatableMode: 'reload'
            });
        });
    </script>
@endsection
