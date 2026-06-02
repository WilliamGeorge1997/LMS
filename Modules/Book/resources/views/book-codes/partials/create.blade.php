<div class="card shadow-sm border-0">
    <div class="card-header border-0 py-4">
        <h3 class="card-title fw-bold fs-5 m-0">{{ __('book::buttons.create_code') }}</h3>
    </div>
    <div class="card-body pt-0">
        <form id="create-form" action="{{ url('/admin/book-codes') }}" method="POST">
            @csrf
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="required form-label">{{ __('book::attributes.book') }}</label>
                    <select name="book_id" class="form-select form-select-solid">
                        <option value="" disabled selected>{{ __('book::placeholders.select_book') }}</option>
                        @foreach ($viewModel->booksByTenant() as $book)
                            <option value="{{ $book->id }}">{{ $book->title }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback d-block" data-error="book_id"></div>
                </div>

                <div class="col-md-6">
                    <label class="required form-label">{{ __('book::attributes.duration') }}</label>
                    <input type="number" name="duration" min="1" class="form-control form-control-solid"
                        placeholder="12" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="duration"></div>
                </div>

                <div class="col-md-6">
                    <label class="required form-label">{{ __('book::attributes.type') }}</label>
                    <div class="d-flex gap-6 mt-2">
                        <label class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="radio" name="type" value="student" />
                            <span class="form-check-label">{{ __('book::attributes.student') }}</span>
                        </label>
                        <label class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="radio" name="type" value="teacher" checked />
                            <span class="form-check-label">{{ __('book::attributes.teacher') }}</span>
                        </label>
                    </div>
                    <div class="invalid-feedback d-block" data-error="type"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('book::attributes.quantity') }}</label>
                    <input type="number" name="quantity" min="1" class="form-control form-control-solid"
                        placeholder="1" autocomplete="off" />
                    <div class="invalid-feedback d-block" data-error="quantity"></div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-8">
                <button type="button" id="create-cancel" class="btn btn-light">
                    {{ __('book::buttons.cancel') }}
                </button>
                <button type="submit" id="create-submit" class="btn btn-primary">
                    <span class="indicator-label">{{ __('book::buttons.submit') }}</span>
                    <span class="indicator-progress">
                        <span class="spinner-border spinner-border-sm align-middle"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
