<?php

//------------
// HOW TO RUN
//
// php artisan test --testsuite=Feature --filter=HomePageTest
//------------
use App\Models\Desk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function makeHomeUser(array $overrides = []): User
{
    return User::factory()->create(array_merge([
        'surname' => 'Tester',
        'username' => 'tester_' . Str::random(8),
        'role' => 'user',
    ], $overrides));
}

// --------- GUEST HERO STATE ---------
test('guest sees welcome prompt without desk control script', function () {
    // Arrange

    // Act
    $response = $this->get(route('home'));

    // Assert
    $response
        ->assertOk()
        ->assertSeeText('Stand up for your health! Please log in to start tracking your progress.')
        ->assertDontSee('window.LevelUp.deskControl');
});

// --------- AUTH CLOCK SHELL ---------
test('authenticated user sees focus clock shell even without configured desk', function () {
    // Arrange
    $user = makeHomeUser();

    // Act
    $response = $this->actingAs($user)->get(route('home'));

    // Assert
    $response
        ->assertOk()
        ->assertSee('window.LevelUp.deskControl', false)
        ->assertSee('enabled: false', false)
        ->assertSeeText('Ready to level up your health today?');
});

// --------- PARTIAL DESK CONFIG ---------
test('desk without positions keeps simulator control disabled', function () {
    // Arrange
    $desk = Desk::create([
        'desk_model' => 'FocusLift',
        'serial_number' => 'DL-' . Str::random(8),
    ]);

    $user = makeHomeUser([
        'desk_id' => $desk->id,
        'sitting_position' => null,
        'standing_position' => 110,
    ]);

    // Act
    $response = $this->actingAs($user)->get(route('home'));

    // Assert
    $response
        ->assertOk()
        ->assertSee('enabled: false', false)
        ->assertSee('deskSerial: "' . $desk->serial_number . '"', false)
        ->assertSee('sitUrl: null', false);
});

// --------- FULL DESK CONTROL ---------
test('configured desk exposes simulator endpoints to the focus clock', function () {
    // Arrange
    $desk = Desk::create([
        'desk_model' => 'FocusLift',
        'serial_number' => 'DL-' . Str::random(8),
    ]);

    $user = makeHomeUser([
        'desk_id' => $desk->id,
        'sitting_position' => 90,
        'standing_position' => 110,
    ]);

    $sitRoute = route('simulator.desks.sit', ['desk' => $desk->serial_number]);
    $standRoute = route('simulator.desks.stand', ['desk' => $desk->serial_number]);

    // Act
    $response = $this->actingAs($user)->get(route('home'));

    // Assert
    $response
        ->assertOk()
        ->assertSee('enabled: true', false)
        ->assertSee('sitUrl: ' . json_encode($sitRoute), false)
        ->assertSee('standUrl: ' . json_encode($standRoute), false);
});

// --------- CLOCK ASSETS LOADED ---------
test('authenticated users load focus clock assets', function () {
    $user = makeHomeUser();

    $response = $this->actingAs($user)->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('/build/assets/focus-clock-', false)
        ->assertSee('/build/assets/pico-timer-sync-', false)
        ->assertSee('/build/assets/app-', false);
});

// --------- NAV POINTS REFLECT USER TOTAL ---------
test('navbar points badge reflects user total points', function () {
    $user = makeHomeUser([
        'total_points' => 237,
    ]);

    $response = $this->actingAs($user)->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('data-total-points="237"', false)
        ->assertSeeText('237')
        ->assertSeeText('Points');
});

// --------- GUEST ASSET GUARD ---------
test('guests do not load focus clock assets or desk control payload', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertDontSee('resources/js/home-clock/focus-clock.js')
        ->assertDontSee('resources/js/pico-timer-sync.js')
        ->assertDontSee('window.LevelUp.deskControl');
});

// --------- CSRF TOKEN PRESENT ---------
test('home page includes csrf token meta tag for simulator calls', function () {
    $user = makeHomeUser();

    $response = $this->actingAs($user)->get(route('home'));

    $response
        ->assertOk()
        ->assertSee('<meta name="csrf-token"', false)
        ->assertSee('content="', false);
});
