<?php

declare(strict_types=1);

namespace Capell\WidgetLocationMap\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class LocationMapRenderData extends Data
{
    /** @param DataCollection<int, LocationMarkerData> $markers */
    public function __construct(public string $title, #[DataCollectionOf(LocationMarkerData::class)] public DataCollection $markers) {}
}
