<?php
// Minimal health probe used by the runtime HEALTHCHECK.
// Returns 200 with a small JSON body when the PHP built-in server is up.
http_response_code(200);
header('Content-Type: application/json');
echo json_encode(['status' => 'ok', 'service' => 'laravel-api-responses']);
exit;