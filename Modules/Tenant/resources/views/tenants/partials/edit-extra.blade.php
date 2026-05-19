@php
    $formId = 'edit-form-' . $tenant->id;
    $internalNotes = data_get($tenant->data, 'internal_notes', '');
@endphp
<tr class="edit-extra-row" data-edit-for="{{ $tenant->id }}">
    <td colspan="8">
        <div class="edit-extra-panel">
            <p class="edit-extra-panel__title">{{ __('tenant::text.extra_fields') }}</p>
            <p class="edit-extra-panel__hint">{{ __('tenant::text.extra_fields_hint') }}</p>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label" for="internal_notes-{{ $tenant->id }}">
                        {{ __('tenant::attributes.internal_notes') }}
                    </label>
                    <textarea id="internal_notes-{{ $tenant->id }}" name="internal_notes" rows="2"
                        form="{{ $formId }}" class="form-control form-control-solid form-control-sm"
                        placeholder="{{ __('tenant::placeholders.internal_notes') }}">{{ $internalNotes }}</textarea>
                    <div class="invalid-feedback d-block" data-error="internal_notes"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="contact_email-{{ $tenant->id }}">
                        {{ __('tenant::attributes.contact_email') }}
                    </label>
                    <input id="contact_email-{{ $tenant->id }}" type="email" name="contact_email"
                        form="{{ $formId }}" class="form-control form-control-solid form-control-sm"
                        value="{{ data_get($tenant->data, 'contact_email', '') }}"
                        placeholder="{{ __('tenant::placeholders.contact_email') }}" />
                    <div class="invalid-feedback d-block" data-error="contact_email"></div>
                </div>
            </div>
        </div>
    </td>
</tr>
