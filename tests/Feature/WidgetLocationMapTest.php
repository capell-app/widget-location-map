<?php

declare(strict_types=1);
use Capell\Frontend\Actions\AssertPublicHtmlContainsNoAuthoringSurfaceAction;
use Capell\Frontend\Support\Security\PublicHtmlSafetyInspector;
use Capell\WidgetLocationMap\Data\LocationMapInputData;
use Capell\WidgetLocationMap\Data\LocationMapRenderData;
use Capell\WidgetLocationMap\Data\LocationMarkerData;
use Capell\WidgetLocationMap\Health\WidgetLocationMapHealthCheck;
use Capell\WidgetLocationMap\Providers\WidgetLocationMapServiceProvider;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function (): void {
    $this->app->register(WidgetLocationMapServiceProvider::class);
});
it('builds an ordinary map link and healthy surfaces', function (): void {
    expect(LocationMarkerData::from(['label' => 'Office', 'latitude' => 52.4862, 'longitude' => -1.8904])->mapUrl())->toContain('openstreetmap.org')->and(WidgetLocationMapHealthCheck::passed())->toBeTrue();
});
it('rejects invalid coordinates', function (): void {
    expect(fn (): LocationMapInputData => LocationMapInputData::validateAndCreate(['title' => 'Map', 'markers' => [['label' => 'Bad', 'latitude' => 91, 'longitude' => 181]]]))->toThrow(ValidationException::class);
});

it('renders public location-map markup accepted by the public HTML safety action', function (): void {
    $renderData = LocationMapRenderData::from(['title' => 'Office', 'markers' => [['label' => 'Office', 'latitude' => 52.4862, 'longitude' => -1.8904]]]);
    $html = view('capell-widget-location-map::widget', ['renderData' => $renderData])->render();

    expect($html)->toContain('data-capell-widget-location-map')
        ->not->toContain('data-capell-location-map');

    AssertPublicHtmlContainsNoAuthoringSurfaceAction::run(new Response(
        '<!doctype html><html><body>' . $html . '</body></html>',
        Response::HTTP_OK,
        ['Content-Type' => 'text/html'],
    ));

    expect(resolve(PublicHtmlSafetyInspector::class)->containsAuthoringSurface(
        '<section data-capell-location-map>Widget</section>',
    ))->toBeTrue();
});
