<?php

// IP info page
// https://github.com/c0m4r/ip-info-page

if(!empty($_GET) or !empty($_POST)) {
    header('HTTP/1.0 405 Method Not Allowed'); exit("405 Method Not Allowed\n");
}

if(is_file('vendor/autoload.php')) {
    require_once 'vendor/autoload.php'; // Composer
} else {
    exit("Composer not installed\n");
}

use GeoIp2\Database\Reader; // GeoIP2-php

if(isset($_SERVER['HTTP_USER_AGENT'])) {
    $ua = htmlspecialchars($_SERVER['HTTP_USER_AGENT']);
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

// Load GeoIP2-php database
// https://dev.maxmind.com/geoip/geolite2-free-geolocation-data
try {
    $cityDbReader = new Reader('.geolite2/GeoLite2-City.mmdb');
    $geoip = $cityDbReader->city($ip);
} catch(Exception $e) {
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

    echo $twig->render('index.html', [
        'ip' => $ip,
        'ua' => $ua,
        'geoip' => $geoip
    ]);
}

?>
