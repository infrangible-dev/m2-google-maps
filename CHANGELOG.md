# Changelog

## 2.0.2

### Changed

- Supports PHP 8.1 again, like Magento 2.4.6: the Adobe Commerce Marketplace requires modules to allow every PHP
  version of the Magento versions they support.

## 2.0.1

### Fixed

- The upgrade from version 1.x failed with "Duplicate key name 'GOOGLE_MAPS_LOCATION_KEY'": version 1.x created the
  index of the table `google_maps_location` as a plain index instead of a unique one, which the upgrade now replaces.

### Changed

- Checks of the PhpStorm conventions with PHPStan: @throws of the method itself, catches of exceptions that are never
  thrown and arguments with the default value of the parameter.

## 2.0.0

### Breaking changes

- Requires Magento 2.4.6 or newer and PHP 8.2 or newer. Older versions of Magento and PHP 7.1 to 8.1 are no longer
  supported; use version 1.x for these.
- Requires `infrangible/m2-core` 5.0, `infrangible/m2-foundation` 3.0, `fewedev/base` 2.4 and the Magento modules it
  uses; before, the module used m2-core and fewedev/base without requiring them.
- All classes have native types, and the constructors use constructor property promotion. `SimpleMarker` returns latitude
  and longitude as numbers.
- Width and height of maps accept numbers (pixels) and CSS lengths (`px`, `%`, `em`, `rem`, `vh`, `vw`); other values
  are ignored.
- The table `google_maps_location` is created by declarative schema; its key is the SHA-256 of the request, so results
  of version 1 are requested again once.

### Security

- The autocomplete input escapes the value of the request; before, a crafted link could run script in the page.
- The templates escape the values of the widgets (IDs, titles, info windows, sizes, API key); a title or info window
  with quotes no longer breaks the map.
- The markers of the JSON list are written as JSON that cannot end the script; before, a quote ended the string of
  `JSON.parse()`.

### Fixed

- The Geocoding API works without the library `PestJSON`, which the module did not require; it uses Magento's HTTP
  client.
- The option `--components` of `google:maps:geocoding` passes the filters as given; before, `country:DE` became the
  filters `0:country` and `1:DE`. The command prints the result as JSON and fails if nothing was found.
- A JSON list without coordinates no longer fails with a division by zero.
- Titles of markers are plain text; before, quotes were shown as HTML entities.
- The texts of the widgets and the configuration are translatable.

### Added

- Translations into 24 locales.
- README.

### Changed

- The request to the Geocoding API no longer pretends to be a browser.
- The module is loaded after the modules it uses.
- Checks with PHPStan level 9, the Magento coding standard and unit tests on every push.
