<?php

declare(strict_types=1);

namespace Capell\WidgetLocationMap\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

final class LocationMapInputData extends Data
{
    /** @param DataCollection<int, LocationMarkerData> $markers */
    public function __construct(public string $title, #[DataCollectionOf(LocationMarkerData::class)] public DataCollection $markers) {}

    /** @return array<string, list<string>> */
    public static function rules(): array
    {
        return ['title' => ['required', 'string', 'max:120'], 'markers' => ['required', 'array', 'min:1', 'max:20']];
    }
}
