<?php

declare(strict_types=1);

namespace Capell\WidgetLocationMap\Filament;

use Capell\Admin\Contracts\Widgets\FilamentWidget;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

final class LocationMapWidget implements FilamentWidget
{
    public static function getWidgetName(): string
    {
        return 'capell-app.location-map';
    }

    public static function make(): Block
    {
        return Block::make(self::getWidgetName())->label(__('capell-widget-location-map::widget.label'))->schema([TextInput::make('title')->required()->maxLength(120), Repeater::make('markers')->minItems(1)->maxItems(20)->reorderable()->schema([TextInput::make('label')->required()->maxLength(100), TextInput::make('latitude')->numeric()->minValue(-90)->maxValue(90)->required(), TextInput::make('longitude')->numeric()->minValue(-180)->maxValue(180)->required(), TextInput::make('address')->maxLength(300)])]);
    }
}
