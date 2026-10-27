<?php

declare(strict_types=1);

use Capell\Core\Facades\CapellCore;
use Capell\Frontend\Actions\AssertPublicHtmlContainsNoAuthoringSurfaceAction;
use Capell\Frontend\Support\Security\PublicHtmlSafetyInspector;
use Capell\Tests\Support\RenderedHtml;
use Capell\WidgetLocationMap\Data\LocationMapInputData;
use Capell\WidgetLocationMap\Data\LocationMapRenderData;
use Capell\WidgetLocationMap\Data\LocationMarkerData;
use Capell\WidgetLocationMap\Health\WidgetLocationMapHealthCheck;
use Capell\WidgetLocationMap\Providers\WidgetLocationMapServiceProvider;
use Illuminate\Translation\Translator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Process;

beforeEach(function (): void {
    CapellCore::forcePackageInstalled('capell-app/widget-location-map');
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

it('renders the map activation control with the shared button styling', function (): void {
    $renderData = LocationMapRenderData::from(['title' => 'Office', 'markers' => [['label' => 'Office', 'latitude' => 52.4862, 'longitude' => -1.8904]]]);
    $html = new RenderedHtml(view('capell-widget-location-map::widget', ['renderData' => $renderData])->render());
    $button = $html->element('//button[@type="button"][@data-map-activate]');
    $classes = preg_split('/\s+/', trim($button->getAttribute('class'))) ?: [];

    expect($classes)->toContain('capell-button', 'capell-button--default', 'capell-button--md')
        ->and(trim($button->textContent))->toBe(__('capell-widget-location-map::widget.activate'));
});

it('uses translated denial and iframe names when activating the public map', function (): void {
    resolve(Translator::class)->addLines(['widget.denied' => 'Carte refusée <test>', 'widget.frame_title' => 'Carte interactive'], 'fr', 'capell-widget-location-map');
    app()->setLocale('fr');

    $renderData = LocationMapRenderData::from(['title' => 'Bureau', 'markers' => [['label' => 'Bureau', 'latitude' => 52.4862, 'longitude' => -1.8904]]]);
    $html = view('capell-widget-location-map::widget', ['renderData' => $renderData])->render();
    $process = new Process(['node', __DIR__ . '/../Fixtures/localised-map.mjs', dirname(__DIR__, 2) . '/resources/dist/widget.js']);
    $document = new RenderedHtml($html);
    $attributes = $document->element('//section[@data-capell-widget-location-map]')->attributes;
    $dataset = [];
    foreach ($attributes as $attribute) {
        if (str_starts_with($attribute->name, 'data-')) {
            $name = lcfirst(str_replace('-', '', ucwords(substr($attribute->name, 5), '-')));
            $dataset[$name] = $attribute->value;
        }
    }

    $process->setInput(json_encode($dataset, JSON_THROW_ON_ERROR));
    $process->run();

    expect($process->getExitCode())->toBe(0, $process->getErrorOutput() . $process->getOutput());
});
