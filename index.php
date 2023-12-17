<?php

require_once 'vendor/autoload.php'; // composer
use GeoIp2\Database\Reader; // geoip

$ua = $_SERVER['HTTP_USER_AGENT'];

if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    $cfcountry = $_SERVER['HTTP_CF_IPCOUNTRY'];
}
else {
    $ip = $_SERVER['REMOTE_ADDR'];
    $cfcountry = '';
}

// https://dev.maxmind.com/geoip/geolite2-free-geolocation-data
try {
    $cityDbReader = new Reader('GeoLite2-City_20231215/GeoLite2-City.mmdb');
    $geoip = $cityDbReader->city($ip);
} catch(Exception $e) {
    $geoip = array();
}

// Twig
$loader = new \Twig\Loader\FilesystemLoader('tpl');
$twig = new \Twig\Environment($loader);

echo $twig->render('index.html', [
    'ip' => $ip,
    'ua' => $ua,
    'geoip' => $geoip
]);

?>
