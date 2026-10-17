<?php

declare(strict_types=1);

namespace Capell\WidgetLocationMap\Providers;

use Capell\Core\Enums\PresentationLoadingStrategy;
use Capell\LayoutBuilder\Data\WidgetExtensions\WidgetExtensionCapabilitiesData;
use Capell\LayoutBuilder\Data\WidgetExtensions\WidgetExtensionDefinitionData;
use Capell\LayoutBuilder\Enums\WidgetPresentationCapability;
use Capell\LayoutBuilder\Providers\AbstractFoundationWidgetServiceProvider;
use Capell\WidgetLocationMap\Data\LocationMapInputData;
use Capell\WidgetLocationMap\Data\LocationMapRenderData;
use Capell\WidgetLocationMap\Filament\LocationMapWidget;
use Override;

final class WidgetLocationMapServiceProvider extends AbstractFoundationWidgetServiceProvider
{
    public static string $name = 'capell-widget-location-map';

    public static string $packageName = 'capell-app/widget-location-map';

    #[Override]
    public function packageBooted(): void {}

    #[Override]
    protected static function resourceGroup(): string
    {
        return 'capell-app.widget-location-map';
    }

    #[Override]
    protected static function scriptLoadingStrategy(): PresentationLoadingStrategy
    {
        return PresentationLoadingStrategy::Interaction;
    }

    #[Override]
    protected function bootInstalledRuntime(): void
    {
        $this->registerWidget();
    }

    #[Override]
    protected function definition(): WidgetExtensionDefinitionData
    {
        return new WidgetExtensionDefinitionData(key: 'capell-app.location-map', packageName: self::$packageName, stateVersion: 1, filamentWidget: LocationMapWidget::class, inputData: LocationMapInputData::class, renderData: LocationMapRenderData::class, fallbackView: 'capell-widget-location-map::widget', components: ['blade' => 'capell::widgets.capell-app.location-map'], resourceGroups: [self::resourceGroup()], defaultResourceLoadingStrategy: PresentationLoadingStrategy::Interaction, capabilities: new WidgetExtensionCapabilitiesData(requiresInstanceIdentity: true, presentationCapabilities: [WidgetPresentationCapability::Width, WidgetPresentationCapability::Alignment, WidgetPresentationCapability::LoadingStrategy]));
    }
}
