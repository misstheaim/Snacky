<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title_ru')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup(Group::make('parent.title_ru')
                ->orderQueryUsing(fn (Builder $query) => $query->orderBy('parent_id', 'asc'))
                ->groupQueryUsing(fn (Builder $query) => $query->groupBy('parent_id'))
                ->collapsible()
                ->titlePrefixedWithLabel(false)
            )
            ->defaultPaginationPageOption('all')
            ->columns([
                TextColumn::make('title_ru')
                    ->label('Title'),
                TextColumn::make('snacks_count')
                    ->counts('snacks')
                    ->label('Snacks count')
                    ->alignCenter()
                    ->state(function (Model $record) {
                        return $record->snacks_count != 0 ?? '' ;
                    }),
                TextColumn::make('parent.title_ru'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            //'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}