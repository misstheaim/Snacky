<?php

namespace App\Filament\Pages;

use App\Filament\Pages\PageWidgets\CommentedSnacksTable;
use Filament\Pages\Page;

class CommentedSnacks extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.commented-snacks';

    protected static bool $shouldRegisterNavigation = false;

    public array $snacks;

    protected function getHeaderWidgets() :array
    {
        $this->snacks = request()->query('snacks') ?? array();
        return [
            CommentedSnacksTable::make([
                'filter' => $this->snacks
            ])
        ];
    }
}
