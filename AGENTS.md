<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- phpunit/phpunit (PHPUNIT) - v12

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>

=== web & tenant guidelines ===

# Web Application & Tenant Guidelines

## Architecture & Conventions
- Reusable code, no duplication. Keep business logic modular and DRY.
- Thin controllers and service layer: business logic belongs in Services (e.g. `MyBooksService`), not controllers.
- Middlewares must be defined inside the controller's `middleware()` method, not in route files.
- Dedicated form requests: always use Form Request classes for validation, without `Web` prefix if already in `Web` namespace (e.g., `UserRegisterRequest`).
- Route names must not prefix `web.` (use `welcome`, `login`, `my-books`).
- Views must not include redundant `web` folders when already under resources (e.g., `user::auth.login`).
- Use centralized response helpers from `Common` module (`ResponseHelper`) for JSON responses.
- Store multi-step flow data (email, OTP state, user type) in session, never exposed in URL query parameters.
- Identify tenants by subdomain/slug (e.g. `canterbury`), never by database ID.
- Do not create migrations, tables, or columns for static styling or tenant theme assets.

## Styling & CSS
- Bootstrap 5 First: always use native Bootstrap 5 utility classes and components as the primary foundation.
- Minimum CSS: custom CSS in `custom.css` is only a fallback when Bootstrap lacks the utility.
- Zero `overflow: hidden`: strictly prohibited across all web application views.
- Zero inline styles: never use `style="..."` on HTML elements; use classes or native HTML attributes.
- Use `rem`, never `px`: in CSS files, always use `rem` for measurements and sizing.
- Asset directory structure:
  - `public/web/assets/general/`: shared styles, generic scripts, common assets.
  - `public/web/assets/tenants/{tenant}/`: dynamic tenant-specific theme tokens (`css/theme.css`) and assets (`images/`).
- Dynamic tenant identity:
  - Colors and backgrounds must follow the active tenant's theme tokens (`--bs-primary`, `--bs-primary-rgb`, `--bs-body-bg`, `--bs-body-color`).
  - Never allow Bootstrap default blue (`#0d6efd`, `#cfe2ff`) to show in buttons, badges, or input focus rings.
  - Modals and cards must follow the tenant identity (surface background, tenant logo, auth-style inputs and buttons).

## JavaScript & UX
- jQuery-first and unified helpers: use jQuery where present; do not introduce extra frontend packages (no HTMX, no Livewire).
- Simple, readable, and minimized JS.
- AJAX-first UX: form submissions use `[data-ajax]` to prevent page reloads.
- Submit buttons show a spinner/loader while preserving their brand identity color on submit.
- Inline form feedback: show error messages as small red text paragraphs below the input, never top alert banners.
- Synchronized focus: input groups and prepended icons must highlight focus borders simultaneously without delay.

## Localization & Content
- 100% translated: all user-facing text must be translated in both English and Arabic.
- No hardcoded defaults in views: use translation keys `__()` and dynamic tenant bindings (`tenant()?->name`).
- Concise keys: translation keys must be strictly 1 or 2 words maximum.

## Media, Assets & Layout
- Tenant logos: always use `logo.webp` (never SVG).
- Tenant favicons: always use `favicon.ico`.
- Page title format: `{Tenant Name} - {Page Title}`.
- Do not generate AI images; use official Stitch design assets.
- Shared layouts and master Blade files live in `Modules/Common/resources/views/web/`.
- Sidebar structure and styling must remain protected without changes.
- Books: open in new tab directly via file path (`target="_blank"`), paginated (12 per page), no carousel sliders/arrows, dark-to-light gradient fallback covers, and full-width tenant primary action buttons.

