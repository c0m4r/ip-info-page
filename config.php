<?php

$config = (object) array
(
    "csp" => true, // adds a Content-Security-Policy (CSP) security header with random nonce
    "sri" => true // adds a Subresource Integrity (SRI) hash for css/js integrity checks
);

?>
