<?php

namespace App\Filament\Resources\Templates;

use App\Contracts\Filament\Snack\ViewTemplate;
use App\Filament\Resources\Helpers\HelperFunctions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component as LivewireComponent;

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
                    TextInput::make('title_ru')
                        ->disabled(true)
                        ->required()
                        ->label('Title'),
                    Select::make('category_id')
                        ->disabled(true)
                        ->required()
                        ->relationship('category', 'title_ru'),
                    Textarea::make('description_ru')
                        ->disabled(true)
                        ->label('Description')
                        ->rows(5)
                        ->placeholder('Here you can write something about your product'),
                    TextInput::make('link')
                        ->disabled(true)
                        ->url()
                        ->required()
                        ->activeUrl()
                        ->label('Link to the product')
                        ->suffixAction(
                            Action::make('redirect')
                                ->icon('heroicon-m-globe-alt')
                                ->action(function (?string $state, LivewireComponent $livewire) {
                                    $livewire->js("window.open('$state');");
                                })
                        ),
                    TextInput::make('price')
                        ->disabled(true)
                        ->prefix('UZS'),
                    Select::make('status')
                        ->hidden( ! HelperFunctions::isUser(Auth::user())->isManager())
                        ->options(function (?Model $record) {
                            if ($record->receipts->count() !== 0) {
                                return [
                                    'APPROVED' => 'Approved',
                                    'IN_PROCESS' => 'In process'
                                ];
                            } else {
                                return [
                                    'APPROVED' => 'Approved',
                                    'DISAPPROVED' => 'Disapproved',
                                    'IN_PROCESS' => 'In process'
                                ];
                            }
                        })
                        ->selectablePlaceholder(false)
                        ->live()
                        ->disabledOn('update')
                        ->afterStateUpdated(function ($state, ?Model $record) {
                            $record->status = $state;
                            $record->save();
                        }),
                    ])->columnSpan(1),
                    Section::make()->columns(1)->schema([
                    ViewField::make('high_image_link')
                        ->label('Image')
                        ->view('forms.components.view-image')
                    ])->columnSpan(1)
                ])
            );

        return $from;
    }
}