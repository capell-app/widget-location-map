# Location Map Widget reference

<!-- prettier-ignore-start -->

Developer reference for `capell-app/widget-location-map`. Start with the [README](../README.md) for what the extension does and how to install it.

## Package Details

Location Map Widget is an **Available**, **No schema impact** Capell package in the **Capell Essentials** product group. It ships as `capell-app/widget-location-map` and extends these surfaces: admin, frontend.

- Status: Available
- Tier: free
- Bundle: foundation
- Composer package: `capell-app/widget-location-map`
- Namespace: `Capell\WidgetLocationMap`
- Theme key: not applicable

## Technical Shape

### Service providers

- `Capell\WidgetLocationMap\Providers\WidgetLocationMapServiceProvider`

### Filament classes

- `LocationMapWidget`

### Data objects

- `LocationMapInputData`
- `LocationMapRenderData`
- `LocationMarkerData`

### Manifest contributions

- `asset: Capell\WidgetLocationMap\WidgetLocationMapContribution`
- `content-widget: Capell\WidgetLocationMap\WidgetLocationMapContribution`

### Health checks

- `Capell\WidgetLocationMap\Health\WidgetLocationMapHealthCheck`

### Blade views

- `packages/widget-location-map/resources/views/widget.blade.php`

### Cache tags

- `widget-location-map`


## Data Model

This package has no schema impact. It extends Capell through `asset` contributions and `content-widget` contributions instead of declaring package-owned tables.

<!-- prettier-ignore-end -->
