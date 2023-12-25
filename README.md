# IP info page

IP info page with geoip detection. Written in PHP, built with Twig, Bootstrap and GeoIP2. Supports CloudFlare.

![image](https://github.com/c0m4r/ip-info-page/assets/6292788/87b89598-e68c-4892-9d17-8d8ec625c71b)

OS and Language are detected using native JavaScript. In the case of being unable to read some or none of the geoip data, it will show only data it was able to detect.

## Demo

* PHPSandbox: https://phpsandbox.io/n/ip-info-page-aeljb

## Dependencies

* [PHP](https://www.php.net/downloads.php)
* [Composer](https://getcomposer.org/download/)
  * [geoip2/geoip2](https://github.com/maxmind/GeoIP2-php)
  * [twbs/bootstrap](https://getbootstrap.com/docs/5.3/getting-started/download/#composer)
  * [twig/twig](https://twig.symfony.com/doc/3.x/intro.html#installation)
  * [Globe Africa](https://icons8.com/icon/dxoYK8bxqiJr/globe-africa) icon by [Icons8](https://icons8.com/)

## Installation

1. [Install Composer](https://getcomposer.org/download/) (PHP modules required: curl, mbstring, openssl, phar)
2. Download required libraries: `php composer.phar update`
3. Download [GeoIP2 Lite database](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data), edit index.php and change the path to the GeoLite2-City.mmdb file:

```php
$cityDbReader = new Reader('/path/to/GeoLite2-City.mmdb');
```

## JSON mode

If request is sent using curl or wget, it will print IP info in JSON:

```json
{
    "ip": "127.0.0.1",
    "geoip": true,
    "country": "Poland",
    "region": "Masovian Voivodeship",
    "city": "Warsaw",
    "postal": "00-022",
    "ua": "curl\/8.5.0"
}
```

In the case of being unable to read the geoip data, it will set ```geoip => false``` and print only IP and UA information.

## Behind proxy

When behind CloudFlare it will use [CF-Connecting-IP](https://developers.cloudflare.com/fundamentals/reference/http-request-headers/#cf-connecting-ip) HTTP Header for IP detection. While using curl keep in mind that things like [Bot Fight Mode](https://developers.cloudflare.com/learning-paths/get-started-free/security/bot-fight-mode/) and [Browser Integrity Check](https://developers.cloudflare.com/waf/tools/browser-integrity-check/) will most likely prevent you from reading anything, due to the altered javascript challenge page.

For other proxies X-Forwarded-For is used.
