#!/bin/sh
# Health probe used by docker-compose healthcheck and the runtime HEALTHCHECK.
# Exits 0 when the PHP built-in server answers 200 on /health.php.
php -r '
$errno = 0;
$errstr = "";
$c = @fsockopen("127.0.0.1", 8080, $errno, $errstr, 5);
if (!$c) { exit(1); }
fwrite($c, "GET /health.php HTTP/1.1\r\nHost: localhost\r\n\r\n");
stream_set_timeout($c, 3);
$r = stream_get_contents($c);
fclose($c);
if (strpos($r, "200") === false) { exit(1); }
exit(0);
'