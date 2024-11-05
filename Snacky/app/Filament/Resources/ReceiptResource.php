<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReceiptResource\Pages;
use App\Filament\Resources\ReceiptResource\RelationManagers\SnacksRelationManager;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ReceiptResource extends Resource
{
    protected static ?string $model = Receipt::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    public static function form(Form $form): Form
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
                    ->hidden(fn (?Model $record) => is_null($record))
                    ->schema([
                        TextInput::make('total_price')
                            ->label('')
                            ->disabled()
                            ->afterStateHydrated(function (?Model $record, TextInput $component) {
                                if (is_null($record)) return;
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
                    ->footerActions([
                        Action::make('Update total price')
                            ->action(function (?Model $record, Set $set) {
                                $set('total_price', $record->total_price);
                            })
                    ])
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('total_price')
                    ->numeric()
                    ->prefix('UZS '),
                TextColumn::make('description')
                    ->wrap()
                    ->limit(20),
                TextColumn::make('snacks_count')
                    ->alignCenter()
                    ->counts('snacks')
                    ->numeric(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('Download pdf')
                        ->label('Downlod PDF')
                        ->icon('heroicon-o-arrow-down-on-square')
                        ->action(function(?Model $record) {
                            return redirect()->route('pdf', $record->id);
                        }),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SnacksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReceipts::route('/'),
            'create' => Pages\CreateReceipt::route('/create'),
            'edit' => Pages\EditReceipt::route('/{record}/edit'),
            'view' => Pages\ViewReceipt::route('/{record}'),
        ];
    }
}
