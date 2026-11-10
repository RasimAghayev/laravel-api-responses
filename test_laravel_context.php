<?php
namespace {
    require __DIR__ . '/vendor/autoload.php';

    // Bootstrap a minimal Laravel application
    $app = new Illuminate\Foundation\Application(
        dirname(__DIR__)
    );

    // Register the essential service providers
    $app->register(Illuminate\Foundation\Providers\FoundationServiceProvider::class);
    $app->register(Illuminate\Foundation\Providers\ArtisanServiceProvider::class);

    // Boot the application
    $app->boot();

    echo "=== Test: now() in global namespace after Laravel boot ===\n";
    $t = now();
    echo "OK: " . get_class($t) . "\n";

    echo "=== Test: now() from within package namespace ===\n";
    $r = \RasimAghayev\LaravelApiResponses\Responses\SuccessApiResponse::make(['test' => 'data'], 200);
    $request = \Illuminate\Http\Request::create('/api/test', 'GET');
    try {
        $out = $r->toResponse($request);
        echo "Status: " . $out->getStatusCode() . "\n";
        echo "Body: " . $out->getContent() . "\n";
    } catch (\Throwable $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}