<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? tenant('name') ?? tenant()?->name }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-dark text-white text-center py-4 rounded-top-3">
                        <h4 class="mb-0 fw-bold">{{ tenant('name') ?? tenant()?->name }}</h4>
                    </div>

                    <div class="card-body p-4 text-secondary">
                        @yield('content')
                    </div>

                    <div class="card-footer bg-white text-center text-muted border-top-0 pb-4 pt-2">
                        <p class="small mb-1">
                            This is an automated notification from <span class="fw-semibold text-secondary">{{ tenant('name') ?? tenant()?->name }}</span>.
                        </p>
                        <p class="small mb-0">Please do not reply directly to this email.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
