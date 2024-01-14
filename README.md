# IP Info page

![made with php](https://img.shields.io/badge/made%20with-php-%23777BB4?logo=php&logoColor=ffffff)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Test](https://github.com/c0m4r/ip-info-page/workflows/PHPMD/badge.svg)](https://github.com/c0m4r/ip-info-page/actions)
[![CodeFactor](https://www.codefactor.io/repository/github/c0m4r/ip-info-page/badge)](https://www.codefactor.io/repository/github/c0m4r/ip-info-page)

IP Info page with geoip detection. Written in PHP, built with Twig, Bootstrap and GeoIP2. Supports CloudFlare.

<div align="center">

![image](https://github.com/c0m4r/ip-info-page/assets/6292788/e6fd7f92-95b0-45ae-9d74-8e6a7611da36)
 
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white) 
![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white) 
![Bootstrap](https://img.shields.io/badge/bootstrap-%238511FA.svg?style=for-the-badge&logo=bootstrap&logoColor=white) 
![PWA](https://img.shields.io/badge/webapp-black.svg?style=for-the-badge&logo=pwa&logoColor=white)

![image](https://github.com/c0m4r/ip-info-page/assets/6292788/4bfc8fc3-fb23-4386-87e8-1e22c686aefb)

</div>

## Dependencies

[PHP](https://www.php.net/downloads.php) | 
[Composer](https://getcomposer.org/download/) | 
[geoip2/geoip2](https://github.com/maxmind/GeoIP2-php) | 
[twbs/bootstrap](https://getbootstrap.com/docs/5.3/getting-started/download/#composer) | 
[twig/twig](https://twig.symfony.com/doc/3.x/intro.html#installation) | 
[Globe Africa](https://icons8.com/icon/dxoYK8bxqiJr/globe-africa) icon by [Icons8](https://icons8.com/)

## Installation

1. [Install Composer](https://getcomposer.org/download/) (PHP modules required: curl, mbstring, openssl, phar)
2. Download required libraries: `php composer.phar update`
3. Download [GeoIP2 Lite database](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data),
   edit config.php and change the path to the GeoLite2-City.mmdb file respectively.
5. Edit manifest.json and change `start_url` for PWA.

#### Quick setup

```bash
git clone https://github.com/c0m4r/ip-info-page.git
cd ip-info-page
wget https://getcomposer.org/download/2.6.6/composer.phar
echo "72600201c73c7c4b218f1c0511b36d8537963e36aafa244757f52309f885b314 composer.phar" | sha256sum -c || rm composer.phar
php composer.phar update
```

#### Docker

```bash
git clone https://github.com/c0m4r/ip-info-page.git
cd ip-info-page
wget https://getcomposer.org/download/2.6.6/composer.phar
echo "72600201c73c7c4b218f1c0511b36d8537963e36aafa244757f52309f885b314 composer.phar" | sha256sum -c || rm composer.phar
php composer.phar update
docker compose up -d
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

When behind CloudFlare it will use [CF-Connecting-IP](https://developers.cloudflare.com/fundamentals/reference/http-request-headers/#cf-connecting-ip) 
HTTP Header for IP detection. While using curl keep in mind that things like 
[Bot Fight Mode](https://developers.cloudflare.com/learning-paths/get-started-free/security/bot-fight-mode/) 
and [Browser Integrity Check](https://developers.cloudflare.com/waf/tools/browser-integrity-check/) 
will most likely prevent you from reading anything, due to the altered javascript challenge page.

For other proxies X-Forwarded-For is used.

## License

> MIT License
> 
> Copyright (c) 2023 c0m4r
> 
> Permission is hereby granted, free of charge, to any person obtaining a copy
> of this software and associated documentation files (the "Software"), to deal
> in the Software without restriction, including without limitation the rights
> to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
> copies of the Software, and to permit persons to whom the Software is
> furnished to do so, subject to the following conditions:
> 
> The above copyright notice and this permission notice shall be included in all
> copies or substantial portions of the Software.
> 
> THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
> IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
> FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
> AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
> LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
> OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
> SOFTWARE.
