# IP Info Page

Yet another what-is-my-ip-page with geoip detection. Built on Bootstrap and GeoIP2 Lite. Supports CloudFlare.

## Dependencies

* [PHP 8.2](https://www.php.net/downloads.php)
* [Composer](https://getcomposer.org/download/)
  * [geoip2/geoip2](https://github.com/maxmind/GeoIP2-php)
  * [twbs/bootstrap](https://getbootstrap.com/docs/5.3/getting-started/download/#composer)
  * [twig/twig](https://twig.symfony.com/doc/3.x/intro.html#installation)

## TL;DR

```
git clone https://github.com/c0m4r/ip-info-page.git
cd ip-info-page
wget https://getcomposer.org/installer -O composer-setup.php
php composer-setup.php
php composer.phar update
mkdir GeoLite2-City_20231215
echo "Now copy GeoLite2-City.mmdb here: $PWD/GeoLite2-City_20231215/"
```

## Installation

1. [Install Composer](https://getcomposer.org/download/)

There are a few php.ini settings required for Composer to work:

```ini
allow_url_fopen = On
allow_url_include = On
extension=openssl
extension=phar
```

2. Download required libraries:

```bash
php composer.phar update
```

3. Download [GeoIP2 Lite database](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data) and change the path to the GeoLite2-City.mmdb database file accordingly.

```php
$cityDbReader = new Reader('GeoLite2-City_20231215/GeoLite2-City.mmdb');
```

## JSON

Accessed via curl or wget will print IP info in JSON:

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

In case being unable to read geoip data, it will set geoip => false and print IP and UA only.

## Screenshot

![image](https://github.com/c0m4r/ip-info-page/assets/6292788/5492c376-bc49-4ee0-97d0-e2a41e55f128)

OS and Language are detected using native JavaScript. In case being unable to read some of the geoip data, it will show only data it was able to read or none.

## CloudFlare

When behind CloudFlare it will use [CF-Connecting-IP](https://developers.cloudflare.com/fundamentals/reference/http-request-headers/#cf-connecting-ip) HTTP Header for IP detection. While using curl keep in mind that things like [Bot Fight Mode](https://developers.cloudflare.com/learning-paths/get-started-free/security/bot-fight-mode/) and [Browser Integrity Check](https://developers.cloudflare.com/waf/tools/browser-integrity-check/) will most likely prevent you from reading anything, due to the altered javascript challenge page.
