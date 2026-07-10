<?php

declare(strict_types=1);

namespace Capell\WidgetLocationMap\Data;

use Spatie\LaravelData\Data;

final class LocationMarkerData extends Data
{
    public function __construct(public string $label, public float $latitude, public float $longitude, public ?string $address = null) {}

    /** @return array<string, list<string>> */
    public static function rules(): array
    {
        return ['label' => ['required', 'string', 'max:100'], 'latitude' => ['required', 'numeric', 'between:-90,90'], 'longitude' => ['required', 'numeric', 'between:-180,180'], 'address' => ['nullable', 'string', 'max:300']];
    }

    public function mapUrl(): string
    {
        return sprintf('https://www.openstreetmap.org/?mlat=%F&mlon=%F#map=15/%F/%F', $this->latitude, $this->longitude, $this->latitude, $this->longitude);
    }
}
