<?php

namespace App\Filament\Resources\SnackResource\RelationManagers;

use App\Filament\Resources\Helpers\HelperFunctions;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static bool $isLazy = false;

    public function isReadOnly(): bool
    {
        return false;
    } 

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextArea::make('comment')
                    ->required()
                    ->maxLength(60000)
                    ->rows(4)
                    ->columnSpanFull(true),
                Hidden::make('user_id')
                    ->dehydrateStateUsing(fn () => Auth::user()->id),
                Hidden::make('snack_id')
                    ->dehydrateStateUsing(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->id),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('comment')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('comment')
                    ->grow(),
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->hidden( ! (HelperFunctions::isUser(Auth::user())->isAdmin() || HelperFunctions::isUser(Auth::user())->isManager()))
            ]);
    }
}
