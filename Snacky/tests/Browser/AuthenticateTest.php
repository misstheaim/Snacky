<?php

use App\Models\User;
use Laravel\Dusk\Browser;

test('Cant see administrator tabs on developer account', function () {
    $this->seed();
    $this->browse(function (Browser $browser) {
        $browser->loginAs(User::whereHas('roles', fn ($query) => $query->where('role', config('app.dev_role')))->first())->visit('/admin')
            ->assertDontSeeIn('.fi-sidebar-item-label', 'Administrator')
            ->assertDontSeeIn('.fi-sidebar-item-label', 'Roles')
            ->assertDontSeeIn('.fi-sidebar-item-label', 'Users');
    });
});
