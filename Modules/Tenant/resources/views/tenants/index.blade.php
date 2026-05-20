@extends('common::layouts.master')
@section('title', config('app.name') . ' - ' . __('tenant::attributes.tenants_list'))

@section('css')
    <link
        href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle' . (app()->isLocale('ar') ? '.rtl' : '') . '.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('toolbar')
    <div id="create-toolbar-area">

        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack my-3">
            {{-- Page title & breadcrumb --}}
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ __('tenant::attributes.tenants') }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="index.html" class="text-muted text-hover-primary">
                            {{ __('common::sidebar.user_management') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ __('tenant::attributes.tenants') }}</li>
                </ul>
            </div>

            {{-- Create toggle button --}}
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="#create-collapse" class="btn btn-sm fw-bold btn-primary create-open-btn" data-bs-toggle="collapse"
                    data-bs-target="#create-collapse" aria-expanded="false" aria-controls="create-collapse">
                    {{ __('tenant::buttons.create') }}
                </a>
                <a href="#create-collapse" class="btn btn-sm fw-bold btn-danger create-close-btn d-none"
                    data-bs-toggle="collapse" data-bs-target="#create-collapse" aria-expanded="true"
                    aria-controls="create-collapse">
                    × {{ __('tenant::buttons.cancel') }}
                </a>
            </div>

        </div>

        <div class="app-container container-fluid mb-7">
            <div id="create-collapse" class="collapse">
                @include('tenant::tenants.partials.create')
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
                    <input type="text" id="table-search" class="form-control form-control-solid w-250px ps-13"
                        placeholder="{{ __('tenant::placeholders.search_tenants') }}" />
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
                                    data-kt-check-target="#kt_datatable .row-checkbox" value="1" />
                            </div>
                        </th>
                        <th class="min-w-175px">{{ __('tenant::attributes.name_en') }}</th>
                        <th class="min-w-175px">{{ __('tenant::attributes.name_ar') }}</th>
                        <th class="min-w-175px">{{ __('tenant::attributes.domain') }}</th>
                        <th class="min-w-100px">{{ __('tenant::attributes.is_active') }}</th>
                        <th class="min-w-125px">{{ __('tenant::attributes.created_at') }}</th>
                        <th class="text-end min-w-100px">{{ __('tenant::attributes.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold"></tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/actions.js') }}"></script>
    <script>
        "use strict";

        var dt = $('#kt_datatable').DataTable({
            serverSide: true,
            processing: true,
            stateSave: false,
            pageLength: 10,
            lengthMenu: [10, 25, 50],
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: "{{ route('tenants.index') }}"
            },
            columns: [{
                    data: "id"
                }, // 0 — hidden, for ordering
                {
                    data: "id"
                }, // 1 — checkbox
                {
                    data: 'name_en'
                },
                {
                    data: 'name_ar'
                },
                {
                    data: 'domain'
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
                    searchable: false
                },
                {
                    targets: 1,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return `<div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input row-checkbox" type="checkbox" value="${data}" />
                            </div>`;
                    }
                },
                {
                    targets: 2,
                    orderable: false,
                    render: function(data) {
                        return '<span class="text-gray-800 fw-bold fs-6">' + (data || '') + '</span>';
                    }
                },
                {
                    targets: 3,
                    orderable: false,
                    render: function(data) {
                        return '<span class="text-gray-800 fw-bold fs-6">' + (data || '') + '</span>';
                    }
                },
                {
                    targets: 4,
                    orderable: false,
                    render: function(data) {
                        if (data) {
                            return `<a href="https://${data}.{{ config('tenancy.central_domains')[0] }}" target="_blank" class="fw-semibold text-gray-700 text-hover-primary text-decoration-underline">` +
                                data + '.{{ config('tenancy.central_domains')[0] }}' +
                                '</a>';
                        }
                        return '<span class="fw-semibold text-gray-700"></span>';
                    }
                },
                {
                    targets: 5,
                    orderable: false,
                    searchable: false,
                    render: function(data, t, row) {
                        return '<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">' +
                            '<input class="form-check-input active-toggle" type="checkbox" data-id="' + row
                            .id + '"' + (data ? ' checked' : '') + ' />' +
                            '</label>';
                    }
                },
                {
                    targets: 6,
                    orderable: true,
                    searchable: false,
                    render: function(data) {
                        return '<span class="text-gray-700 fw-semibold">' + (data || '') + '</span>';
                    }
                },
                {
                    targets: 7,
                    orderable: false,
                    searchable: false,
                    className: 'text-end',
                    render: function(data, t, row) {
                        return '<button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-btn" data-id="' +
                            row.id + '">' +
                            '<span class="indicator-label"><i class="ki-duotone ki-pencil fs-5"><span class="path1"></span><span class="path2"></span></i></span>' +
                            '<span class="indicator-progress"><span class="spinner-border spinner-border-sm"></span></span>' +
                            '</button>' +
                            '<button class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete-btn" data-id="' +
                            row.id + '">' +
                            '<i class="ki-duotone ki-trash fs-5"></i></button>';
                    }
                },
            ],
        });

        var searchTimer;
        $('#table-search').on('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                dt.search($('#table-search').val()).draw();
            }, 400);
        });

        Actions.initCreate(dt);
        Actions.initEdit(dt, "{{ route('tenants.edit', ':id') }}");
        Actions.initDelete(dt, "{{ route('tenants.destroy', ':id') }}");
        Actions.initToggle("{{ route('tenants.toggle-activate', ':id') }}");
    </script>

@endsection
