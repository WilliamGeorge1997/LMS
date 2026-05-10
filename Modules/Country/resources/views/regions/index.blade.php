@extends('common::layouts.master')
@section('title', config('app.name') . ' - Regions')

@section('css')
    <link href="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet"
        type="text/css" />
@endsection

@section('toolbar')
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack my-3">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                Regions
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('countries.index') }}" class="text-muted text-hover-primary">Locations</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Regions</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="#create-collapse" id="create-toggle" class="btn btn-sm fw-bold btn-primary" data-bs-toggle="collapse"
                aria-expanded="false" aria-controls="create-collapse">
                <span id="create-toggle-label">+ Create Region</span>
            </a>
        </div>
    </div>

    <div class="app-container container-fluid mb-7">
        <div id="create-collapse" class="accordion-collapse collapse">
            <div class="card shadow-sm border-0">
                <div class="card-header border-0 py-4">
                    <h3 class="card-title fw-bold fs-5 m-0">Create Region</h3>
                </div>
                <div class="card-body pt-0">
                    <form id="create-form" novalidate>
                        @csrf
                        <div class="row g-6">
                            <div class="col-md-6">
                                <label class="required form-label">Country</label>
                                <select name="country_id" class="form-select form-select-solid" data-rule-required
                                    data-msg-required="Please select a country.">
                                    <option value="" disabled selected>Select country</option>
                                    @foreach ($countryOptions as $opt)
                                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback d-block" data-field-error="country_id"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="required form-label">City</label>
                                <select name="city_id" class="form-select form-select-solid" data-depends-on="country_id"
                                    data-depends-url="{{ route('cities.select-options') }}?country_id=:value"
                                    data-depends-placeholder="Select city" data-rule-required
                                    data-msg-required="Please select a city.">
                                    <option value="" disabled selected>Select city</option>
                                </select>
                                <div class="invalid-feedback d-block" data-field-error="city_id"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="required form-label">Title (EN)</label>
                                <input type="text" name="title_en" class="form-control form-control-solid"
                                    data-rule-required data-rule-max="255" data-msg-required="English title is required." />
                                <div class="invalid-feedback d-block" data-field-error="title_en"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="required form-label">Title (AR)</label>
                                <input type="text" name="title_ar" class="form-control form-control-solid"
                                    data-rule-required data-rule-max="255" data-msg-required="Arabic title is required." />
                                <div class="invalid-feedback d-block" data-field-error="title_ar"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('attributes.is_active') }}</label>
                                <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" />
                                </div>
                            </div>
                        </div>
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

@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <input type="text" data-kt-docs-table-filter="search"
                        class="form-control form-control-solid w-250px ps-13" placeholder="Search" />
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
                                    data-kt-check-target="#kt_datatable .form-check-input" value="1" />
                            </div>
                        </th>
                        <th class="min-w-125px">Title (EN)</th>
                        <th class="min-w-125px">Title (AR)</th>
                        <th class="min-w-125px">Country</th>
                        <th class="min-w-125px">City</th>
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

@section('js')
    <script src="{{ asset('dashboard/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/ajax.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/notify.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/validator.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/core/dependent-dropdown.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/create-plugin.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/edit-plugin.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/toggle-plugin.js') }}"></script>
    <script src="{{ asset('dashboard/assets/js/custom/plugins/delete-plugin.js') }}"></script>

    <script>
        "use strict";

        KTUtil.onDOMContentLoaded(function() {
            var countryOptions = @json($countryOptions);
            var citiesSelectUrl = "{{ route('cities.select-options') }}?country_id=:value";

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
                ],
                ajax: {
                    url: "{{ route('regions.index') }}"
                },
                columns: [{
                        data: "id"
                    },
                    {
                        data: "id"
                    },
                    {
                        data: "title_en"
                    },
                    {
                        data: "title_ar"
                    },
                    {
                        data: "country_id"
                    },
                    {
                        data: "city_id"
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
                                    <input class="form-check-input" type="checkbox" value="${data}" />
                                </div>`;
                        }
                    },
                    {
                        targets: 2,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<span class="fw-bold text-gray-800">${data ?? ""}</span>`;
                        }
                    },
                    {
                        targets: 3,
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `<span class="fw-bold text-gray-800">${data ?? ""}</span>`;
                        }
                    },
                    {
                        targets: 4,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<span class="badge badge-light-info fw-bold">${row.country_label ?? ""}</span>`;
                        }
                    },
                    {
                        targets: 5,
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            return `<span class="badge badge-light-primary fw-bold">${row.city_label ?? ""}</span>`;
                        }
                    },
                    {
                        targets: 6,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var checked = data ? "checked" : "";
                            return `<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                    <input class="form-check-input active-toggle" type="checkbox" ${checked} data-id="${row.id}" />
                                </label>`;
                        }
                    },
                    {
                        targets: 7,
                        orderable: true,
                        searchable: false,
                        render: function(data) {
                            return `<span class="text-gray-700 fw-semibold">${data ?? ""}</span>`;
                        }
                    },
                    {
                        targets: 8,
                        orderable: false,
                        searchable: false,
                        className: "text-end",
                        render: function(data, type, row) {
                            return `<a class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-btn"
                                        title="Edit"
                                        data-id="${row.id}">
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
                storeUrl: "{{ route('regions.store') }}",
                datatable: dt,
                labels: {
                    create: "+ Create Region",
                    cancel: "× Cancel"
                }
            });

            // ── 4. EditPlugin ─────────────────────────────────────────────────────
            EditPlugin.init({
                updateUrl: "{{ route('regions.update', ['region' => ':id']) }}",
                datatable: dt,
                selector: ".edit-btn",
                columns: [{
                        field: "title_en",
                        type: "text",
                        target: 2,
                        rules: {
                            required: true,
                            max: 255
                        },
                        messages: {
                            required: "English title is required.",
                            max: "Max 255 characters."
                        },
                    },
                    {
                        field: "title_ar",
                        type: "text",
                        target: 3,
                        rules: {
                            required: true,
                            max: 255
                        },
                        messages: {
                            required: "Arabic title is required.",
                            max: "Max 255 characters."
                        },
                    },
                    {
                        field: "country_id",
                        type: "select",
                        target: 4,
                        options: countryOptions,
                        rules: {
                            required: true
                        },
                        messages: {
                            required: "Please select a country."
                        },
                    },
                    {
                        field: "city_id",
                        type: "select",
                        target: 5,
                        options: [],
                        dependsOn: "country_id",
                        dependsUrl: citiesSelectUrl,
                        rules: {
                            required: true
                        },
                        messages: {
                            required: "Please select a city."
                        },
                    },
                    {
                        field: "is_active",
                        type: "toggle",
                        target: 6,
                    },
                ],
                mapRow: function(response) {
                    return response.data;
                },
                notifications: {
                    successTitle: "Updated",
                    successText: "Region updated successfully.",
                    errorTitle: "Error",
                    errorText: "Something went wrong. Please try again.",
                },
            });

            // ── 5. Toggle Plugin ────────────────────────────────────────────────
            TogglePlugin.init({
                toggleUrl: "{{ route('regions.toggle-activate', ['region' => ':id']) }}",
                selector: ".active-toggle"
            });

            // ── 6. Delete Plugin ────────────────────────────────────────────────
            DeletePlugin.init({
                deleteUrl: "{{ route('regions.destroy', ['region' => ':id']) }}",
                datatable: dt,
                selector: ".delete-btn"
            });
        });
    </script>
@endsection
