@extends('common::layouts.master')
@section('title', config('app.name') . ' - ' . __('book::attributes.book_codes_list'))

@section('css')
    @if (app()->getLocale() === 'ar')
        <link href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.rtl.css') }}" rel="stylesheet"
            type="text/css" />
    @else
        <link href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
            type="text/css" />
    @endif
@endsection

@section('toolbar')
    <div id="create-toolbar-area">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack my-3">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    {{ __('book::attributes.book_codes') }}
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ url('/admin/book-codes') }}"
                            class="text-muted text-hover-primary">{{ __('book::attributes.book_codes') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ __('book::attributes.book_codes') }}</li>
                </ul>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ request()->routeIs('tenant.*') ? route('tenant.book-codes.export') : route('book-codes.export') }}" class="btn btn-sm fw-bold btn-success" onclick="this.classList.add('disabled'); setTimeout(() => this.classList.remove('disabled'), 5000);">
                    <i class="ki-duotone ki-file-down fs-2"><span class="path1"></span><span class="path2"></span></i>
                    Export Excel
                </a>
                <a href="#create-collapse" class="btn btn-sm fw-bold btn-primary create-open-btn" data-bs-toggle="collapse"
                    data-bs-target="#create-collapse" aria-expanded="false" aria-controls="create-collapse">
                    {{ __('book::buttons.create_code') }}
                </a>
                <a href="#create-collapse" class="btn btn-sm fw-bold btn-danger create-close-btn d-none"
                    data-bs-toggle="collapse" data-bs-target="#create-collapse" aria-expanded="true"
                    aria-controls="create-collapse">
                    × {{ __('book::buttons.cancel') }}
                </a>
            </div>
        </div>

        <div class="app-container container-fluid mb-7">
            <div id="create-collapse" class="collapse">
                @include('book::book-codes.partials.create')
            </div>
        </div>
    </div>
@endsection

@php($is_super_admin = auth('admin')->user()->hasRole(\Modules\Admin\Enums\Role::SUPER_ADMIN))

@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <input type="text" id="table-search" class="form-control form-control-solid w-250px ps-13"
                        placeholder="{{ __('book::placeholders.search_book_codes') }}" />
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
                        <th class="min-w-125px">{{ __('book::attributes.access_code') }}</th>
                        <th class="min-w-150px">{{ __('book::attributes.book') }}</th>
                        <th class="min-w-100px">{{ __('book::attributes.type') }}</th>
                        <th class="min-w-100px">{{ __('book::attributes.duration') }}</th>
                        <th class="min-w-100px">{{ __('book::attributes.from') }}</th>
                        <th class="min-w-100px">{{ __('book::attributes.to') }}</th>
                        <th class="min-w-100px">{{ __('book::attributes.is_used') }}</th>
                        @if ($is_super_admin)
                            <th class="min-w-125px">{{ __('publisher::attributes.tenant') }}</th>
                        @endif
                        <th class="min-w-100px">{{ __('book::attributes.is_active') }}</th>
                        <th class="min-w-100px">{{ __('book::attributes.created_at') }}</th>
                        <th class="text-end min-w-75px">{{ __('book::attributes.actions') }}</th>
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
                url: "{{ url('/admin/book-codes') }}"
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'id'
                },
                {
                    data: 'code',
                    name: 'code',
                },
                {
                    data: 'book.title',
                    name: 'book.title',
                },
                {
                    data: 'type'
                },
                {
                    data: 'duration'
                },
                {
                    data: 'from'
                },
                {
                    data: 'to'
                },
                {
                    data: 'is_used'
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
                    searchable: true,
                    render: function(data) {
                        return '<span class="fw-bold text-gray-800">' + (data || '') + '</span>';
                    }
                },
                {
                    targets: 3,
                    orderable: false,
                    searchable: true,
                    render: function(data) {
                        return '<span class="fw-bold text-gray-800">' + (data ? data.en + ' - ' + data.ar :
                            '') + '</span>';
                    }
                },
                {
                    targets: 4,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        var label = {
                            student: @json(__('book::attributes.student')),
                            teacher: @json(__('book::attributes.teacher')),
                        } [data] || data || '';

                        if (data === 'student') {
                            return '<span class="badge badge-primary fw-bold">' + label + '</span>';
                        }
                        if (data === 'teacher') {
                            return '<span class="badge badge-info fw-bold">' + label + '</span>';
                        }

                        return '<span class="badge badge-light fw-bold">' + label + '</span>';
                    }
                },
                {
                    targets: 5,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        return '<span class="text-gray-700 fw-semibold">' + (data || '') + '</span>';
                    }
                },
                {
                    targets: 6,
                    orderable: false,
                    searchable: false,
                    render: function(data, t, row) {
                        var value = row.is_used && data ? data : @json(__('book::attributes.pending_register'));

                        return '<span class="text-gray-700 fw-semibold">' + value + '</span>';
                    }
                },
                {
                    targets: 7,
                    orderable: false,
                    searchable: false,
                    render: function(data, t, row) {
                        var value = row.is_used && data ? data : @json(__('book::attributes.pending_register'));

                        return '<span class="text-gray-700 fw-semibold">' + value + '</span>';
                    }
                },
                {
                    targets: 8,
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        if (data == 1) {
                            return '<i class="ki-duotone ki-check fs-2 text-success"><span class="path1"></span><span class="path2"></span></i>';
                        }
                        return '<i class="ki-duotone ki-cross fs-2 text-danger"><span class="path1"></span><span class="path2"></span></i>';
                    }
                },
                @if ($is_super_admin)
                    {
                        targets: 9,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return '<span class="fw-bold text-gray-800">' + (data ? data.en + ' - ' + data
                                .ar : '') + '</span>';
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
                        return data;
                    }
                },
                {
                    targets: -1,
                    orderable: false,
                    searchable: false,
                    className: 'text-end',
                    render: function(data, t, row) {
                        return '<button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete-btn" data-id="' +
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
        Actions.initDelete(dt, "{{ url('/admin/book-codes') }}/:id",
            "{{ __('book::messages.delete_code_confirm') }}");
        Actions.initToggle("{{ url('/admin/book-codes') }}/:id/toggle-activate");
    </script>
@endsection
