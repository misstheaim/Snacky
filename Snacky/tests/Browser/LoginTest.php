<?php

use Database\Seeders\VerifySeeder;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;

uses(DatabaseTruncation::class);

test('can see microsoft authentication login page', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/admin/login')
            ->assertSee('Microsoft');
    });
});

test('can log in', function () {
    $this->seed();
    $this->seed(VerifySeeder::class);

    $this->browse(function (Browser $browser) {
        $browser->logout()->visit('/admin/login')
            ->type('#data\.email', 'admin@admin.com')
            ->type('#data\.password', 'admin')
            ->press('Sign in')
            ->waitForLocation('/admin')
            ->assertPathIs('/admin');
    });
});

test('2FA verification appears after signing up', function () {
    $this->seed();

    $this->browse(function (Browser $browser) {
        $browser->logout()->visit('/admin/register')
            ->type('#data\.name', 'Bob')
            ->type('#data\.email', 'bob@' . config('app.allowed_email_domains')[0])
            ->type('#data\.password', 'bob123456798')
            ->type('#data\.passwordConfirmation', 'bob123456798')
            ->press('Sign up')
            ->waitForLocation('/admin/two-factor-auth')
            ->assertPathIs('/admin/two-factor-auth');
    });
});

test('Checks if only allowed email domen can access the app', function () {
    $this->browse(function (Browser $browser) {
        $browser->logout()->visit('/admin/register')
            ->type('#data\.name', 'Bob')
            ->type('#data\.email', 'bob@strangedomain.com')
            ->type('#data\.password', 'bob123456798')
            ->type('#data\.passwordConfirmation', 'bob123456798')
            ->press('Sign up')
            ->waitForText('The email address field format is invalid')
            ->assertSee('The email address field format is invalid');
    });
});
