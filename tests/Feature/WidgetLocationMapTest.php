<?php

declare(strict_types=1);

use Capell\Core\Contracts\Privacy\ConsentDecisionProvider;
use Capell\Core\Enums\ConsentCategory;
use Capell\Core\Facades\CapellCore;
use Capell\Core\Support\Privacy\ConsentSettings;
use Capell\Frontend\Actions\AssertPublicHtmlContainsNoAuthoringSurfaceAction;
use Capell\Frontend\Support\Security\PublicHtmlSafetyInspector;
use Capell\Tests\Support\RenderedHtml;
use Capell\WidgetLocationMap\Data\LocationMapInputData;
use Capell\WidgetLocationMap\Data\LocationMapRenderData;
use Capell\WidgetLocationMap\Data\LocationMarkerData;
use Capell\WidgetLocationMap\Health\WidgetLocationMapHealthCheck;
use Capell\WidgetLocationMap\Providers\WidgetLocationMapServiceProvider;
use Illuminate\Http\Request;
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

it('fits the embed window around every marker and centres on all of them', function (): void {
    $renderData = LocationMapRenderData::from(['title' => 'Offices', 'markers' => [
        ['label' => 'Birmingham', 'latitude' => 52.4862, 'longitude' => -1.8904],
        ['label' => 'Edinburgh', 'latitude' => 55.9533, 'longitude' => -3.1883],
        ['label' => 'Cardiff', 'latitude' => 51.4816, 'longitude' => -3.1791],
    ]]);
    $bounds = $renderData->bounds();

    expect($bounds['west'])->toBeLessThan(-3.1883)
        ->and($bounds['east'])->toBeGreaterThan(-1.8904)
        ->and($bounds['south'])->toBeLessThan(51.4816)
        ->and($bounds['north'])->toBeGreaterThan(55.9533);

    $section = (new RenderedHtml(view('capell-widget-location-map::widget', ['renderData' => $renderData])->render()))->element('//section[@data-capell-widget-location-map]');
    [$west, $south, $east, $north] = array_map('floatval', explode(',', $section->getAttribute('data-bbox')));

    expect($west)->toBe($bounds['west'])->and($north)->toBe($bounds['north'])
        ->and((float) $section->getAttribute('data-latitude'))->toBe(round(($south + $north) / 2, 6))
        ->and((float) $section->getAttribute('data-longitude'))->toBe(round(($west + $east) / 2, 6))
        ->and($section->hasAttribute('data-marker'))->toBeFalse();
});

it('marks a single place and keeps a neighbourhood sized window around it', function (): void {
    $renderData = LocationMapRenderData::from(['title' => 'Office', 'markers' => [['label' => 'Office', 'latitude' => 52.4862, 'longitude' => -1.8904]]]);
    $section = (new RenderedHtml(view('capell-widget-location-map::widget', ['renderData' => $renderData])->render()))->element('//section[@data-capell-widget-location-map]');

    expect($section->getAttribute('data-marker'))->toBe('52.4862,-1.8904')
        ->and($section->getAttribute('data-bbox'))->toBe('-1.9104,52.4662,-1.8704,52.5062');
});

it('renders the heading at an allowlisted level and falls back to h2', function (string $level, string $tag): void {
    $renderData = LocationMapRenderData::from(['title' => 'Office', 'headingLevel' => $level, 'markers' => [['label' => 'Office', 'latitude' => 52.4862, 'longitude' => -1.8904]]]);
    $html = new RenderedHtml(view('capell-widget-location-map::widget', ['renderData' => $renderData])->render());

    expect($html->element('//section/*[self::h1 or self::h2 or self::h3 or self::h4 or self::h5 or self::h6][1]')->nodeName)->toBe($tag);
})->with([['h3', 'h3'], ['h6', 'h6'], ['h1', 'h2'], ['script', 'h2']]);

it('validates the heading level on input', function (): void {
    expect(fn (): LocationMapInputData => LocationMapInputData::validateAndCreate(['title' => 'Map', 'headingLevel' => 'h1', 'markers' => [['label' => 'A', 'latitude' => 1, 'longitude' => 1]]]))->toThrow(ValidationException::class);
});

it('declares the openstreetmap purpose as functional so the default consent provider can decide it', function (): void {
    config()->set('capell.privacy.consent.purposes', []);
    $provider = resolve(ConsentDecisionProvider::class);

    expect($provider->purposes())->toHaveKey('openstreetmap', 'functional')
        ->and($provider->decide('openstreetmap', Request::create('/')))->toBe(resolve(ConsentSettings::class)->defaultDecision(ConsentCategory::Functional));
});
