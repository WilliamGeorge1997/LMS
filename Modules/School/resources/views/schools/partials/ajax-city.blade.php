<option value="" disabled selected>{{ __('school::placeholders.select_city') }}</option>
@foreach ($cities as $city)
    <option value="{{ $city->id }}">
        {{ $city->getTranslation('title', 'en') }} - {{ $city->getTranslation('title', 'ar') }}
    </option>
@endforeach
