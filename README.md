# IP Info page

![made with php](https://img.shields.io/badge/made%20with-php-%23777BB4?logo=php&logoColor=ffffff)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![CodeFactor](https://www.codefactor.io/repository/github/c0m4r/ip-info-page/badge)](https://www.codefactor.io/repository/github/c0m4r/ip-info-page)

IP Info page with geoip detection. Written in PHP, built with Twig, Bootstrap and GeoIP2. Supports CloudFlare.

<div align="center">

![image](https://github.com/c0m4r/ip-info-page/assets/6292788/e6fd7f92-95b0-45ae-9d74-8e6a7611da36)
 
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white) 
![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white) 
![Bootstrap](https://img.shields.io/badge/bootstrap-%238511FA.svg?style=for-the-badge&logo=bootstrap&logoColor=white) 
![PWA](https://img.shields.io/badge/webapp-black.svg?style=for-the-badge&logo=pwa&logoColor=white) 
[![Docker](https://img.shields.io/badge/docker-%230db7ed.svg?style=for-the-badge&logo=docker&logoColor=white)](https://hub.docker.com/r/c0m4r/ip-info-page)

![image](https://github.com/c0m4r/ip-info-page/assets/6292788/4bfc8fc3-fb23-4386-87e8-1e22c686aefb)

</div>

## Dependencies

[PHP](https://www.php.net/downloads.php) | 
[Composer](https://getcomposer.org/download/) | 
[geoip2/geoip2](https://github.com/maxmind/GeoIP2-php) | 
[twbs/bootstrap](https://getbootstrap.com/docs/5.3/getting-started/download/#composer) | 
[twig/twig](https://twig.symfony.com/doc/3.x/intro.html#installation) | 
[Globe Africa](https://icons8.com/icon/dxoYK8bxqiJr/globe-africa) icon by [Icons8](https://icons8.com/)

## Requirements

- PHP 8.5.12 or newer with the `curl`, `iconv`, `mbstring` and `openssl` extensions
- Composer 2.11 or newer (needs the `phar` extension and either the `zip` extension or `unzip`)
- Optional: [GeoLite2 City database](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data)

## Installation

1. Download Composer and verify its SHA-256 checksum (see below).
2. Install the libraries pinned in `composer.lock`: `php composer.phar install --no-dev`.
   Do not use `composer update` for deployments: it ignores the lock file and pulls whatever is newest.
3. Download the [GeoLite2 City database](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data),
   edit config.php and change the path to the GeoLite2-City.mmdb file respectively.
4. Edit manifest.json and change `start_url` for PWA.
5. Configure your web server so that only `index.php` and the static assets are reachable,
   see [.nginx/nginx.conf.example](.nginx/nginx.conf.example).

#### Manual

```bash
git clone https://github.com/c0m4r/ip-info-page.git
cd ip-info-page
curl -fsSLO https://getcomposer.org/download/2.11.1/composer.phar
echo "6f1b891e1d4927d62740276c540fb490e47689e19e102c49e8da849794882f8c  composer.phar" | sha256sum -c || rm -f composer.phar
php composer.phar install --no-dev --no-interaction
```

The checksum comes from [getcomposer.org/download](https://getcomposer.org/download/). If it does not match,
the file is deleted and the install step fails instead of running an unverified binary.

#### Docker

The image is built from the official `php:8.5.12-fpm-alpine3.24` image pinned by digest. Dependencies are installed
from `composer.lock` at build time, never at container start, and nginx and PHP-FPM both run as the unprivileged
`www-data` user on port 8080.

Build and run it with Docker Compose (read-only root filesystem, all capabilities dropped):

```bash
git clone https://github.com/c0m4r/ip-info-page.git
cd ip-info-page
docker compose up -d --build
```

The page is served at http://127.0.0.1:8080. For GeoIP, put `GeoLite2-City.mmdb` next to `docker-compose.yml`
and uncomment the `volumes` section. For IPv6 add the override file:

```bash
docker compose -f docker-compose.yml -f .docker/docker-compose.ipv6.yml up -d --build
```

Or with plain Docker:

```bash
docker build -t ip-info-page .
docker run --name ipp -d -p 127.0.0.1:8080:8080 --read-only --tmpfs /tmp \
  --cap-drop ALL --security-opt no-new-privileges ip-info-page
```

With GeoIP module:

```bash
docker run --name ipp -d -p 127.0.0.1:8080:8080 --read-only --tmpfs /tmp \
  --cap-drop ALL --security-opt no-new-privileges \
  -v ./GeoLite2-City.mmdb:/var/www/html/GeoLite2-City.mmdb:ro ip-info-page
```

Prebuilt images are published on [Docker Hub](https://hub.docker.com/r/c0m4r/ip-info-page). If you use one,
reference it by digest (`c0m4r/ip-info-page@sha256:...`), not by a tag that can be moved.

## Supply-chain security

- Dependencies are only ever installed from `composer.lock`. Composer plugins are disabled (`allow-plugins: false`).
- Composer's [dependency policy](https://getcomposer.org/doc/06-config.md#policy) blocks versions with security
  advisories, versions flagged as malware and abandoned packages. It also withholds any release younger than 7 days
  (cooldown), so a hijacked release has time to be caught first. For an urgent security fix use
  `composer update --no-cooldown vendor/package`.
- Docker images are pinned by tag and digest, GitHub Actions by full commit SHA, and Composer by version and checksum.
- Dependabot keeps those pins current, with the same 7-day cooldown for regular version updates.
- CI runs `composer validate`, `composer audit`, PHP lint, [PHPStan](https://phpstan.org/) at max level with strict
  rules, Dockerfile build checks, a container smoke test, [zizmor](https://docs.zizmor.sh/) for the workflows,
  and GitHub's dependency review for pull requests.

## Development

```bash
php composer.phar install
php composer.phar lint
php composer.phar analyse
```

To update `composer.lock`, run Composer in the official image (PHP 8.5.12, same extensions as production),
so the lock does not depend on what happens to be installed on your machine:

```bash
docker run --rm -it -u "$(id -u):$(id -g)" -v "$PWD:/app" \
  composer:2.11.1@sha256:8de2184c6de224f4689e8f80ed5f5edaf0030b6b8b7839a22a2bcb6722ac87d0 update
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

## Funding

If you found this software helpful, please consider [making donation](https://en.wosp.org.pl/fundacja/jak-wspierac-wosp/wesprzyj-online) to a charity on my behalf. Thank you.
