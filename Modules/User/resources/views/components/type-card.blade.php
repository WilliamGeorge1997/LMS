@props([
    'title',
    'image',
    'buttonText',
    'buttonUrl' => '#',
])

<div class="card card-hover h-100 p-3 rounded-4 border shadow-sm">
    <!-- Image Box -->
    <div class="ratio ratio-4x5 rounded-3 overflow-hidden bg-light">
        <img src="{{ $image }}" alt="{{ $title }}" class="w-100 h-100 object-fit-cover">
    </div>

    <!-- Title -->
    <h2 class="card-title h5 fw-bold my-3 text-start">
        {{ $title }}
    </h2>

    <!-- Action Button -->
    <a href="{{ $buttonUrl }}" class="btn btn-primary rounded-pill w-100 fw-semibold py-2">
        {{ $buttonText }}
    </a>
</div>
