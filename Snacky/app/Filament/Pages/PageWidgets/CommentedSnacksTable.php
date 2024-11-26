<?php

namespace App\Filament\Pages\PageWidgets;

use App\Filament\Resources\SnackResource;
use App\Models\Notification;
use App\Models\Snack;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class CommentedSnacksTable extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = 'full';

    // public static function canView(): bool
    // {
    //     return false;
    // }

    public array $filter = array();

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Snack::query()->whereIn('id', $this->filter)->with('notifications', fn ($query) => $query->where('user_id', Auth::user()->id)->where('type', "COMMENTED"))
            )
            ->headerActions([
                Action::make('Mark all as viewed')
                    ->action(function() {
                        $notifications = Notification::where('user_id', Auth::user()->id)->where('type', 'COMMENTED')->get();
                        foreach ($notifications as $notification) {
                            $notification->status = "SEEN";
                            $notification->save();
                        }
                    })
            ])
            ->recordUrl(fn (?Model $record) => SnackResource::getUrl('view', [$record->id]))
            ->columns([
                //Panel::make([
                ImageColumn::make('low_image_link')
                    ->label('Image')
                    ->alignCenter(),
                TextColumn::make('title_ru')
                    ->wrap()
                    ->label('Title'),
                TextColumn::make('category.title_ru')
                    ->wrap(),
                TextColumn::make('price')
                    ->numeric(decimalPlaces: 0)
                    ->prefix("UZS "),
                TextColumn::make('link')
                    ->extraAttributes([
                        'style' => 'justify-content: center;'
                    ])
                    ->alignCenter()
                    ->formatStateUsing(function (?string $state) {
                        return new HtmlString('<style>.link:hover { color: rgb(234 179 8); }</style><a href="'.$state.'" target="_blank"> '.Blade::render('<x-heroicon-o-cursor-arrow-ripple class="link text-gray-400 w-6 h-6"/>').' </a>');
                    }),
                TextColumn::make('user.name'),
                TextColumn::make('notifications_count')
                    ->label('Comments count')
                    ->alignCenter()
                    ->state(fn (?Model $record) => $record->notifications->count()),
                TextColumn::make('status')
                    ->state(function (Model $record) {
                        foreach($record->notifications as $notification) {
                            if ($notification->status === "NOT_SEEN") {
                                return [
                                    'new',
                                ];
                            }
                        }
                        return 'viewed';
                    })
                    ->badge()
                    ->color(fn ($state) => $state === 'new' ? 'primary' : 'gray')
                    ->action(function(?Model $record, $state) {
                        if ($state[0] !== 'new') return;
                        foreach ($record->notifications as $notification) {
                            $notification->status = "SEEN";
                            $notification->save();
                        }
                    })
                    ->tooltip(fn ($state) => $state[0] === 'new' ? 'Mark as viewed!' : ''),
            ])
            ->actions([
                // ViewAction::make()
                //     ->action(function(?Model $record) {
                //         foreach ($record->notifications as $notification) {
                //             if ($notification->status !== "SEEN") {
                //                 $notification->status = "SEEN";
                //                 $notification->save();
                //             }
                //         }
                //     })
                //     ->url(fn (?Model $record) => SnackResource::getUrl('view', [$record->id])),
            ]);
    }
}
