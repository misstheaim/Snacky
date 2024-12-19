<?php

namespace App\Filament\Pages;

use App\Filament\Pages\PageWidgets\ActionsList;
use App\Filament\Resources\Helpers\HelperFunctions;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Actions extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static string $view = 'filament.pages.actions';

    protected static ?string $navigationGroup = 'Administrator';

    public static function canAccess(): bool
    {
        return HelperFunctions::isUser(Auth::user())->isAdmin();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ActionsList::class
        ];
    }
}
