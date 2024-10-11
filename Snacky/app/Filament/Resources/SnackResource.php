<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SnackResource\Pages;
use App\Filament\Resources\SnackResource\RelationManagers;
use App\Filament\Resources\Templates\HelperFunctions;
use App\Filament\Resources\Templates\SnackTemplates;
use App\Models\Snack;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SnackResource extends Resource
{
    protected static ?string $model = Snack::class;

    protected static ?string $navigationIcon = 'heroicon-o-percent-badge';

    protected static ?string $slug = 'snacks';

    protected static ?string $label = 'Snack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(SnackTemplates::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(SnackTemplates::getTable())
            ->filters([
                //
            ])
            ->query(HelperFunctions::isUser(Auth::user())->isDev() ? Snack::query()->where('status', 'APPROVED') : Snack::query())
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSnacks::route('/'),
            'create' => Pages\CreateSnack::route('/create'),
            'edit' => Pages\EditSnack::route('/{record}/edit'),
        ];
    }
}
