<?php

namespace App\Filament\Pages;

use Filament\Pages\Auth\EditProfile;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;

class ProfileForm extends EditProfile
{
    protected static bool $shouldRegisterNavigation = false;

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getAvatarComponent(),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data')
                    ->inlineLabel(! static::isSimple()),
            ),
        ];
    }

    protected function getAvatarComponent(): Component
    {
        return FileUpload::make('avatar')
            ->image()
            ->imageEditor()
            ->disk('s3')
            ->directory('Avatars')
            ->maxSize(2048)
            ->imageCropAspectRatio('1:1');
    }
}
