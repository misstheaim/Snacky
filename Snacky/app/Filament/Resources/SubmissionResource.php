<?php

namespace App\Filament\Resources;

use App\Contracts\Filament\Snack\TableTemplate;
use App\Contracts\Filament\Snack\FormTemplate;
use App\Contracts\Filament\Snack\ViewTemplate;
use App\Filament\Resources\SubmissionResource\Pages;
use App\Filament\Resources\Helpers\HelperFunctions;
use App\Models\Snack;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SubmissionResource extends Resource
{
    protected static ?string $model = Snack::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $slug = 'snack-submissions';

    protected static ?string $label = 'Submission';

    public static function canViewAny() :bool
    {
        return ! HelperFunctions::isUser(Auth::user())->isManager(); 
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(App::make(FormTemplate::class)());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(App::make(TableTemplate::class)())
            ->filters([
                
            ])
            ->query(Snack::query()->where('user_id', Auth::user()->id))
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
            'index' => Pages\ListSubmissions::route('/'),
            'create' => Pages\CreateSubmission::route('/create'),
            'edit' => Pages\EditSubmission::route('/{record}/edit'),
        ];
    }
}
