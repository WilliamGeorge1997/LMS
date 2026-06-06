@extends('common::layouts.master')
@section('title', config('app.name') . ' - ' . __('school::attributes.schools_list'))

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
    <div id="create-toolbar-area">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack my-3">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ __('school::attributes.schools') }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ url('/admin/schools') }}"
                            class="text-muted text-hover-primary">{{ __('school::attributes.schools') }}</a>
                    </li>
                    <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                    <li class="breadcrumb-item text-muted">{{ __('school::attributes.schools') }}</li>
                </ul>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="#create-collapse" class="btn btn-sm fw-bold btn-primary create-open-btn" data-bs-toggle="collapse"
                    data-bs-target="#create-collapse" aria-expanded="false" aria-controls="create-collapse">
                    {{ __('school::buttons.create') }}
                </a>
                <a href="#create-collapse" class="btn btn-sm fw-bold btn-danger create-close-btn d-none"
                    data-bs-toggle="collapse" data-bs-target="#create-collapse" aria-expanded="true"
                    aria-controls="create-collapse">
                    × {{ __('school::buttons.cancel') }}
                </a>
            </div>
        </div>

        <div class="app-container container-fluid mb-7">
            <div id="create-collapse" class="collapse">
                @include('school::schools.partials.create')
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
                        placeholder="{{ __('school::placeholders.search_schools') }}" />
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
                        <th class="min-w-125px">{{ __('school::attributes.title_en') }}</th>
                        <th class="min-w-125px">{{ __('school::attributes.title_ar') }}</th>
                        <th class="min-w-100px">{{ __('school::attributes.phone') }}</th>
                        <th class="min-w-125px">{{ __('school::attributes.email') }}</th>
                        <th class="min-w-125px">{{ __('school::attributes.country_id') }}</th>
                        <th class="min-w-125px">{{ __('school::attributes.city_id') }}</th>
                        <th class="min-w-125px">{{ __('school::attributes.region_id') }}</th>
                        @if ($is_super_admin)
                            <th class="min-w-125px">{{ __('school::attributes.tenant') }}</th>
                        @endif
                        <th class="min-w-125px">{{ __('school::attributes.is_active') }}</th>
                        <th class="min-w-125px">{{ __('school::attributes.created_at') }}</th>
                        <th class="text-end min-w-100px">{{ __('school::attributes.actions') }}</th>
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
                url: "{{ url('/admin/schools') }}"
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'id'
                },
                {
                    data: 'title_en'
                },
                {
                    data: 'title_ar'
                },
                {
                    data: 'phone'
                },
                {
                    data: 'email'
                },
                {
                    data: 'country.title'
                },
                {
                    data: 'city.title'
                },
                {
                    data: 'region.title'
                },
                @if ($is_super_admin)
                    {
                        data: 'tenant.name'
                    },
                @endif {
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
                    orderable: true,
                    render: function(data) {
                        return '<span class="fw-bold text-gray-800">' + (data || '') + '</span>';
                    }
                },
                {
                    targets: 3,
                    orderable: true,
                    render: function(data) {
                        return '<span class="fw-bold text-gray-800">' + (data || '') + '</span>';
                    }
                },
                {
                    targets: 4,
                    orderable: true,
                    render: function(data) {
                        return '<span class="text-gray-700 fw-semibold">' + (data || '') + '</span>';
                    }
                },
                {
                    targets: 5,
                    orderable: true,
                    render: function(data) {
                        return '<span class="text-gray-700 fw-semibold">' + (data || '') + '</span>';
                    }
                },
                {
                    targets: 6,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return '<span class="badge badge-light-info fw-bold">' + (data ? data.en + ' - ' + data.ar : '') + '</span>';
                    }
                },
                {
                    targets: 7,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return '<span class="badge badge-light-primary fw-bold">' + (data ? data.en + ' - ' + data.ar : '') + '</span>';
                    }
                },
                {
                    targets: 8,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return '<span class="badge badge-light-success fw-bold">' + (data ? data.en + ' - ' + data.ar : '') + '</span>';
                    }
                },
                @if ($is_super_admin)
                    {
                        targets: 9,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<span class="fw-bold text-gray-800">' + (data ? data.en + ' - ' + data.ar : '') + '</span>';
                        }
                    },
                @endif {
                    targets: -3,
                    orderable: false,
                    searchable: false,
                    render: function(data, t, row) {
                        return '<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">' +
                            '<input class="form-check-input active-toggle" type="checkbox" data-id="' + row
                            .id + '"' +
                            (data ? ' checked' : '') + ' />' +
                            '</label>';
                    }
                },
                {
                    targets: -2,
                    orderable: true,
                    searchable: false,
                    render: function(data) {
                        return '<span class="text-gray-700 fw-semibold">' + (data || '') + '</span>';
                    }
                },
                {
                    targets: -1,
                    orderable: false,
                    searchable: false,
                    className: 'text-end',
                    render: function(data, t, row) {
                        return '<button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-btn" data-id="' +
                            row.id + '">' +
                            '<span class="indicator-label"><i class="ki-duotone ki-pencil fs-5"><span class="path1"></span><span class="path2"></span></i></span>' +
                            '<span class="indicator-progress"><span class="spinner-border spinner-border-sm"></span></span>' +
                            '</button>' +
                            '<button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete-btn" data-id="' +
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
        Actions.initEdit(dt, "{{ url('/admin/schools') }}/:id/edit");
        Actions.initDelete(dt, "{{ url('/admin/schools') }}/:id");
        Actions.initToggle("{{ url('/admin/schools') }}/:id/toggle-activate");
        Actions.initDepends();
    </script>
@endsection
