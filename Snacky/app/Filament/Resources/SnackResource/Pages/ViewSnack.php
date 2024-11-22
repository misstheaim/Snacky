<?php

namespace App\Filament\Resources\SnackResource\Pages;

use App\Contracts\Filament\Snack\ViewTemplate;
use App\Filament\Resources\SnackResource;
use Filament\Forms\Form;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\App;
use Parallax\FilamentComments\Actions\CommentsAction;

class ViewSnack extends ViewRecord
{
    protected static string $resource = SnackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CommentsAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema(App::make(ViewTemplate::class)())
            ->disabled(false);
    }

}
