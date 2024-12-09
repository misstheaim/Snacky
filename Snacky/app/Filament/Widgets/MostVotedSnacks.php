<?php

namespace App\Filament\Widgets;

use App\Models\Snack;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class MostVotedSnacks extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 10;

    public $up_vote = 'UPVOTE';

    public $down_vote = 'DOWNVOTE';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Snack::query()
                    ->withCount([
                        'votes as up_votes' => function (Builder $query) {
                            $query->where('vote_type', $this->up_vote);
                        },
                        'votes as down_votes' => function (Builder $query) {
                            $query->where('vote_type', $this->down_vote);
                        },
                    ])
                    ->having('up_votes', '>', 0)
                    ->orderBy('up_votes', 'desc')->take(5)
            )
            ->paginated(false)
            ->columns([
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
                    ->prefix('UZS '),
                TextColumn::make('link')
                    ->extraAttributes([
                        'style' => 'justify-content: center;',
                    ])
                    ->alignCenter()
                    ->formatStateUsing(function (?string $state) {
                        return new HtmlString('<style>.link:hover { color: rgb(234 179 8); }</style><a href="' . $state . '" target="_blank"> ' . Blade::render('<x-heroicon-o-cursor-arrow-ripple class="link text-gray-400 w-6 h-6"/>') . ' </a>');
                    }),
                TextColumn::make('up_votes')
                    ->label(new HtmlString(Blade::render('<x-heroicon-o-hand-thumb-up class="w-6 h-6" />')))
                    ->icon('heroicon-o-hand-thumb-up')
                    ->iconColor(function (Snack $record) {
                        return $this->setIconColor($record, Auth::user()->id, $this->up_vote);
                    }),
                TextColumn::make('down_votes')
                    ->label(new HtmlString(Blade::render('<x-heroicon-o-hand-thumb-down class="w-6 h-6" />')))
                    ->icon('heroicon-o-hand-thumb-down')
                    ->iconColor(function (Snack $record) {
                        return $this->setIconColor($record, Auth::user()->id, $this->down_vote);
                    }),
                TextColumn::make('user.name'),
            ]);
    }

    private function setIconColor(Snack $record, $user_id, $vote_type)
    {
        if (
            $record->votes()
            ->where('user_id', $user_id)
            ->where('snack_id', $record->id)
            ->where('vote_type', $vote_type)
            ->exists()
        ) {
            return 'primary';
        }
    }
}
