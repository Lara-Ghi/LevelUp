<?php

use App\Models\Reward;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

test('admin can create a reward without image', function () {
    $admin = adminForUsers();

    $rewardData = [
        'card_name' => 'Test Reward',
        'card_description' => 'A test reward description',
        'points_amount' => 100,
        // Remove card_image since it's nullable and causes validation errors
    ];

    $response = $this->actingAs($admin)
        ->post(route('admin.rewards.store'), $rewardData);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // Check database without card_image and archived (set by controller)
    $this->assertDatabaseHas('rewards_catalog', [
        'card_name' => 'Test Reward',
        'card_description' => 'A test reward description', 
        'points_amount' => 100,
        'archived' => false,
    ]);
});

test('admin can create a reward with image', function () {
    $admin = adminForUsers();

    $image = UploadedFile::fake()->image('reward.jpg', 600, 400);

    $rewardData = [
        'card_name' => 'Test Reward with Image',
        'card_description' => 'A test reward description',
        'points_amount' => 150,
        'card_image' => $image,
    ];

    $response = $this->actingAs($admin)
        ->post(route('admin.rewards.store'), $rewardData);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('rewards_catalog', [
        'card_name' => 'Test Reward with Image',
        'card_description' => 'A test reward description',
        'points_amount' => 150,
        'archived' => false,
    ]);

    // Verify image was stored
    $reward = Reward::where('card_name', 'Test Reward with Image')->first();
    expect($reward->card_image)->not()->toBeNull();
});

test('create reward validation fails with missing required fields', function () {
    $admin = adminForUsers();

    $response = $this->actingAs($admin)
        ->post(route('admin.rewards.store'), []);

    $response->assertSessionHasErrors(['card_name', 'points_amount']);
});

test('create reward validation fails with invalid points cost', function () {
    $admin = adminForUsers();

    $response = $this->actingAs($admin)
        ->post(route('admin.rewards.store'), [
            'card_name' => 'Test Reward',
            'points_amount' => -10,
        ]);

    $response->assertSessionHasErrors(['points_amount']);
});

test('create reward validation fails with duplicate name', function () {
    $admin = adminForUsers();
    $existingReward = Reward::factory()->create(['card_name' => 'Unique Reward']);

    $response = $this->actingAs($admin)
        ->post(route('admin.rewards.store'), [
            'card_name' => 'Unique Reward',
            'points_amount' => 100,
        ]);

    $response->assertSessionHasErrors(['card_name']);
});

test('create reward validation fails with invalid image file', function () {
    $admin = adminForUsers();

    $response = $this->actingAs($admin)
        ->post(route('admin.rewards.store'), [
            'card_name' => 'Test Reward',
            'points_amount' => 100,
            'card_image' => 'not-an-image.txt', // Invalid file type
        ]);

    $response->assertSessionHasErrors(['card_image']);
});