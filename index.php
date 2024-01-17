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

require_once 'config.php';

if(!empty($_GET) or !empty($_POST)) {
    header('HTTP/1.0 405 Method Not Allowed'); exit("405 Method Not Allowed\n");
}

if(is_file('vendor/autoload.php')) {
    require_once 'vendor/autoload.php'; // Composer
} else {
    header('HTTP/1.0 503 Service Unavailable'); exit("Composer library not found\n");
}

use GeoIp2\Database\Reader; // GeoIP2-php

if(isset($_SERVER['HTTP_USER_AGENT'])) {
    $ua = substr(htmlspecialchars($_SERVER['HTTP_USER_AGENT']),0,512);
} else {
    $ua = NULL;
}

// Get CloudFlare CF_CONNECTING_IP if present
if(isset($_SERVER['HTTP_CF_CONNECTING_IP']) and filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) and filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} elseif(isset($_SERVER['REMOTE_ADDR']) and filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
    $ip = $_SERVER['REMOTE_ADDR'];
} else {
    header('HTTP/1.0 501 Not Implemented'); exit("501 Not Implemented\n");
}

// CSP header
if($config->csp == true) {
    $nonce = bin2hex(openssl_random_pseudo_bytes(32));
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-$nonce'; base-uri 'self';");
}

// Load GeoIP2-php database
// https://dev.maxmind.com/geoip/geolite2-free-geolocation-data
try {
    $cityDbReader = new Reader($config->geoip_db);
    $geoip = $cityDbReader->city($ip);
} catch(GeoIp2\Exception\AddressNotFoundException) {
    $geoip = array("notfound" => "IP address not found in GeoIP database.");
} catch(Exception $e) {
    var_dump($e);
    $geoip = array();
}

// Print IP info
if(preg_match('/^(curl|wget)/i', $ua)) {
    header('Content-Type: application/json; charset=utf-8');

    if(is_object($geoip)) {
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

    echo json_encode($data, JSON_PRETTY_PRINT)."\n";
} else
{
    // Twig
    $loader = new \Twig\Loader\FilesystemLoader('tpl');
    $twig = new \Twig\Environment($loader);

    // SRI Hash generator
    function sri($file) {
        $handle = fopen($file, "r");
        $body = fread($handle, filesize($file));
        $hash = hash('sha384', $body, true);
        return "sha384-".base64_encode($hash);
    }

    echo $twig->render('index.html', [
        'config' => $config,
        'ip' => $ip,
        'ua' => $ua,
        'bootstrap_css_sri_hash' => sri("vendor/twbs/bootstrap/dist/css/bootstrap.min.css"),
	'style_css_sri_hash' => sri("css/style.css"),
        'navigator_js_sri_hash' => sri("js/navigator.js"),
        'geoip' => $geoip
    ]);
}

?>
