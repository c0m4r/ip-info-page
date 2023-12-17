# IP Info Page

Built on Bootstrap and GeoIP2 Lite. Supports CloudFlare.

## Dependencies

* [PHP 8.2](https://www.php.net/downloads.php)
* [Composer](https://getcomposer.org/download/)
  * [geoip2/geoip2](https://github.com/maxmind/GeoIP2-php)
  * [twbs/bootstrap](https://getbootstrap.com/docs/5.3/getting-started/download/#composer)
  * [twig/twig](https://twig.symfony.com/doc/3.x/intro.html#installation)

## Installation

1. [Install Composer](https://getcomposer.org/download/)
2. Download required libraries:

```bash
php composer.phar update
```

3. Download [GeoIP2 Lite database](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data)

## PHP Settings

There are a few php.ini settings required for Composer to work:

```ini
allow_url_fopen = On
allow_url_include = On
extension=openssl
extension=phar
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

In case being unable to read geoip data, it will set geoip = false and print ip and ua only.

## Screenshot

![image](https://github.com/c0m4r/ip-info-page/assets/6292788/5492c376-bc49-4ee0-97d0-e2a41e55f128)
