<option value="" disabled selected>{{ __('school::placeholders.select_region') }}</option>
@foreach ($regions as $region)
    <option value="{{ $region->id }}">
        {{ $region->getTranslation('title', 'en') }} - {{ $region->getTranslation('title', 'ar') }}
    </option>
@endforeach
