# Infrangible Google Maps

Google Maps in Magento: CMS widgets with maps, markers and address suggestions, and address lookups with the Geocoding
API. The script of Google Maps is only loaded on pages with a widget of the module.

## Widgets

| Widget                     | Content                                                                                       |
|----------------------------|-----------------------------------------------------------------------------------------------|
| Google Maps Simple Marker  | a map with one marker (latitude, longitude, title, info window with formatted text)          |
| Google Maps Json Marker    | a map with the markers of a JSON list, centered on the markers                               |
| Google Maps Autocomplete   | an input with address suggestions of Google Maps (Places), optionally restricted to countries |

The maps have an ID, a size (pixels, or a CSS length such as `100%`, `50vh` or `20rem`), a zoom, the controls of Google
Maps (zoom, map type, Street View, rotation, scale, fullscreen) and the visibility of administrative areas, landscapes,
points of interest, roads, transit and water.

Markers of the JSON list: `lat`, `lng`, `title` and `info_window` (HTML), for example
`[{"lat":50.9553,"lng":11.6224,"title":"Shop","info_window":"<h2>Shop</h2>"}]`.

The page triggers the event `google-maps-api` on `document` when the API is loaded; scripts of other modules (for example
Infrangible Retail Store) wait for it.

## Geocoding

`Infrangible\GoogleMaps\Model\GeocodingApi::searchLocation($address, $components, $language)` returns the first result of
the Geocoding API (for example `geometry.location`), or an empty array; results are cached in the table
`google_maps_location`. On the console:

```bash
bin/magento google:maps:geocoding --address "Brückenstr. 16, 07743 Jena" --components country:DE --language de
```

## Configuration

Stores > Configuration > Infrangible Solutions > Google Maps:

- Settings: Enabled, API Key (Maps JavaScript API and Places API)
- Geocoding API: Use Maps key, or an own API Key

## Requirements

- Magento Open Source or Adobe Commerce 2.4.6 or newer
- PHP 8.1 or newer
- An API key of the Google Maps Platform

For the Hyvä theme: [Infrangible Google Maps Hyvä](https://www.infrangible.dev/modules/google-maps-hyva).

## Installation

```bash
composer require infrangible/m2-google-maps
bin/magento module:enable Infrangible_GoogleMaps
bin/magento setup:upgrade
```

In production mode, also run `bin/magento setup:di:compile` and `bin/magento setup:static-content:deploy`.

## License

Infrangible Google Maps is licensed under the MIT License - see the LICENSE file for details.
