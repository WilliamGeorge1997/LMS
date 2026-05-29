<option value="" disabled selected>{{ __('level::placeholders.select_category') }}</option>
@foreach ($categories as $category)
    <option value="{{ $category->id }}">
        {{ $category->getTranslation('title', 'en') }} - {{ $category->getTranslation('title', 'ar') }}
    </option>
@endforeach
