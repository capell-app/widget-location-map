@php /** @var \Capell\WidgetLocationMap\Data\LocationMapRenderData $renderData */ $first = $renderData->markers->first(); @endphp
<section
    class="capell-location-map"
    data-capell-location-map
    data-latitude="{{ $first->latitude }}"
    data-longitude="{{ $first->longitude }}"
>
    <h2>{{ $renderData->title }}</h2>
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
