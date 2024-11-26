<?php

namespace App\Filament\Resources;

use App\Filament\Pages\CommentedSnacks;
use App\Filament\Resources\Helpers\HelperFunctions;
use App\Filament\Resources\NotificationResource\Pages;
use App\Filament\Resources\NotificationResource\RelationManagers;
use App\Models\Notification;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';


    protected static ?string $navigationBadgeTooltip = 'New Submissions';

    protected static ?string $navigationBadgeColor = 'primary';

    public static function getNavigationBadge(): ?string
    {
        $notCount = Notification::where('user_id', Auth::user()->id)->where('status', 'NOT_SEEN')->count();
        return $notCount !== 0 ? $notCount : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Notification::query()->where('user_id', Auth::user()->id)->whereIn('type', ['APPROVED', 'REJECTED', 'ADDED_TO_THE_RECEIPT', 'SUBMISSION'])->with(['snack.receipts'])->orderBy('status', 'desc')->orderBy('updated_at', 'desc'))
            ->contentGrid([
                'sm' => 2,
            ])
            ->headerActions([
                Action::make('You have new commented snacks!')
                    ->hidden(fn () =>  Notification::where('user_id', Auth::user()->id)->where('status', 'NOT_SEEN')->where('type', "COMMENTED")->count() === 0)
                    ->url(function () {
                        $nots = Notification::select('snack_id')->where('user_id', Auth::user()->id)->where('type', "COMMENTED")->get();
                        $snacks = array();
                        foreach ($nots as $not) {
                            $snacks[] = $not->snack_id;
                        }
                        return CommentedSnacks::getUrl(['snacks' => $snacks]);
                    })
            ])
            //->recordUrl(fn (?Model $record) => SnackResource::getUrl('view', [$record->snack_id]))
            ->columns([
                Panel::make([
                    Grid::make(2)
                        ->schema([
                            Stack::make([
                                TextColumn::make('status')
                                    ->state(fn (Model $record) => match ($record->status) {
                                        'SEEN' => 'viewed',
                                        'NOT_SEEN' => 'new',
                                    })
                                    ->action(function (Model $record) {
                                        if ($record->status === "NOT_SEEN") {
                                            $record->status = "SEEN";
                                            $record->save();
                                        }
                                    })
                                    ->badge()
                                    ->tooltip('Click me!'),
                                TextColumn::make('snack.title_ru')
                                    ->limit(50)
                                    ->url(fn (?Model $record) => SnackResource::getUrl('view', [$record->snack_id])),
                            ])->space(2),
                            Stack::make([
                                TextColumn::make('type')
                                    ->badge()
                                    ->color(fn (string $state) => match($state) {
                                        'APPROVED' => 'success',
                                        'REJECTED' => 'danger',
                                        'ADDED_TO_THE_RECEIPT' => 'primary',
                                        'SUBMISSION' => 'gray',
                                    }),
                                TextColumn::make('updated_at')
                                    ->label('date'),
                            ])->space(2)
                        ])
                ])
                ->extraAttributes(fn (Model $record) => match ($record->status) {
                    "NOT_SEEN" => [
                        'style' => 'background-color: rgb(245, 158, 11, 0.1); ',
                        ],
                    "SEEN" => [],
                }),
                Panel::make([
                    Grid::make(2)
                        ->schema([
                            Stack::make([
                                ImageColumn::make('snack.low_image_link')
                                    ->label('Image'),
                                TextColumn::make('snack.price')
                                    ->prefix("UZS "),
                                TextColumn::make('snack.link')
                                    ->hidden( ! HelperFunctions::isUser(Auth::user())->isManager())
                                    ->icon('heroicon-o-cursor-arrow-ripple')
                                    ->limit(10)
                                    ->url(fn ($state) => $state)
                            ])->space(3),
                            Stack::make([
                                TextColumn::make('snack.user.name')
                                    ->label('User'),
                                SelectColumn::make('snack.status')
                                    ->hidden( ! HelperFunctions::isUser(Auth::user())->isManager())
                                    ->options(function (?Model $record) {
                                        if ($record->snack->receipts->count() !== 0) {
                                            return [
                                                'APPROVED' => 'Approved',
                                                'IN_PROCESS' => 'In process'
                                            ];
                                        } else {
                                            return [
                                                'APPROVED' => 'Approved',
                                                'DISAPPROVED' => 'Disapproved',
                                                'IN_PROCESS' => 'In process'
                                            ];
                                        }
                                    })
                                    ->selectablePlaceholder(false),
                                TextColumn::make('snack.link')
                                    ->hidden(HelperFunctions::isUser(Auth::user())->isManager())
                                    ->icon('heroicon-o-cursor-arrow-ripple')
                                    ->limit(10)
                                    ->url(fn ($state) => $state)
                            ])->space(3),
                    ])
                ])->collapsible()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
