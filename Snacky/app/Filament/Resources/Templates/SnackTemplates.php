<?php

namespace App\Filament\Resources\Templates;

use App\Models\Category;
use App\Models\Snack;
use App\Models\User;
use App\Models\Vote;
use App\Services\Helpers\HelperSortProductData;
use App\Services\UzumHttpProductReceiver;
use Closure;
use Doctrine\DBAL\Schema\Schema;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Str;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Livewire\Component;

class SnackTemplates
{

    public static $buffer;

    public static function getTable() :array
    {
        $user_id = Auth::user()->id;
        $up_vote = 'UPVOTE';
        $down_vote = 'DOWNVOTE';

        
        $isAdmin = HelperFunctions::isUser(Auth::user())->isAdmin();
        $isDev = HelperFunctions::isUser(Auth::user())->isDev();
        $isManager = HelperFunctions::isUser(Auth::user())->isManager();


        $table = array(
            TextColumn::make('title_ru')
                ->wrap()
                ->width('40%')
                ->label('Title'),
            ImageColumn::make('low_image_link')
                ->label('Image')
                ->alignCenter(),
            TextColumn::make('category.title_ru')
                ->width('30%')
                ->wrap(),
            TextColumn::make('description_ru')
                ->words(5)
                ->label('Description')
                ->wrap(),
            TextColumn::make('price')
                ->money('UZS'),
            TextColumn::make('link')
                ->limit(7)
                ->url(fn (Snack $record) => $record->link)
                ->openUrlInNewTab(),
            TextColumn::make('votes_count')
                ->label('')
                ->width('1%')
                ->alignCenter()
                ->counts([
                    'votes' => fn (Builder $query) => $query->where('vote_type', $up_vote)
                ])
                ->action(fn (Snack $record) => HelperFunctions::votingFun($record, $up_vote, $user_id))
                //->label('Upvotes')
                ->icon('heroicon-o-hand-thumb-up')
                ->iconColor(function (Snack $record) use($up_vote, $user_id) {
                        return HelperFunctions::setIconColor($record, $user_id, $up_vote);
                })
                ->sortable()
                ->hidden(function (Table $table){
                    return $table->getModelLabel() == 'Submission' ? true : false;
                }),
            TextColumn::make('votes_count_down')
                ->label('')
                ->width('1%')
                ->alignCenter()
                ->getStateUsing(fn (Snack $record) => $record->votes()->where('vote_type', $down_vote)->count())
                ->action(fn (Snack $record) => HelperFunctions::votingFun($record, $down_vote, $user_id))
                //->label('Downvotes')
                ->icon('heroicon-o-hand-thumb-down')
                ->iconColor(function (Snack $record) use($down_vote, $user_id) {
                    return HelperFunctions::setIconColor($record, $user_id, $down_vote);
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

        if ($isDev) {
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
                ->sortable()
                ->alignCenter()
                ->width('40%')
                ->hidden(function (Table $table){
                    return $table->getModelLabel() == 'Snack' ? true : false;
                });
        }

        if ($isManager || $isAdmin) {
            $table[] = SelectColumn::make('status')
                ->options([
                    'APPROVED' => 'Approved',
                    'DISAPPROVED' => 'Disapproved',
                    'IN_PROCESS' => 'In process'
                ])
                ->alignCenter()
                ->width('40%')
                ->sortable();
        }


        return $table;
    }


    public static function getForm() :array
    {
        $isAdmin = HelperFunctions::isUser(Auth::user())->isAdmin();
        $isManager = HelperFunctions::isUser(Auth::user())->isManager();


        $from = array(
            TextInput::make('link')
                ->required()
                ->activeUrl()
                ->rules(['valid_product' => fn() => function (string $attribute, mixed $value, Closure $fail) {
                    if (parse_url($value)['host'] != config('uzum.hostname')) {
                        $fail('Incorrect hostname');
                        return;
                    }

                    $reciever = new UzumHttpProductReceiver();
                    $id = Str::of($value)->match('/(?<=-)\d+(?!.*(?<=-)\d+)/');

                    $record = $reciever->receiveProductData($id);

                    if (isset($record['failed'])) {
                        $fail('This is the wrong link');
                        return;
                    }
                    $data = HelperSortProductData::getSortedProduct($record);
                    SnackTemplates::$buffer = $data;

                    if (is_null($data['category_id'])) {
                        $fail('The link must be from Product category');
                    }
                }])
                ->label('Add a link to the product on Uzum Market')
                ->validationMessages([
                    'required' => 'Please add a link to the product.',
                    'active_url' => 'This field must be a valid URL.'
                ]),
            Hidden::make('user_id')
                ->default(fn () => Auth::user()->id),
            Hidden::make('title_ru'),
            Hidden::make('title_uz'),
            Hidden::make('uzum_product_id'),
            Hidden::make('price'),
            Hidden::make('category_id'),
            Hidden::make('description_ru'),
            Hidden::make('high_image_link'),
            Hidden::make('low_image_link'),
        );

        if ($isManager || $isAdmin) {
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



    public static function getViewForm() :array
    {
        $isAdmin = HelperFunctions::isUser(Auth::user())->isAdmin();
        $isManager = HelperFunctions::isUser(Auth::user())->isManager();


        $from = array(
            Section::make()
                ->columns(2)
                ->schema([
                    Section::make()->columns(1)->schema([
                    TextInput::make('title_ru')->required()
                        ->label('Title'),
                    Select::make('category_id')
                        ->required()
                        ->relationship('category', 'title_ru'),
                    Textarea::make('description_ru')
                        ->label('Description')
                        ->rows(5)
                        ->placeholder('Here you can write something about your product'),
                    TextInput::make('link')
                        ->url()
                        ->required()
                        ->activeUrl()
                        ->label('Link to the product')
                        ->suffixAction(
                            Action::make('redirect')
                                ->icon('heroicon-m-globe-alt')
                                ->action(function (?string $state, Component $livewire) {
                                    $livewire->js("window.open('$state');");
                                })
                        ),
                    TextInput::make('price')
                        ->prefix('UZS'),
                    ])->columnSpan(1),
                    Section::make()->columns(1)->schema([
                    ViewField::make('high_image_link')
                        ->label('Image')
                        ->view('forms.components.view-image')
                    ])->columnSpan(1)
                ])
        );

        if ($isManager || $isAdmin) {
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


class HelperFunctions
{
    public static function votingFun(Snack $record, string $vote_type, mixed $user_id)
    {
        $self_click = false;
        $if_deleted = false;
        $vote_count = Vote::where('user_id', $user_id)->count();
        $query = Vote::where('user_id', $user_id)
            ->where('snack_id', $record->id);
        if ($query->exists()) {
            $self_click = $query->select('vote_type')->first()->vote_type === $vote_type;
            $query->delete();
            $if_deleted = true;
        }
        if (!$self_click) {
            if ($if_deleted || $vote_count < config('app.vote_limit_per_user')) {
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
    }

    public static function setIconColor(Snack $record, $user_id, $vote_type) 
    {
        if ($record->votes()
            ->where('user_id', $user_id)
            ->where('snack_id', $record->id)
            ->where('vote_type', $vote_type)
            ->exists()) {
            return 'primary';
        }
    }

    public static function isUser(User $user) :User {
        return $user;
    }
}