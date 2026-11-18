# Location Map Widget

<!-- prettier-ignore-start -->

Your branches appear on the page as a numbered list of up to twenty places. Each has its name, an address if you've given one, and a link that opens OpenStreetMap at that exact spot. The map is OpenStreetMap, not Google Maps.

Under the list sits a "Load interactive map" button. Nothing is fetched from OpenStreetMap until a visitor presses it, and the map that appears frames every place in your list, with a marker only when there is just one.

There's no postcode search. For each place you type in two numbers, its latitude and longitude, which you can copy from a map app.

If your site runs Privacy Centre and a visitor has turned down functional cookies in its cookie banner, pressing the button shows a short note and no map loads. The list and its links work for everyone either way.

- **Price and availability:** Check current availability and price on the [Capell Marketplace listing](https://capell.app/extensions/widget-location-map).
- **Licence:** Check the licence terms on the [Capell Marketplace listing](https://capell.app/extensions/widget-location-map).
- **Requires:** `PHP ^8.4`, `capell-app/admin ^1.0`, `capell-app/core ^1.0.66`, `capell-app/frontend ^1.0`, `capell-app/layout-builder ^1.0`, `spatie/laravel-package-tools ^1.0`.
- **Install:** `composer require capell-app/widget-location-map`.

## Quick Start

1. Install the package: `composer require capell-app/widget-location-map`.
2. Open a page, click **Layout builder**, **Add widgets**, then choose **Location Map**.

## For Developers

The widget validates marker data and keeps third-party map loading behind an interaction and privacy-aware extension boundary.

## Configure

This extension works without configuration. There is nothing to set after install.

## Limits And Pitfalls

- Install the required Capell packages at the versions in `composer.json`: `capell-app/admin ^1.0`, `capell-app/core ^1.0.66`, `capell-app/frontend ^1.0`, `capell-app/layout-builder ^1.0`.
- Keep public Blade and cached HTML free of authoring markers, model IDs, permissions, signed editor URLs, and lazy database queries.
- Custom write integrations must preserve invalidation for `widget-location-map` cache tags.

## Screens And Workflow

![Location Map public output](docs/screenshots/location-map-public-output.png)

![Location Map fields in Layout Builder](docs/screenshots/location-map-layout-builder-editor.png)

- Location Map Widget extension card (marketplace).
- Location Map fields in Layout Builder (admin).
- Location Map public output (frontend).
- Location Map fields in Layout Builder with admin sidebar menu open (admin).

## Install Impact

- Required packages: `capell-app/admin`, `capell-app/core`, `capell-app/frontend`, `capell-app/layout-builder`.
- Admin navigation: no admin page or resource contribution is declared.
- Admin/editor extensions: `content-widget: WidgetLocationMapContribution`.
- Permissions: no package permission declarations or Shield gates detected; host access rules still apply.
- Public routes: none declared.
- Database changes: no package migrations declared.
- Config: no package config files.
- Settings: no package settings declared.
- Queues or schedules: none declared.
- Cache tags: `widget-location-map`.
- Commands: none declared.

## Troubleshooting

| Symptom | Likely cause | Check | Fix |
| --- | --- | --- | --- |
| Package surface is missing after install | Provider or manifest is not loaded | Confirm `capell.json`, package `composer.json`, and provider registration | Reinstall the package, refresh Composer autoload, and clear host caches |
| Public output leaks unexpected state | Render data, cache variation, or authoring boundary has regressed | Check public Blade, cache tags, and public-output safety tests | Move data loading out of Blade and rerun the package public-output tests |

## Upgrading

No manual steps between minor versions.

## Next Steps

- [Package docs](docs/README.md)
- [Overview](docs/overview.md)
- [Troubleshooting](#troubleshooting)
- [Developer reference](docs/reference.md)
- [Marketplace assets](docs/assets/marketplace/)
- [Capell content language plan](../../docs/CONTENT_LANGUAGE_PLAN.md)
- [Capell documentation design system](../../docs/DESIGN_SYSTEM.md)
- [Capell and package ERD notes](../../docs/erd/capell-and-package-erds.md)
- Related packages: [Layout Builder](../layout-builder/README.md), [Privacy Centre](../privacy-center/README.md).

<!-- prettier-ignore-end -->
