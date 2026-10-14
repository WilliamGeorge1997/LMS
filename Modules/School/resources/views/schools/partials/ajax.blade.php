<option value="" disabled selected>{{ __('school::placeholders.select_school') }}</option>
@foreach ($schools as $school)
    <option value="{{ $school->id }}">
        {{ $school->getTranslation('title', 'en') }} - {{ $school->getTranslation('title', 'ar') }}
    </option>
@endforeach
