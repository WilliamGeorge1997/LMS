@props([
    'title',
    'image',
    'buttonText',
    'type' => null,
    'buttonUrl' => '#',
])

@if($type)
    <form action="{{ route('select-type') }}" method="POST" class="h-100">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        <button type="submit" class="card card-hover h-100 p-3 rounded-4 border-0 shadow-sm text-center text-reset d-block w-100 bg-white">
            <!-- Image Box -->
            <div class="ratio ratio-4x5 rounded-3 bg-light">
                <img src="{{ $image }}" alt="{{ $title }}" class="w-100 h-100 object-fit-cover rounded-3">
            </div>

            <!-- Title -->
            <h2 class="card-title h5 fw-bold my-3 text-center">
                {{ $title }}
            </h2>

            <!-- Action Button -->
            <div class="btn btn-primary rounded-pill w-100 fw-semibold py-2">
                {{ $buttonText }}
            </div>
        </button>
    </form>
@else
    <a href="{{ $buttonUrl }}" class="card card-hover h-100 p-3 rounded-4 border-0 shadow-sm text-center text-decoration-none text-reset d-block bg-white">
        <!-- Image Box -->
        <div class="ratio ratio-4x5 rounded-3 bg-light">
            <img src="{{ $image }}" alt="{{ $title }}" class="w-100 h-100 object-fit-cover rounded-3">
        </div>

        <!-- Title -->
        <h2 class="card-title h5 fw-bold my-3 text-center">
            {{ $title }}
        </h2>

        <!-- Action Button -->
        <div class="btn btn-primary rounded-pill w-100 fw-semibold py-2">
            {{ $buttonText }}
        </div>
    </a>
@endif
