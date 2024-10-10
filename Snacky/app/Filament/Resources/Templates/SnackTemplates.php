<?php

namespace App\Filament\Resources\Templates;

use App\Models\Snack;
use App\Models\Vote;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class SnackTemplates
{

    public static function getTable() :array
    {
        $user_id = Auth::user()->id;
        $up_vote = 'UPVOTE';
        $down_vote = 'DOWNVOTE';

        function votingFun(Snack $record, string $vote_type, mixed $user_id)
        {
            $vote_count = Vote::where('user_id', $user_id)->count();
            $query = Vote::where('user_id', $user_id)
                ->where('vote_type', $vote_type)
                ->where('snack_id', $record->id);
            if ($query->exists()) {
                $query->delete();
            } else if ($vote_count < config('app.vote_limit_per_user')) {
                Vote::create([
                    'vote_type' => $vote_type,
                    'user_id' => $user_id,
                    'snack_id' => $record->id
                ]);
            } else {
                Notification::make()
                    ->title("Your voting limit is exhausted\r\nThe current limit is: " . config('app.vote_limit_per_user'))
                    ->warning()
                    ->send();
            }
        }

        $table = array(
            TextColumn::make('name'),
            TextColumn::make('category.name'),
            TextColumn::make('description')
                ->words(10)
                ->wrap(),
            TextColumn::make('price')
                ->money('UZS'),
            TextColumn::make('link')
                ->limit(17)
                ->url(fn (Snack $record) => $record->link)
                ->openUrlInNewTab(),
            TextColumn::make('votes_count')
                ->counts([
                    'votes' => fn (Builder $query) => $query->where('vote_type', $up_vote)
                ])
                ->action(fn (Snack $record) => votingFun($record, $up_vote, $user_id))
                ->label('Upvotes')
                ->icon('heroicon-o-hand-thumb-up')
                ->iconColor(function (Snack $record) use($up_vote) {
                    if ($record->votes()
                        ->where('user_id', Auth::user()->id)
                        ->where('snack_id', $record->id)
                        ->where('vote_type', $up_vote)
                        ->exists()) {
                        return 'primary';
                    }
                })
                ->sortable()
                ->hidden(function (Table $table){
                    return $table->getModelLabel() == 'Submission' ? true : false;
                }),
            TextColumn::make('votes_count_down')
                ->getStateUsing(fn (Snack $record) => $record->votes()->where('vote_type', $down_vote)->count())
                ->action(fn (Snack $record) => votingFun($record, $down_vote, $user_id))
                ->label('Downvotes')
                ->icon('heroicon-o-hand-thumb-down')
                ->iconColor(function (Snack $record) use($down_vote) {
                    if ($record->votes()
                        ->where('user_id', Auth::user()->id)
                        ->where('snack_id', $record->id)
                        ->where('vote_type', $down_vote)
                        ->exists()) {
                        return 'primary';
                    }
                })
                ->sortable(query: function (Builder $query, string $direction) use($down_vote) {
                    return $query->withCount([
                        'votes as down_votes' => function (Builder $query) use($down_vote) {
                            $query->where('vote_type', $down_vote);
                        }])
                        ->orderBy('down_votes', $direction);
                })
                ->hidden(function (Table $table){
                    return $table->getModelLabel() == 'Submission' ? true : false;
                }),
            TextColumn::make('user.name')
        );

        if (Auth::user()->isDev()) {
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
                    'Disapproved' => 'warning',
                    'In process' => 'gray'
                })
                ->sortable()
                ->hidden(function (Table $table){
                    return $table->getModelLabel() == 'Snack' ? true : false;
                });
        }

        if (Auth::user()->isManager() || Auth::user()->isAdmin()) {
            $table[] = SelectColumn::make('status')
                ->options([
                    'APPROVED' => 'Approved',
                    'DISAPPROVED' => 'Disapproved',
                    'IN_PROCESS' => 'In process'
                ])
                ->sortable();
        }


        return $table;
    }



    public static function getForm() :array
    {
        $from = array(
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
        );

        if (Auth::user()->isManager() || Auth::user()->isAdmin()) {
            $table[] = SelectColumn::make('status')
                ->options([
                    'APPROVED' => 'Approved',
                    'DISAPPROVED' => 'Disapproved',
                    'IN_PROCESS' => 'In process'
                ])
                ->sortable();
        }


        return $from;
    }


    


}