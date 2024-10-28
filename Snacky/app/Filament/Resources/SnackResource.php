<?php

namespace App\Filament\Resources;

use App\Contracts\Filament\Snack\TableTemplate;
use App\Contracts\Filament\Snack\FormTemplate;
use App\Contracts\Filament\Snack\ViewTemplate;
use App\Filament\Resources\SnackResource\Pages;
use App\Filament\Resources\Helpers\HelperFunctions;
use App\Models\Snack;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
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
            ->schema(App::make(FormTemplate::class)());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup(
                Group::make('category.title_ru')
                    ->orderQueryUsing(fn (Builder $query, $direction) => $query->join('categories', 'snacks.category_id', 'categories.uzum_category_id')->orderBy('categories.title_ru', $direction))
                    ->groupQueryUsing(fn (Builder $query) => $query->groupBy('category_id'))
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
            )
            ->columns(App::make(TableTemplate::class)())
            ->searchPlaceholder('Search by User')
            ->searchOnBlur()
            ->filters([
                //
            ])
            ->query(HelperFunctions::isUser(Auth::user())->isDev() ? Snack::query()->where('status', 'APPROVED') : Snack::query())
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make()
                        ->form(App::make(ViewTemplate::class)()),
                    Tables\Actions\DeleteAction::make(),
                ]),
                
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
