<?php

namespace App\Filament\Resources\ReceiptResource\Widgets;

use App\Contracts\HttpProductReceiver;
use App\Models\Receipt;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Snack;
use Exception;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class TableWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public ?Model $record = null;

    protected static bool $isLazy = false;


    public $up_vote = 'UPVOTE';
    public $down_vote = 'DOWNVOTE';


    public function table(Table $table): Table
    {
        return $table
            ->query(Snack::query()
                ->whereIn('status', ['APPROVED', 'IN_PROCESS'])
                ->withExists(['receipts' => fn ($query) => $query->where('receipt_id', $this->record->id)])
                ->with('receipts')
                ->withCount([
                    'votes as down_votes' => function (Builder $query) {
                        $query->where('vote_type', $this->down_vote);
                    }])
                ->orderBy('receipts_exists', 'desc'))
            ->heading('Snacks')
            ->searchPlaceholder('Search by Title')
            ->headerActions([
                Action::make('Update prices of attached snacks')
                    ->action(function () {
                        foreach ($this->record->snacks as $snack) {
                            $ok = true;
                            $receiver = App::make(HttpProductReceiver::class);
                            try {
                                $receiver->makeWork($snack->uzum_product_id);
                            } catch (Exception $e) {
                                $ok = false;
                                Notification::make()
                                    ->warning()
                                    ->title('Something went wrong! Data wasn\'t updated!')
                                    ->send();
                            }
                            if ($ok) {
                                Notification::make()
                                    ->success()
                                    ->title('Data successfully updated!')
                                    ->send();
                            }
                        }
                    })
            ])
            ->defaultGroup(
                Group::make('category.title_ru')
                    //->orderQueryUsing(fn (Builder $query, $direction) => $query->withExists('receipts')->orderBy('receipts_exists', 'desc')/* ->join('categories', 'snacks.category_id', 'categories.uzum_category_id')->orderBy('categories.title_ru', $direction) */)
                    ->groupQueryUsing(fn (Builder $query) => $query->groupBy('category_id'))
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
            )
            ->columns([
                CheckboxColumn::make('receiptss')
                    ->label('Attach')
                    ->width('5%')
                    ->alignCenter()
                    ->updateStateUsing(function (?Model $record, $state) {
                        if ($state) {
                            $isApproved = match ($record->status) {
                                'APPROVED' => true,
                                default => false,
                            };
                            if ( ! $isApproved ) {
                                Notification::make()
                                    ->warning()
                                    ->title('You are trying to attach unapproved snack!')
                                    ->send();
                                return;
                            }
                            $this->record->snacks()->syncWithoutDetaching($record->id);
                            //$this->calculateAndSaveTotalProce();
                        } else {
                            $this->record->snacks()->detach($record->id);
                            //$this->calculateAndSaveTotalProce();
                        }
                    })
                    ->getStateUsing(function (?Model $record) {
                        if ($this->record->snacks->contains($record)) {
                            return true;
                        }
                        return false;
                    }),
                ImageColumn::make('low_image_link')
                    ->label('Image')
                    ->alignCenter(),
                TextColumn::make('title_ru')
                    ->label('Title')
                    ->width('30%')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('category.title_ru'),
                TextColumn::make('votes_count')
                    ->label(new HtmlString(Blade::render('<x-heroicon-o-hand-thumb-up class="w-6 h-6" />')))
                    ->width('1%')
                    ->counts([
                        'votes' => fn (Builder $query) => $query->where('vote_type', $this->up_vote)
                    ])
                    ->icon('heroicon-o-hand-thumb-up')
                    ->sortable(query: function (Builder $query, string $direction) {
                        $direction = match($direction) {
                            'asc' => 'desc',
                            'desc' => 'asc',
                        };
                        return $query->reorder('votes_count', $direction);
                    }),
                TextColumn::make('down_votes')
                    ->label(new HtmlString(Blade::render('<x-heroicon-o-hand-thumb-down class="w-6 h-6" />')))
                    ->width('1%')
                    ->getStateUsing(fn (Snack $record) => $record->votes()->where('vote_type', $this->down_vote)->count())
                    ->icon('heroicon-o-hand-thumb-down')
                    ->sortable(query: function (Builder $query, string $direction) {
                        $direction = match($direction) {
                            'asc' => 'desc',
                            'desc' => 'asc',
                        };
                        return $query->reorder('down_votes', $direction);
                    }),
                SelectColumn::make('status')
                    ->options([
                        'APPROVED' => 'Approved',
                        'IN_PROCESS' => 'In process'
                    ])
                    ->width('20%')
                    ->selectablePlaceholder(false)
                    ->alignCenter(),
                TextColumn::make('price')
                    ->numeric()
                    ->prefix('UZS '),
                TextInputColumn::make('item_count')
                    ->disabled(fn (?Model $record) => ! $this->record->snacks->contains($record) ? true : false)
                    ->updateStateUsing(function (?Model $record, $state) {
                        $this->record->snacks()->syncWithoutDetaching([$record->id => ['item_count' => $state]]);
                        //$this->calculateAndSaveTotalPriceForCountField($record, $state);
                    })
                    ->width('10%')
                    ->alignCenter()
                    ->getStateUsing(function (?Model $record) {
                        $count = 1;
                        foreach ($record->receipts as $receipt) {
                            if ($receipt->id === $this->record->id) {
                                return $receipt->pivot->item_count;
                            }
                        }
                        return $count;
                        
                    }),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime(),
                
            ])
            ->filters([
                //
            ]);
    }

    public function calculateAndSaveTotalProce() :void
    {
        $total_price = 0;
        foreach ($this->record->snacks as $snack) {
            $total_price += $snack->price * $snack->pivot->item_count;
        }
        $this->record->total_price = $total_price;
        $this->record->save();
    }

    public function calculateAndSaveTotalPriceForCountField($record, $state) :void
    {
        $total_price = 0;
        foreach ($this->record->snacks as $snack) {
            if ($snack->id === $record->id) {
                $total_price += $snack->price * $state;
            } else {
                $total_price += $snack->price * $snack->pivot->item_count;
            }
        }
        $this->record->total_price = $total_price;
        $this->record->save();
    }
}
