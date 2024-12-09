<?php

namespace App\Filament\Auth;

use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseAuth;

class Register extends BaseAuth
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                /** @phpstan-ignore method.notFound */
                $this->getEmailFormComponent()->regex(function () {
                    $regex = '/^.+(';
                    $isFirst = true;
                    foreach (config('app.allowed_email_domains') as $domain) {
                        if ($isFirst) {
                            $regex .= $domain;
                            $isFirst = false;

                            continue;
                        }
                        $regex .= '|' . $domain;
                    }
                    $regex .= ')/i';

                    return $regex;
                }),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ])
            ->statePath('data');
    }
}
