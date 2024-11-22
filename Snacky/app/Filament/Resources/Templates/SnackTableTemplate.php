<?php

namespace App\Filament\Resources\Templates;

use App\Contracts\Filament\Snack\TableTemplate;
use App\Filament\Resources\Helpers\HelperFunctions;
use App\Models\Snack;
use App\Models\Vote;
use DateInterval;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class SnackTableTemplate implements TableTemplate
{
    public $up_vote = 'UPVOTE';
    public $down_vote = 'DOWNVOTE';

    public $user_id;
    
    public $isAdmin;
    public $isDev;
    public $isManager;

    public function __construct()
    {
        $this->user_id = Auth::user()->id;

        $this->isAdmin = HelperFunctions::isUser(Auth::user())->isAdmin();
        $this->isDev = HelperFunctions::isUser(Auth::user())->isDev();
        $this->isManager = HelperFunctions::isUser(Auth::user())->isManager();
    }

    public function __invoke() :array
    {
        $table = array(
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
                //->width('5%')
                ->extraAttributes([
                    'style' => 'justify-content: center;'
                ])
                ->alignCenter()
                ->formatStateUsing(function (?string $state) {
                    return new HtmlString('<style>.link:hover { color: rgb(234 179 8); }</style><a href="'.$state.'" target="_blank"> '.Blade::render('<x-heroicon-o-cursor-arrow-ripple class="link text-gray-400 w-6 h-6"/>').' </a>');
                }),
            TextColumn::make('votes_count')
                ->label(new HtmlString(Blade::render('<x-heroicon-o-hand-thumb-up class="w-6 h-6" />')))
                ->width('1%')
                ->counts([
                    'votes' => fn (Builder $query) => $query->where('vote_type', $this->up_vote)
                ])
                ->action(fn (Snack $record) => $this->votingFun($record, $this->up_vote, $this->user_id))
                ->icon('heroicon-o-hand-thumb-up')
                ->iconColor(function (Snack $record) {
                        return $this->setIconColor($record, $this->user_id, $this->up_vote);
                })
                ->sortable(query: function (Builder $query, string $direction) {
                    $direction = match($direction) {
                        'asc' => 'desc',
                        'desc' => 'asc',
                    };
                    return $query->reorder('votes_count', $direction);
                })
                ->hidden(function (Table $table){
                    return $table->getModelLabel() == 'Submission' ? true : false;
                }),
            TextColumn::make('down_votes')
                ->label(new HtmlString(Blade::render('<x-heroicon-o-hand-thumb-down class="w-6 h-6" />')))
                ->width('1%')
                ->action(fn (Snack $record) => $this->votingFun($record, $this->down_vote, $this->user_id))
                ->icon('heroicon-o-hand-thumb-down')
                ->iconColor(function (Snack $record) {
                    return $this->setIconColor($record, $this->user_id, $this->down_vote);
                })
                ->sortable(query: function (Builder $query, string $direction) {
                    $direction = match($direction) {
                        'asc' => 'desc',
                        'desc' => 'asc',
                    };
                    return $query->reorder('down_votes', $direction);
                })
                ->hidden(function (Table $table){
                    return $table->getModelLabel() == 'Submission' ? true : false;
                }),
            TextColumn::make('user.name')
                ->wrap()
                ->searchable()
        );

        if ($this->isDev) {
            $table[] = TextColumn::make('status')
                ->state(function (Snack $record) {
                    return match($record->status) {
                        'APPROVED' => 'Approved',
                        'DISAPPROVED' => 'Disapproved',
                        'IN_PROCESS' => 'In process'
                    };
                })
                ->badge()
                ->color(fn (string $state) => match($state) {
                    'Approved' => 'success',
                    'Disapproved' => 'danger',
                    'In process' => 'gray'
                })
                ->sortable(query: fn (Builder $query, string $direction) => $query->reorder('status', $direction))
                ->alignCenter()
                ->hidden(function (Table $table){
                    return $table->getModelLabel() == 'Snack' ? true : false;
                });
        }

        if ($this->isManager || $this->isAdmin) {
            $table[] = SelectColumn::make('status')
                ->options(function (?Model $record) {
                    if ($record->receipts_exists) {
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
                ->alignCenter()
                ->selectablePlaceholder(false)
                ->sortable(query: fn (Builder $query, string $direction) => $query->reorder('status', $direction));
        }


        return $table;
    }



    private function votingFun(Snack $record, string $vote_type, mixed $user_id)
    {
        $self_click = false;
        $if_deleted = false;
        $query = Vote::where('user_id', $user_id)
            ->where('snack_id', $record->id);
        if ($query->exists()) {
            $self_click = $query->select('vote_type')->first()->vote_type === $vote_type;
            if ($self_click) {
                $query->delete();
                $if_deleted = true;
            } else {
                $query->update(['vote_type' => $vote_type]);
                return;
            }
            
        }
        if (!$self_click) {
            $check = $this->checkVoteLimit($user_id);
            if ($if_deleted || $check['is_available']) {
                Vote::create([
                    'vote_type' => $vote_type,
                    'user_id' => $user_id,
                    'snack_id' => $record->id
                ]);
            } else {
                Notification::make()
                    ->title("Your voting limit is exhausted\r\nThe current limit is: " . config('app.vote_limit_per_user') . " Next vote available at: " . date_add($check['expire'], DateInterval::createFromDateString('5 hours')))
                    ->warning()
                    ->send();
            }
        }
    }

    private function checkVoteLimit($user_id) :array
    {
        $expire = null;
        $interval = DateInterval::createFromDateString(config('app.vote_limit_timeout'));
        $expire_time = date_sub(now(), $interval);
        $vote_count = Vote::where('user_id', $user_id)->where('created_at', '>', $expire_time)->count();
        if ($vote_count > 0) {
            $expire = date_add(Vote::where('user_id', $user_id)->where('created_at', '>', $expire_time)->first()->created_at, $interval);
        }
        return array('is_available' => $vote_count < config('app.vote_limit_per_user'), 'expire' => $expire);
    }


    private function setIconColor(Snack $record, $user_id, $vote_type) 
    {
        if ($record->votes()
            ->where('user_id', $user_id)
            ->where('snack_id', $record->id)
            ->where('vote_type', $vote_type)
            ->exists()) {
            return 'primary';
        }
    }

}