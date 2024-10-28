<?php

namespace App\Filament\Resources\Templates;

use App\Contracts\Filament\Snack\ViewTemplate;
use App\Filament\Resources\Helpers\HelperFunctions;
use Filament\Forms\Components\Actions\Action;
use Livewire\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SnackViewTemplate implements ViewTemplate
{
    public $isAdmin;
    public $isManager;

    public function __construct()
    {
        $this->isAdmin = HelperFunctions::isUser(Auth::user())->isAdmin();
        $this->isManager = HelperFunctions::isUser(Auth::user())->isManager();
    }


    public function __invoke()
    {
        $from = array(
            Section::make()
                ->columns(2)
                ->schema([
                    Section::make()->columns(1)->schema([
                    TextInput::make('title_ru')->required()
                        ->label('Title'),
                    Select::make('category_id')
                        ->required()
                        ->relationship('category', 'title_ru'),
                    Textarea::make('description_ru')
                        ->label('Description')
                        ->rows(5)
                        ->placeholder('Here you can write something about your product'),
                    TextInput::make('link')
                        ->url()
                        ->required()
                        ->activeUrl()
                        ->label('Link to the product')
                        ->suffixAction(
                            Action::make('redirect')
                                ->icon('heroicon-m-globe-alt')
                                ->action(function (?string $state, Component $livewire) {
                                    $livewire->js("window.open('$state');");
                                })
                        ),
                    TextInput::make('price')
                        ->prefix('UZS'),
                    ])->columnSpan(1),
                    Section::make()->columns(1)->schema([
                    ViewField::make('high_image_link')
                        ->label('Image')
                        ->view('forms.components.view-image')
                    ])->columnSpan(1)
                ])
            );

        if ($this->isManager || $this->isAdmin) {
            $table[] = SelectColumn::make('status')
                ->options([
                    'APPROVED' => 'Approved',
                    'DISAPPROVED' => 'Disapproved',
                    'IN_PROCESS' => 'In process'
                ])
                ->sortable();
        }


        return $from;
    }
}