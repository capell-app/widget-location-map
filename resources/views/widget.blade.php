@php /** @var \Capell\WidgetLocationMap\Data\LocationMapRenderData $renderData */ $bounds = $renderData->bounds(); $centre = $renderData->centre(); $single = $renderData->singleMarker(); $heading = $renderData->headingTag(); @endphp
<section
    class="capell-location-map"
    data-capell-widget-location-map
    data-frame-title="{{ __('capell-widget-location-map::widget.frame_title') }}"
    data-latitude="{{ $centre['latitude'] }}"
    data-bbox="{{ $bounds['west'] }},{{ $bounds['south'] }},{{ $bounds['east'] }},{{ $bounds['north'] }}"
    @if ($single) data-marker="{{ $single->latitude }},{{ $single->longitude }}" @endif
    data-denied-label="{{ __('capell-widget-location-map::widget.denied') }}"
    data-longitude="{{ $centre['longitude'] }}"
>
    <{{ $heading }}>{{ $renderData->title }}</{{ $heading }}>
    <div class="capell-location-map__fallback">
        <ol>
            @foreach ($renderData->markers as $marker)
                <li>
                    <strong>{{ $marker->label }}</strong>
                    @if ($marker->address)
                        <address>{{ $marker->address }}</address>
                    @endif
                    <a
                        href="{{ $marker->mapUrl() }}"
                        rel="noopener noreferrer"
                        >{{ __('capell-widget-location-map::widget.view_on_openstreetmap') }}</a
                    >
                </li>
            @endforeach
        </ol>
        <button
            class="capell-button capell-button--default capell-button--md"
            type="button"
            data-map-activate
        >
            {{ __('capell-widget-location-map::widget.activate') }}
        </button>
        <p role="status" aria-live="polite" data-map-status></p>
    </div>
    <div data-map-frame></div>
    <small>{{ __('capell-widget-location-map::widget.attribution') }}</small>
</section>
