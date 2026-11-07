# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-11-07

### Added

- Composer dependency policy: blocks insecure, malware-flagged and abandoned packages, plus a 7-day cooldown for new releases
- Composer plugins disabled (`allow-plugins: false`)
- CI workflow: composer validate/audit, PHP lint, PHPStan (max level, strict rules), Dockerfile checks, container smoke test, zizmor, dependency review
- Dependabot for Composer, Docker images and GitHub Actions, with a 7-day cooldown
- .dockerignore allowlist, `X-Content-Type-Options: nosniff` header

### Changed

- requires PHP 8.5.12 or newer
- geoip2/geoip2 upgraded to 3.x, twig/twig to 3.30
- config.php now returns an array instead of defining `$config`
- Dockerfile moved to the repository root and rewritten: digest-pinned php:8.5.12-fpm-alpine3.24 base, dependencies installed from composer.lock at build time, nginx and PHP-FPM run as www-data on port 8080 (was 80), PHP-FPM only listens on 127.0.0.1
- docker-compose.yml builds that image and runs it read-only with all capabilities dropped; the IPv6 compose file is now an override
- README: verified Composer download, `composer install` instead of `composer update`, hardened Docker commands
- CSP nonce uses `random_bytes()`, navigator.js uses `textContent` instead of `innerHTML`

### Removed

- PHPMD and Psalm workflows (replaced by the CI workflow)

### Fixed

- user agent was HTML-escaped twice in the page and appeared HTML-escaped in the JSON output
- malformed `<script>` tag when SRI is disabled
- deprecation warning when the request has no User-Agent header

## [1.3.0] - 2024-01-17

### Added

- Dockerfile
- catching AddressNotFoundException

## [1.2.1] - 2024-01-14

### Changed

- path to GeoLite2-City.mmdb database can now be set in config.php

## [1.2.0] - 2023-12-29

### Added

- PWA and HTML5 optimization
- added manifest.json for PWA compatibility
- added additional icons

### Changed

- CSP header now has base-uri 'self'
- minified nginx configuration

## [1.1.1] - 2023-12-25

### Added

- added docker example configuration

## [1.1.0] - 2023-12-24

### Added

- added csp header with nonce
- new file: config.php with csp and sri enable/disable options

### Changed

- moved inline svg style to css/style.css
- updated nginx example config

## [1.0.2] - 2023-12-24

### Added

- added github creator meta tags
- added missing composer.lock

### Changed

- changed bootstrap grid options
- changed ip container size

## [1.0.1] - 2023-12-23

### Added

- added favicon

### Changed

- stripping ua longer than 512 chars

## [1.0.0] - 2023-12-22

### Added

- First release
