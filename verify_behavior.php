<?php
namespace {
    require __DIR__ . '/vendor/autoload.php';

    use Illuminate\Http\Request;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Resources\Json\JsonResource;
    use RasimAghayev\LaravelApiResponses\Responses\SuccessApiResponse;
    use RasimAghayev\LaravelApiResponses\Responses\ErrorApiResponse;
    use RasimAghayev\LaravelApiResponses\Responses\ErrorValidationResponse;

    // Simulate Laravel's container alias for now()
    // In real Laravel, the Application class registers aliases like 'now' => Illuminate\Support\Facades\Date
    // but the function now() is NOT aliased - it's a namespaced function.
    // Let's test what happens in a real Laravel app context.

    $request = Request::create('/api/test', 'POST');

    echo "=== Test 1: ErrorValidationResponse with errors array (422) ===\n";
    $r = ErrorValidationResponse::make(['errors' => ['email' => ['required']]], 422);
    $out = $r->toResponse($request);
    echo "Status: " . $out->getStatusCode() . "\n";
    echo "Body: " . $out->getContent() . "\n\n";

    echo "=== Test 2: ErrorApiResponse with errors array (422) [as TransactionHelper does] ===\n";
    $r = ErrorApiResponse::make(['email' => ['required']], 422);
    $out = $r->toResponse($request);
    echo "Status: " . $out->getStatusCode() . "\n";
    echo "Body: " . $out->getContent() . "\n\n";

    echo "=== Test 3: ErrorValidationResponse with string (422) ===\n";
    $r = ErrorValidationResponse::make('Validation failed', 422);
    $out = $r->toResponse($request);
    echo "Status: " . $out->getStatusCode() . "\n";
    echo "Body: " . $out->getContent() . "\n\n";

    echo "=== Test 4: SuccessApiResponse default ===\n";
    $r = SuccessApiResponse::make(['data' => 'x']);
    $out = $r->toResponse($request);
    echo "Status: " . $out->getStatusCode() . "\n";
    echo "Body: " . $out->getContent() . "\n";

    echo "=== Test 5: JsonResponse getData types ===\n";
    $r = new JsonResponse(['x' => 1]);
    $d = $r->getData();
    echo "getData() type: " . gettype($d) . "\n";
    $d2 = $r->getData(true);
    echo "getData(true) type: " . gettype($d2) . "\n";

    echo "=== Test 6: JsonResource resolve ===\n";
    $res = new class extends JsonResource {
        public function toArray($request) { return ['foo' => 'bar']; }
    };
    $resolved = $res->resolve($request);
    echo "resolve() returns: " . json_encode($resolved) . "\n";
}