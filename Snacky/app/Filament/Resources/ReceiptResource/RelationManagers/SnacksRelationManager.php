<?php

namespace App\Filament\Resources\ReceiptResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SnacksRelationManager extends RelationManager
{
    protected static string $relationship = 'snacks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('uzum_product_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title_ru')
            ->columns([
                TextColumn::make('title_ru')
                    ->label('Title')
                    ->width('30%')
                    ->wrap()
                    ->searchable(),
                ImageColumn::make('low_image_link')
                    ->label('Image')
                    ->alignCenter(),
                TextColumn::make('category.title_ru'),
                TextColumn::make('price')
                    ->numeric()
                    ->prefix('UZS '),
                TextColumn::make('item_count')
                    ->width('10%')
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
