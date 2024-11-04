<?php

namespace App\Filament\Resources;

use App\Contracts\Filament\Snack\TableTemplate;
use App\Contracts\Filament\Snack\FormTemplate;
use App\Contracts\Filament\Snack\ViewTemplate;
use App\Filament\Resources\SubmissionResource\Pages;
use App\Filament\Resources\Helpers\HelperFunctions;
use App\Models\Snack;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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
            ->query(Snack::query()->where('user_id', Auth::user()->id)->withExists('receipts'))
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make()
                        ->form(App::make(ViewTemplate::class)()),
                    Tables\Actions\DeleteAction::make()
                        ->modalDescription(fn (?Model $record) => $record->receipts_exists ? 'This snack attached to the receipt, you cannot delete snacks attached to the receipt' : 'Are you sure you would like to do this?')
                        ->color(fn (?Model $record) => $record->receipts_exists ? 'danger' : 'warning')
                        ->modalHeading(fn (?Model $record) => $record->receipts_exists ? 'Warning! Deleting attached Snack!' : 'Delete Snack')
                        ->before(function (Tables\Actions\DeleteAction $action, ?Model $record) {
                            if ($record->receipts_exists) {
                                Notification::make()
                                    ->danger()
                                    ->title('You cannot delete snack because it exists in receipt!')
                                    ->send();
                                $action->cancel();
                            }
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->modalDescription('From the choosen list only not attached snacks will be deleted!')
                    ->action(function (Collection $records) {
                        $isAttachedRecords = false;
                        foreach($records as $record) {
                            if ( ! $record->receipts_exists) {
                                $record->delete();
                            } else {
                                $isAttachedRecords = true;
                            }
                        }
                        if ($isAttachedRecords) {
                            Notification::make()
                                ->warning()
                                ->title('Some of the records were NOT deleted!')
                                ->send();
                        }
                    }),
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
