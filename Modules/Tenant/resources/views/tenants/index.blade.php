@extends('common::layouts.master')
@section('title', config('app.name') . ' - ' . __('tenant::attributes.tenants_list'))

@section('css')
    @if (app()->getLocale() === 'ar')
        <link href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.rtl.css') }}" rel="stylesheet"
            type="text/css" />
    @else
        <link href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
            type="text/css" />
    @endif
@endsection

@php
    $centralHost = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';
@endphp

@section('toolbar')
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack my-3">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                {{ __('tenant::attributes.tenants') }}
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">{{ __('tenant::attributes.tenants') }}</li>
            </ul>
        </div>

        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="#create-collapse" id="create-toggle" class="btn btn-sm fw-bold btn-primary" data-bs-toggle="collapse"
                aria-expanded="false" aria-controls="create-collapse">
                <span id="create-toggle-label">+ {{ __('tenant::buttons.create') }}</span>
            </a>
        </div>
    </div>

    <div class="app-container container-fluid mb-7">
        <div id="create-collapse" class="accordion-collapse collapse">
            <div class="card shadow-sm border-0">
                <div class="card-header border-0 py-4">
                    <h3 class="card-title fw-bold fs-5 m-0">{{ __('tenant::buttons.create') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <form id="create-form" novalidate>
                        @csrf
                        <div class="row g-6">
                            <div class="col-md-6">
                                <label class="required form-label">{{ __('tenant::attributes.name_en') }}</label>
                                <input type="text" name="name_en" class="form-control form-control-solid"
                                    placeholder="{{ __('tenant::placeholders.enter_name_en') }}" autocomplete="off"
                                    data-rule-required data-rule-max="255"
                                    data-msg-required="{{ __('tenant::validations.name_en_required') }}"
                                    data-msg-max="{{ __('tenant::validations.name_max') }}" />
                                <div class="invalid-feedback d-block" data-field-error="name_en"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="required form-label">{{ __('tenant::attributes.name_ar') }}</label>
                                <input dir="rtl" type="text" name="name_ar" class="form-control form-control-solid"
                                    placeholder="{{ __('tenant::placeholders.enter_name_ar') }}" autocomplete="off"
                                    data-rule-required data-rule-max="255"
                                    data-msg-required="{{ __('tenant::validations.name_ar_required') }}"
                                    data-msg-max="{{ __('tenant::validations.name_max') }}" />
                                <div class="invalid-feedback d-block" data-field-error="name_ar"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="required form-label">{{ __('tenant::attributes.domain') }}</label>
                                <input type="text" name="domain" class="form-control form-control-solid"
                                    placeholder="{{ __('tenant::placeholders.enter_domain') }}" autocomplete="off"
                                    data-rule-required data-rule-max="63"
                                    data-msg-required="{{ __('tenant::validations.domain_required') }}"
                                    data-msg-max="{{ __('tenant::validations.domain_invalid') }}" />
                                <div class="form-text">
                                    {{ __('tenant::attributes.domain_hint', ['subdomain' => 'acme', 'host' => $centralHost]) }}
                                </div>
                                <div class="invalid-feedback d-block" data-field-error="domain"></div>
                            </div>

                            <div class="col-md-6 d-flex align-items-end">
                                <label class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                        checked />
                                    <span
                                        class="form-check-label fw-semibold">{{ __('tenant::attributes.is_active') }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-8">
                            <button type="submit" class="btn btn-primary">{{ __('tenant::buttons.create') }}</button>
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
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5"></i>
                    <input type="text" data-kt-docs-table-filter="search"
                        class="form-control form-control-solid w-250px ps-13"
                        placeholder="{{ __('tenant::attributes.tenants') }}" />
                </div>
            </div>
        </div>
        <div class="card-body pt-0">
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
                        <th class="min-w-125px">{{ __('tenant::attributes.name_en') }}</th>
                        <th class="min-w-125px">{{ __('tenant::attributes.name_ar') }}</th>
                        <th class="min-w-125px">{{ __('tenant::attributes.domain') }}</th>
                        <th class="min-w-125px">{{ __('tenant::attributes.is_active') }}</th>
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
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/ajax.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/notify.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/validator.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/input-builder.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/create-plugin.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/edit-plugin.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/toggle-plugin.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/delete-plugin.js') }}"></script>

    <script>
        "use strict";

        KTUtil.onDOMContentLoaded(function() {
            var esc = PluginInputBuilder.escapeHtml;
            var centralHost = @json($centralHost);

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
                    url: "{{ route('tenants.index') }}"
                },
                columns: [{
                        data: "id"
                    },
                    {
                        data: "id"
                    },
                    {
                        data: "name_en"
                    },
                    {
                        data: "name_ar"
                    },
                    {
                        data: "domain"
                    },
                    {
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
                    {
                        targets: 4,
                        orderable: false,
                        searchable: true,
                        render: function(data) {
                            return `<span class="badge badge-light-primary">${esc(data || '')}.${esc(centralHost)}</span>`;
                        }
                    },
                    {
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

            var searchTimer;
            $('[data-kt-docs-table-filter="search"]').on("input", function() {
                var value = this.value;
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    dt.search(value).draw();
                }, 400);
            });

            CreatePlugin.init({
                storeUrl: "{{ route('tenants.store') }}",
                datatable: dt,
                labels: {
                    create: "+ {{ __('tenant::buttons.create') }}",
                    cancel: "× {{ __('tenant::buttons.cancel') }}"
                }
            });

            EditPlugin.init({
                updateUrl: "{{ route('tenants.update', ['tenant' => ':id']) }}",
                datatable: dt,
                selector: ".edit-btn",
                columns: [{
                        field: "name_en",
                        type: "text",
                        target: 2,
                        placeholder: "{{ __('tenant::placeholders.enter_name_en') }}",
                        rules: {
                            required: true,
                            max: 255
                        },
                        messages: {
                            required: "{{ __('tenant::validations.name_en_required') }}",
                            max: "{{ __('tenant::validations.name_max') }}",
                        },
                    },
                    {
                        field: "name_ar",
                        type: "text",
                        target: 3,
                        placeholder: "{{ __('tenant::placeholders.enter_name_ar') }}",
                        rules: {
                            required: true,
                            max: 255
                        },
                        messages: {
                            required: "{{ __('tenant::validations.name_ar_required') }}",
                            max: "{{ __('tenant::validations.name_max') }}",
                        },
                    },
                    {
                        field: "domain",
                        type: "text",
                        target: 4,
                        placeholder: "{{ __('tenant::placeholders.enter_domain') }}",
                        rules: {
                            required: true,
                            max: 63
                        },
                        messages: {
                            required: "{{ __('tenant::validations.domain_required') }}",
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
                    successTitle: "{{ __('tenant::messages.updated') }}",
                    successText: "{{ __('tenant::messages.updated_successfully') }}",
                    errorTitle: "{{ __('tenant::messages.error') }}",
                    errorText: "{{ __('tenant::messages.something_went_wrong') }}",
                },
            });

            TogglePlugin.init({
                toggleUrl: "{{ route('tenants.toggle-activate', ['tenant' => ':id']) }}",
                selector: ".active-toggle"
            });

            DeletePlugin.init({
                deleteUrl: "{{ route('tenants.destroy', ['tenant' => ':id']) }}",
                datatable: dt,
                selector: ".delete-btn"
            });
        });
    </script>
@endsection
