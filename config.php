<?php

$config = (object) array
(
    "geoip_db" => 'GeoLite2-City.mmdb', // path to GeoLite2-City.mmdb database
    "csp" => true, // adds a Content-Security-Policy (CSP) security header with random nonce
    "sri" => true // adds a Subresource Integrity (SRI) hash for css/js integrity checks
);

?>
