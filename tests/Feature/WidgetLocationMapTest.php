<?php

declare(strict_types=1);
use Capell\WidgetLocationMap\Data\LocationMapInputData;
use Capell\WidgetLocationMap\Data\LocationMarkerData;
use Capell\WidgetLocationMap\Health\WidgetLocationMapHealthCheck;
use Capell\WidgetLocationMap\Providers\WidgetLocationMapServiceProvider;
use Illuminate\Validation\ValidationException;

beforeEach(function (): void {
    $this->app->register(WidgetLocationMapServiceProvider::class);
});
it('builds an ordinary map link and healthy surfaces', function (): void {
    expect(LocationMarkerData::from(['label' => 'Office', 'latitude' => 52.4862, 'longitude' => -1.8904])->mapUrl())->toContain('openstreetmap.org')->and(WidgetLocationMapHealthCheck::passed())->toBeTrue();
});
it('rejects invalid coordinates', function (): void {
    expect(fn () => LocationMapInputData::validateAndCreate(['title' => 'Map', 'markers' => [['label' => 'Bad', 'latitude' => 91, 'longitude' => 181]]]))->toThrow(ValidationException::class);
});
