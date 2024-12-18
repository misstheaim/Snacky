<?php

namespace App\Filament\Resources;

use App\Contracts\Filament\Snack\FormTemplate;
use App\Contracts\Filament\Snack\TableTemplate;
use App\Contracts\Filament\Snack\ViewTemplate;
use App\Filament\Resources\Helpers\HelperFunctions;
use App\Filament\Resources\SnackResource\Pages;
use App\Filament\Resources\SnackResource\RelationManagers\CommentsRelationManager;
use App\Models\Receipt;
use App\Models\Snack;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SnackResource extends Resource
{
    protected static ?string $model = Snack::class;

    protected static ?string $navigationIcon = 'heroicon-o-percent-badge';

    protected static ?string $slug = 'snacks';

    protected static ?string $label = 'Snack';

    public ?Model $record = null;

    protected static ?string $navigationBadgeTooltip = 'The number of unprocessed snacks';

    protected static ?string $navigationBadgeColor = 'primary';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'IN_PROCESS')->count();

        return HelperFunctions::isUser(Auth::user())->isDev() ? null : ($count !== 0 ? $count : null);
    }

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
            ->query(HelperFunctions::isUser(Auth::user())->isDev() ? Snack::query()->where('status', 'APPROVED')->withExists('receipts')->withCount([
                'votes as down_votes' => function (Builder $query) {
                    $query->where('vote_type', 'DOWNVOTE');
                }]) : Snack::query()->withExists('receipts')->withCount([
                    'votes as down_votes' => function (Builder $query) {
                        $query->where('vote_type', 'DOWNVOTE');
                    }]))
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make()
                        ->form(App::make(ViewTemplate::class)()),
                    Tables\Actions\DeleteAction::make()
                        ->modalDescription(function (?Model $record) {
                            /** @var \App\Models\Snack $record */
                            return $record->receipts_exists ? 'This snack attached to the receipt, are you sure you want to delete it? All attached receipts will be recalculated' : 'Are you sure you would like to do this?';
                        })
                        ->color(function (?Model $record) {
                            /** @var \App\Models\Snack $record */
                            return $record->receipts_exists ? 'danger' : 'warning';
                        })
                        ->modalHeading(function (?Model $record) {
                            /** @var \App\Models\Snack $record */
                            return $record->receipts_exists ? 'Warning! Deleting attached Snack!' : 'Delete Snack';
                        })
                        ->before(function (?Model $record) {
                            /** @var \App\Models\Snack $record */
                            if ($record->receipts_exists) {
                                $receipts = Receipt::all();
                                foreach ($receipts as $receipt) {
                                    $receipt->snacks()->detach($record->id);
                                }
                            }
                        }),
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->modalSubmitActionLabel('Delete all Snacks')
                        ->modalDescription('Deleting All Snacks will couse detaching them from receipts, if they were attached!')
                        ->extraModalFooterActions(fn (Tables\Actions\DeleteBulkAction $action) => [
                            $action->makeModalSubmitAction('DeleteOnlyNotAttachedSnacks', arguments: ['notAttached' => true]),
                        ])
                        ->action(function (Collection $records, array $arguments) {
                            if ($arguments['notAttached'] ?? false) {
                                /** @var iterable<\App\Models\Snack> $records */
                                foreach ($records as $record) {
                                    if (! $record->receipts_exists) {
                                        $record->delete();
                                    }
                                }
                            } else {
                                /** @var iterable<\App\Models\Snack> $records */
                                foreach ($records as $record) {
                                    if ($record->receipts_exists) {
                                        $receipts = Receipt::all();
                                        foreach ($receipts as $receipt) {
                                            $receipt->snacks()->detach($record->id);
                                        }
                                    }
                                    $record->delete();
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //CommentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSnacks::route('/'),
            'create' => Pages\CreateSnack::route('/create'),
            'edit' => Pages\EditSnack::route('/{record}/edit'),
            'view' => Pages\ViewSnack::route('/{record}'),
        ];
    }
}
