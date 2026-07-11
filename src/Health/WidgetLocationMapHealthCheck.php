<?php

declare(strict_types=1);

namespace Capell\WidgetLocationMap\Health;

use Capell\LayoutBuilder\Health\AbstractWidgetExtensionHealthCheck;
use Capell\WidgetLocationMap\Filament\LocationMapWidget;

final class WidgetLocationMapHealthCheck extends AbstractWidgetExtensionHealthCheck
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^1.0';
    }

    protected static function definitionKey(): string
    {
        return 'capell-app.location-map';
    }

    protected static function filamentWidget(): string
    {
        return LocationMapWidget::class;
    }

    protected static function fallbackView(): string
    {
        return 'capell-widget-location-map::widget';
    }

    protected static function resourceGroups(): array
    {
        return ['capell-app.widget-location-map'];
    }
}
