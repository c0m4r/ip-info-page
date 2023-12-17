<?php

// IP Info Page
// https://github.com/c0m4r/ip-info-page

require_once 'vendor/autoload.php'; // Composer
use GeoIp2\Database\Reader; // GeoIP2-php

$ua = $_SERVER['HTTP_USER_AGENT'];

// Get CloudFlare CF_CONNECTING_IP if present
if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

// Load GeoIP2-php database
// https://dev.maxmind.com/geoip/geolite2-free-geolocation-data
try {
    $cityDbReader = new Reader('GeoLite2-City_20231215/GeoLite2-City.mmdb');
    $geoip = $cityDbReader->city($ip);
} catch(Exception $e) {
    $geoip = array();
}

// Print IP info
if(preg_match('/^(curl|wget)/i', $_SERVER['HTTP_USER_AGENT'])) {
    // JSON (curl/wget only)
    header('Content-Type: application/json; charset=utf-8');

    if(is_object($geoip))
    {
        $data = array(
            'ip' => $ip,
            'geoip' => true,
            'country' => $geoip->country->name,
            'region' => $geoip->mostSpecificSubdivision->name,
            'city' => $geoip->city->name,
            'postal' => $geoip->postal->code,
            'ua' => $ua
        );
    } else
    {
        $data = array('ip' => $ip, 'ua' => $ua, 'geoip' => false);
    }

    echo json_encode($data, JSON_PRETTY_PRINT);
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
