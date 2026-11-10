<?php
namespace RasimAghayev\LaravelApiResponses\Responses {
    require __DIR__ . '/vendor/autoload.php';
    use function Illuminate\Support\now;

    $x = now();
    echo "now() OK: " . get_class($x) . "\n";
}

namespace {
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Resources\Json\JsonResource;

    $r = new JsonResponse(['x' => 1]);
    $d = $r->getData();
    echo "getData() type: " . gettype($d) . "\n";
    $d2 = $r->getData(true);
    echo "getData(true) type: " . gettype($d2) . "\n";
    echo "json_decode on stdClass: ";
    $j = json_decode($d, true, 512, JSON_THROW_ON_ERROR);
    echo "OK\n";

    $req = Illuminate\Http\Request::create('/api/x', 'GET');
    $res = new class extends JsonResource {
        public function toArray($request) { return ['foo' => 'bar']; }
    };
    $resolved = $res->resolve($req);
    echo "resolve() returns: " . json_encode($resolved) . "\n";
}