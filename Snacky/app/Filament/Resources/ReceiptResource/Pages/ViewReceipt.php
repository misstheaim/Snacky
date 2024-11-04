<?php

namespace App\Filament\Resources\ReceiptResource\Pages;

use App\Filament\Resources\ReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Model;

class ViewReceipt extends ViewRecord
{
    protected static string $resource = ReceiptResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('title')
                            ->required(),
                        Textarea::make('description')
                    ])
                    ->columnSpan(1)
                    ->columns(1),
                Section::make('Total Price: ')
                    ->schema([
                        TextInput::make('total_price')
                            ->label('')
                            ->disabled()
                            ->afterStateHydrated(function (?Model $record, TextInput $component) {
                                $total_price = 0;
                                foreach ($record->snacks as $snack) {
                                    $total_price += $snack->price * $snack->pivot->item_count;
                                }
                                $record->total_price = $total_price;
                                $record->save();
                                $component->state($total_price);
                            })
                            ->dehydrateStateUsing(fn (?Model $record) => $record->total_price)
                    ])
                    ->extraAttributes(['style' => 'height: 100%;
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: center;'])
                    ->columnSpan(1)
            ])
            ->columns(2);
    }
}
