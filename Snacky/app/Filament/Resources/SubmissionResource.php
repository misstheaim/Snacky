<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubmissionResource\Pages;
use App\Filament\Resources\SubmissionResource\RelationManagers;
use App\Models\Snack;
use App\Models\Submission;
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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class SubmissionResource extends Resource
{
    protected static ?string $model = Snack::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $slug = 'snack-submissions';

    protected static ?string $label = 'Submission';

    public static function canViewAny() :bool
    {
        return !Auth::user()->isManager();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                Select::make('category_id')
                    ->required()
                    ->relationship('category', 'name'),
                Textarea::make('description')
                    ->helperText('Optional')
                    ->placeholder('Here you can write something about your product'),
                TextInput::make('link')
                    ->required()
                    ->activeUrl()
                    ->label('Add a link to the product on korzinka.uz')
                    ->validationMessages([
                        'required' => 'Please add a link to the product.',
                        'active_url' => 'This field must be a valid URL.'
                    ]),
                Hidden::make('user_id')
                    ->default(fn () => Auth::user()->id)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(TableController::getSnackTable())
            ->filters([
                
            ])
            ->query(Snack::query()->where('user_id', Auth::user()->id))
            ->actions([
                Tables\Actions\EditAction::make(),
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
