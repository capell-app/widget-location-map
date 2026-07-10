<?php

declare(strict_types=1);

namespace Capell\WidgetLocationMap;

use Capell\Core\Contracts\Extensions\RegistersExtensionContentWidget;

final class WidgetLocationMapContribution implements RegistersExtensionContentWidget
{
    public static function compatibleCapellApiVersion(): string
    {
        return '^4.1';
    }
}
