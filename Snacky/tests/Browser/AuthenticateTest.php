<?php

use App\Models\Category;
use App\Models\Snack;
use App\Models\User;
use Database\Seeders\VerifySeeder;
use Laravel\Dusk\Browser;

//>>>>>>>>>>>>> Main Page <<<<<<<<<<<<<<<<<<<

// test('Cant see administrator tabs on developer account', function () {
//     $this->seed();
//     $this->seed(VerifySeeder::class);
//     $this->browse(function (Browser $browser) {
//         $browser->loginAs(User::whereHas('roles', fn ($query) => $query->where('role', config('app.dev_role')))->first())->visit('/admin')
//             ->assertDontSeeIn('.fi-sidebar-nav', 'Administrator')
//             ->assertDontSeeIn('.fi-sidebar-nav', 'Roles')
//             ->assertDontSeeIn('.fi-sidebar-nav', 'Users');
//     });
// });

// test('Can see administrator tabs on administrator account', function () {
//     $this->seed();
//     $this->seed(VerifySeeder::class);
//     $this->browse(function (Browser $browser) {
//         $browser->loginAs(User::whereHas('roles', fn ($query) => $query->where('role', config('app.admin_role')))->first())->visit('/admin')
//             ->assertSeeIn('.fi-sidebar-nav', 'Administrator')
//             ->assertSeeIn('.fi-sidebar-nav', 'Roles')
//             ->assertSeeIn('.fi-sidebar-nav', 'Users');
//     });
// });


// test('Can see manager\'s tabs on manager account', function () {
//     $this->seed();
//     $this->seed(VerifySeeder::class);
//     $this->browse(function (Browser $browser) {
//         $browser->loginAs(User::whereHas('roles', fn ($query) => $query->where('role', config('app.manager_role')))->first())->visit('/admin')
//             ->assertSeeIn('.fi-sidebar-nav', 'Receipts')
//             ->assertSeeIn('.fi-dashboard-page', 'Total Users')
//             ->assertSeeIn('.fi-dashboard-page', 'Total Snacks')
//             ->assertSeeIn('.fi-dashboard-page', 'Snacks added per month');
//     });
// });

// test('Cant see manager\'s tabs on developer account', function () {
//     $this->seed();
//     $this->seed(VerifySeeder::class);
//     $this->browse(function (Browser $browser) {
//         $browser->loginAs(User::whereHas('roles', fn ($query) => $query->where('role', config('app.dev_role')))->first())->visit('/admin')
//             ->assertDontSeeIn('.fi-sidebar-nav', 'Receipts')
//             ->assertdontSeeIn('.fi-dashboard-page', 'Total Users')
//             ->assertdontSeeIn('.fi-dashboard-page', 'Total Snaсks')
//             ->assertdontSeeIn('.fi-dashboard-page', 'Snacks added per month');
//     });
// });

// test('Cant see developer\'s tabs on manager account', function () {
//     $this->seed();
//     $this->seed(VerifySeeder::class);
//     $this->browse(function (Browser $browser) {
//         $browser->loginAs(User::whereHas('roles', fn ($query) => $query->where('role', config('app.manager_role')))->first())->visit('/admin')
//             ->assertDontSeeIn('.fi-sidebar-nav', 'Submissions');
//     });
// });

// test('Can see developer\'s tabs on developer account', function () {
//     $this->seed();
//     $this->seed(VerifySeeder::class);
//     $this->browse(function (Browser $browser) {
//         $browser->loginAs(User::whereHas('roles', fn ($query) => $query->where('role', config('app.dev_role')))->first())->visit('/admin')
//             ->assertSeeIn('.fi-sidebar-nav', 'Submissions');
//     });
// });

// >>>>>>>>>>>> Snack Table <<<<<<<<<<<<<<<

// test('Cant approve snack on developer account', function () {
//     $this->seed();
//     $this->seed(VerifySeeder::class);
//     $categories = Category::factory()->count(10)->create();
//     $snacks = Snack::factory()
//         ->for($categories[rand(0, count($categories) - 1)])
//         ->for(User::inRandomOrder()->first())
//         ->state([
//             'status' => 'APPROVED'
//         ])
//         ->count(10)
//         ->create();
//     $this->browse(function (Browser $browser) use ($snacks) {
//         $browser->loginAs(User::whereHas('roles', fn ($query) => $query->where('role', config('app.dev_role')))->first())
//                 ->visit('/admin/snacks')
//                 ->assertSourceMissing('x-model="state"')
//                 ->assertSeeIn('.fi-ta-table', $snacks[0]->title_ru);
//     });
// });

test('Can approve snack on manager account', function () {
    $this->seed();
    $this->seed(VerifySeeder::class);
    $categories = Category::factory()->count(10)->create();
    Snack::factory()
        ->for($categories[rand(0, count($categories) - 1)])
        ->for(User::inRandomOrder()->first())
        ->count(1)
        ->create();
    $this->browse(function (Browser $browser) {
        $browser->loginAs(User::whereHas('roles', fn ($query) => $query->where('role', config('app.manager_role')))->first())
            ->visit('/admin/snacks')
            ->assertAttribute('.fi-select-input', 'x-model', 'state')
            ->pause(1000)
            ->select('.fi-select-input', 'APPROVED')
            ->assertValue('.fi-select-input', 'APPROVED');
    });
});
