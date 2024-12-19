<?php

namespace App\Filament\Pages\PageWidgets;

use Filament\Widgets\Widget;

class ActionsList extends Widget
{
    protected static string $view = 'filament.widgets.actions-list';

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;
}
