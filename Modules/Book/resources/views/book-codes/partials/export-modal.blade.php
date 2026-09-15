<div class="modal fade" id="export-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">{{ __('book::buttons.export_excel') }}</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="export-form" class="form"
                    action="{{ url('/admin/book-codes/export') }}"
                    method="GET">
                    
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mb-2">{{ __('book::attributes.type') }}</label>
                        <select name="type" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                            <option value="all">{{ __('book::attributes.all') }}</option>
                            <option value="student">{{ __('book::attributes.student') }}</option>
                            <option value="teacher">{{ __('book::attributes.teacher') }}</option>
                        </select>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mb-2">{{ __('book::attributes.is_used') }}</label>
                        <select name="is_used" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                            <option value="all">{{ __('book::attributes.all') }}</option>
                            <option value="1">{{ __('book::attributes.yes') }}</option>
                            <option value="0">{{ __('book::attributes.no') }}</option>
                        </select>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mb-2">{{ __('book::attributes.is_active') }}</label>
                        <select name="is_active" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                            <option value="all">{{ __('book::attributes.all') }}</option>
                            <option value="1">{{ __('book::attributes.active') }}</option>
                            <option value="0">{{ __('book::attributes.not_active') }}</option>
                        </select>
                    </div>

                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mb-2">{{ __('book::attributes.school') }}</label>
                        <select name="school_id" class="form-select form-select-solid" data-control="select2" data-placeholder="{{ __('book::attributes.all') }}" data-allow-clear="true">
                            <option value=""></option>
                            @foreach ($viewModel->schoolsByTenant() as $school)
                                <option value="{{ $school->id }}">{{ $school->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mb-2">{{ __('book::attributes.book') }} ({{ __('book::attributes.multiple') }})</label>
                        <select name="book_ids[]" class="form-select form-select-solid" data-control="select2" data-placeholder="{{ __('book::attributes.all') }}" data-allow-clear="true" multiple="multiple">
                            <option value=""></option>
                            @foreach ($viewModel->booksByTenant() as $book)
                                <option value="{{ $book->id }}">{{ $book->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mb-2">{{ __('book::attributes.from') }}</label>
                                <input class="form-control form-control-solid" placeholder="Pick date" name="from_date" type="date" />
                            </div>
                        </div>
                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mb-2">{{ __('book::attributes.to') }}</label>
                                <input class="form-control form-control-solid" placeholder="Pick date" name="to_date" type="date" />
                            </div>
                        </div>
                    </div>

                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('book::buttons.cancel') }}</button>
                        <button type="submit" class="btn btn-primary" data-kt-export-modal-action="submit">
                            <span class="indicator-label">{{ __('book::buttons.export_excel') }}</span>
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
