<?php

// IP info page with geoip detection
// https://github.com/c0m4r/ip-info-page
//
// MIT License
//
// Copyright (c) 2023 c0m4r
//
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in all
// copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
// SOFTWARE.

declare(strict_types=1);

use GeoIp2\Database\Reader; // GeoIP2-php
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\City;

/** @var array{geoip_db: string, csp: bool, sri: bool} $config */
$config = require __DIR__ . '/config.php';

if($_GET !== [] || $_POST !== []) {
    http_response_code(405); exit("405 Method Not Allowed\n");
}

if(is_file(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php'; // Composer
} else {
    http_response_code(503); exit("Composer library not found\n");
}

// SRI Hash generator
function sri(string $file): string {
    $hash = hash_file('sha384', $file, true);
    if($hash === false) {
        throw new RuntimeException("Unable to read $file");
    }
    return "sha384-".base64_encode($hash);
}

// User agent, stripped to 512 chars (escaping is left to Twig and json_encode)
if(isset($_SERVER['HTTP_USER_AGENT']) && is_string($_SERVER['HTTP_USER_AGENT'])) {
    $ua = mb_substr(mb_scrub($_SERVER['HTTP_USER_AGENT'], 'UTF-8'), 0, 512, 'UTF-8');
} else {
    $ua = null;
}

// Get CloudFlare CF_CONNECTING_IP if present, then X-Forwarded-For, then the peer address
$ip = null;
foreach(['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $header) {
    $candidate = filter_var($_SERVER[$header] ?? null, FILTER_VALIDATE_IP);
    if(is_string($candidate)) {
        $ip = $candidate;
        break;
    }
}
if($ip === null) {
    http_response_code(501); exit("501 Not Implemented\n");
}

header('X-Content-Type-Options: nosniff');

// CSP header
if($config['csp']) {
    $nonce = bin2hex(random_bytes(32));
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-$nonce'; base-uri 'self';");
}

// Load GeoIP2-php database
// https://dev.maxmind.com/geoip/geolite2-free-geolocation-data
try {
    $cityDbReader = new Reader($config['geoip_db']);
    $geoip = $cityDbReader->city($ip);
} catch(AddressNotFoundException) {
    $geoip = array("notfound" => "IP address not found in GeoIP database.");
} catch(Exception) {
    $geoip = array();
}

// Print IP info
if($ua !== null && preg_match('/^(curl|wget)/i', $ua) === 1) {
    header('Content-Type: application/json; charset=utf-8');

    if($geoip instanceof City) {
        $data = array(
            'ip' => $ip,
            'geoip' => true,
            'country' => $geoip->country->name,
            'region' => $geoip->mostSpecificSubdivision->name,
            'city' => $geoip->city->name,
            'postal' => $geoip->postal->code,
            'ua' => $ua
        );
    } else {
        $data = array('ip' => $ip, 'ua' => $ua, 'geoip' => false);
    }

    echo json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)."\n";
} else
{
    // Twig
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/tpl');
    $twig = new \Twig\Environment($loader);

    echo $twig->render('index.html', [
        'config' => $config,
        'ip' => $ip,
        'ua' => $ua,
        'bootstrap_css_sri_hash' => sri(__DIR__ . '/vendor/twbs/bootstrap/dist/css/bootstrap.min.css'),
        'style_css_sri_hash' => sri(__DIR__ . '/css/style.css'),
        'navigator_js_sri_hash' => sri(__DIR__ . '/js/navigator.js'),
        'geoip' => $geoip
    ]);
}
