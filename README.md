# IP Info Page

Yet another what-is-my-ip-page with geoip detection. Built on Bootstrap and GeoIP2 Lite. Supports CloudFlare.

## Demo

PHPSandbox: https://phpsandbox.io/n/ip-info-page-aeljb

## Dependencies

* [PHP 8.2](https://www.php.net/downloads.php)
* [Composer](https://getcomposer.org/download/)
  * [geoip2/geoip2](https://github.com/maxmind/GeoIP2-php)
  * [twbs/bootstrap](https://getbootstrap.com/docs/5.3/getting-started/download/#composer)
  * [twig/twig](https://twig.symfony.com/doc/3.x/intro.html#installation)

## TL;DR

```bash
#!/bin/bash
git clone https://github.com/c0m4r/ip-info-page.git && cd ip-info-page
wget -O composer-setup.php https://getcomposer.org/installer || curl -o composer-setup.php https://getcomposer.org/installer || echo "wget/curl not found, can't download"
wget -O composer-installer.sig https://composer.github.io/installer.sig || curl -o composer-installer.sig https://composer.github.io/installer.sig || echo "wget/curl not found, can't download"
if [[ $(sha384sum composer-setup.php | awk '{print $1}') -eq $(cat composer-installer.sig) ]]; then php composer-setup.php && php composer.phar update && rm -f composer-setup.php composer-installer.sig ; else echo "sig FAILED" ; fi
REMOTE_ADDR=127.0.0.1 HTTP_USER_AGENT=curl php index.php && echo -e "\ninstallation ok" || echo "installation failed"
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
$cityDbReader = new Reader('/path/to/GeoLite2-City.mmdb');
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

In the case of being unable to read the geoip data, it will set ```geoip => false``` and print only IP and UA information.

## Screenshot

![image](https://github.com/c0m4r/ip-info-page/assets/6292788/5492c376-bc49-4ee0-97d0-e2a41e55f128)

OS and Language are detected using native JavaScript. In the case of being unable to read some or none of the geoip data, it will show only data it was able to detect.

## CloudFlare

When behind CloudFlare it will use [CF-Connecting-IP](https://developers.cloudflare.com/fundamentals/reference/http-request-headers/#cf-connecting-ip) HTTP Header for IP detection. While using curl keep in mind that things like [Bot Fight Mode](https://developers.cloudflare.com/learning-paths/get-started-free/security/bot-fight-mode/) and [Browser Integrity Check](https://developers.cloudflare.com/waf/tools/browser-integrity-check/) will most likely prevent you from reading anything, due to the altered javascript challenge page.
