<?php

declare(strict_types=1);

namespace Capell\WidgetLocationMap\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class LocationMapRenderData extends Data
{
    public const array HEADING_LEVELS = ['h2', 'h3', 'h4', 'h5', 'h6'];

    private const float MINIMUM_SPAN = 0.02;

    /** @param DataCollection<int, LocationMarkerData> $markers */
    public function __construct(
        public string $title,
        #[DataCollectionOf(LocationMarkerData::class)]
        public DataCollection $markers,
        public string $headingLevel = 'h2',
    ) {}

    public function headingTag(): string
    {
        return in_array($this->headingLevel, self::HEADING_LEVELS, true) ? $this->headingLevel : 'h2';
    }

    /**
     * The embed window that contains every marker with a margin, never smaller than one neighbourhood.
     *
     * @return array{west: float, south: float, east: float, north: float}
     */
    public function bounds(): array
    {
        $latitudes = $this->markers->toCollection()->map(static fn (LocationMarkerData $marker): float => $marker->latitude);
        $longitudes = $this->markers->toCollection()->map(static fn (LocationMarkerData $marker): float => $marker->longitude);
        $south = (float) $latitudes->min();
        $north = (float) $latitudes->max();
        $west = (float) $longitudes->min();
        $east = (float) $longitudes->max();
        $latitudePadding = max(self::MINIMUM_SPAN, ($north - $south) * 0.15);
        $longitudePadding = max(self::MINIMUM_SPAN, ($east - $west) * 0.15);

        return [
            'west' => max(-180.0, round($west - $longitudePadding, 6)),
            'south' => max(-90.0, round($south - $latitudePadding, 6)),
            'east' => min(180.0, round($east + $longitudePadding, 6)),
            'north' => min(90.0, round($north + $latitudePadding, 6)),
        ];
    }

    /** @return array{latitude: float, longitude: float} */
    public function centre(): array
    {
        $bounds = $this->bounds();

        return [
            'latitude' => round(($bounds['south'] + $bounds['north']) / 2, 6),
            'longitude' => round(($bounds['west'] + $bounds['east']) / 2, 6),
        ];
    }

    /** The embed shows one marker, so it is only requested when there is one place to mark. */
    public function singleMarker(): ?LocationMarkerData
    {
        return $this->markers->count() === 1 ? $this->markers->first() : null;
    }
}
