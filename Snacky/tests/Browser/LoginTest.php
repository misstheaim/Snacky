<?php

use Laravel\Dusk\Browser;

test('example', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/admin/login')
                ->assertSee('Snacky');
    });
});
