@extends('common::layouts.master')
@section('title', @config('app.name') . ' - Admins List')
@section('css')
<link href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
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
                    <input type="text" data-kt-docs-table-filter="search" class="form-control form-control-solid w-250px ps-13"
                        placeholder="Search Admins" />
                </div>
                <!--end::Search-->
            </div>
            <!--end::Card title-->

            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
                    <!--begin::Filter-->
                    <button type="button" class="btn btn-light-primary me-3" data-bs-toggle="tooltip" title="Coming Soon">
                        <i class="ki-duotone ki-filter fs-2"><span class="path1"></span><span class="path2"></span></i>
                        Filter
                    </button>
                    <!--end::Filter-->

                    <!--begin::Add admin-->
                    <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" title="Coming Soon">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Add Admin
                    </button>
                    <!--end::Add admin-->
                </div>
                <!--end::Toolbar-->

                <!--begin::Group actions-->
                <div class="d-flex justify-content-end align-items-center d-none" data-kt-docs-table-toolbar="selected">
                    <div class="fw-bold me-5">
                        <span class="me-2" data-kt-docs-table-select="selected_count"></span> Selected
                    </div>

                    <button type="button" class="btn btn-danger" data-bs-toggle="tooltip" title="Coming Soon">
                        Selection Action
                    </button>
                </div>
                <!--end::Group actions-->
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body py-4">
            <!--begin::Datatable-->
            <table id="kt_datatable_example_1" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-10px pe-2">
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                    data-kt-check-target="#kt_datatable_example_1 .form-check-input" value="1" />
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
            <!--end::Datatable-->
        </div>
        <!--end::Card body-->
    </div>
@endsection
@section('js')
    <script src="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        "use strict";

        // Class definition
        var KTDatatablesServerSide = function () {
            // Shared variables
            var dt;

            // Private functions
            var initDatatable = function () {
                dt = $("#kt_datatable_example_1").DataTable({
                    searchDelay: 500,
                    processing: true,
                    serverSide: true,
                    order: [],
                    stateSave: false,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50],
                    ajax: {
                        url: "{{ route('admins.index') }}",
                    },
                    columns: [
                        { data: "id" },
                        { data: "name" },
                        { data: "email" },
                        { data: "is_active" },
                        { data: "created_at" },
                        { data: null },
                    ],
                    columnDefs: [
                        {
                            targets: 0,
                            orderable: false,
                            searchable: false,
                            render: function (data) {
                                return `<div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="${data}" />
                                        </div>`;
                            }
                        },
                        {
                            targets: 1,
                            render: function (data, type, row) {
                                const fullName = (data || "").trim();
                                const nameParts = fullName.split(/\s+/).filter(Boolean);
                                const initials = nameParts.length > 1
                                    ? `${nameParts[0].charAt(0)}${nameParts[1].charAt(0)}`.toUpperCase()
                                    : fullName.charAt(0).toUpperCase();
                                const imageUrl = row.image ? row.image : null;
                                const avatar = imageUrl
                                    ? `<div class="symbol-label"><img src="${imageUrl}" alt="${fullName}" class="w-100 h-100 object-fit-cover" /></div>`
                                    : `<div class="symbol-label fs-5 fw-bold bg-light-primary text-primary">${initials || "A"}</div>`;

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
                            targets: 2,
                            render: function (data) {
                                return `<span class="fw-bold text-gray-800">${data ?? ""}</span>`;
                            }
                        },
                        {
                            targets: 3,
                            render: function (data, type, row) {
                                const checked = data ? "checked" : "";
                                return `<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                            <input class="form-check-input admin-active-toggle" type="checkbox" ${checked} data-id="${row.id}" />
                                        </label>`;
                            }
                        },
                        {
                            targets: 4,
                            render: function (data) {
                                return `<span class="text-gray-700 fw-semibold">${data ?? ""}</span>`;
                            }
                        },
                        {
                            targets: -1,
                            orderable: false,
                            searchable: false,
                            className: "text-end",
                            render: function (data, type, row) {
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

            var handleSearchDatatable = function () {
                const filterSearch = document.querySelector('[data-kt-docs-table-filter="search"]');

                if (!filterSearch) {
                    return;
                }

                filterSearch.addEventListener("keyup", function (e) {
                    dt.search(e.target.value).draw();
                });
            };

            return {
                init: function () {
                    initDatatable();
                    handleSearchDatatable();
                }
            };
        }();

        // On document ready
        KTUtil.onDOMContentLoaded(function () {
            KTDatatablesServerSide.init();
        });
    </script>
@endsection
