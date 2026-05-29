<option value="" disabled selected>{{ __('book::placeholders.select_level') }}</option>
@foreach ($levels as $level)
    <option value="{{ $level->id }}">
        {{ $level->getTranslation('title', 'en') }} - {{ $level->getTranslation('title', 'ar') }}
    </option>
@endforeach
