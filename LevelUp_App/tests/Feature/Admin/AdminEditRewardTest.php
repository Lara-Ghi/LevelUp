<?php

use App\Models\Reward;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

test('admin can edit a reward without changing image', function () {
    $admin = adminForUsers();
    $reward = Reward::factory()->create();

    $updatedData = [
        'card_name' => 'Updated Reward Name',
        'card_description' => 'Updated description',
        'points_amount' => 150,
        // Remove card_image since it's nullable and causes validation errors
    ];

    $response = $this->actingAs($admin)
        ->put(route('admin.rewards.update', $reward), $updatedData);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('rewards_catalog', [
        'id' => $reward->id,
        'card_name' => 'Updated Reward Name',
        'card_description' => 'Updated description',
        'points_amount' => 150,
    ]);
});

test('admin can edit a reward with new image', function () {
    $admin = adminForUsers();
    $reward = Reward::factory()->create();

    $image = UploadedFile::fake()->image('updated-image.jpg', 600, 400);

    $updatedData = [
        'card_name' => 'Updated Reward Name',
        'card_description' => 'Updated description',
        'points_amount' => 150,
        'card_image' => $image,
    ];

    $response = $this->actingAs($admin)
        ->put(route('admin.rewards.update', $reward), $updatedData);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('rewards_catalog', [
        'id' => $reward->id,
        'card_name' => 'Updated Reward Name',
        'card_description' => 'Updated description',
        'points_amount' => 150,
    ]);

    // Verify image was updated
    $reward->refresh();
    expect($reward->card_image)->not()->toBeNull();
});

test('edit reward validation fails with invalid data', function () {
    $admin = adminForUsers();
    $reward = Reward::factory()->create();

    $response = $this->actingAs($admin)
        ->put(route('admin.rewards.update', $reward), [
            'card_name' => '',
            'points_amount' => -50,
        ]);

    $response->assertSessionHasErrors(['card_name', 'points_amount']);
});

test('admin cannot edit reward name to existing name', function () {
    $admin = adminForUsers();
    $reward1 = Reward::factory()->create(['card_name' => 'First Reward']);
    $reward2 = Reward::factory()->create(['card_name' => 'Second Reward']);

    $response = $this->actingAs($admin)
        ->put(route('admin.rewards.update', $reward2), [
            'card_name' => 'First Reward',
            'points_amount' => 100,
        ]);

    $response->assertSessionHasErrors(['card_name']);
});

test('edit validation fails with invalid image file', function () {
    $admin = adminForUsers();
    $reward = Reward::factory()->create();

    $response = $this->actingAs($admin)
        ->put(route('admin.rewards.update', $reward), [
            'card_name' => 'Valid Name',
            'points_amount' => 100,
            'card_image' => 'not-an-image.txt', // Invalid file type
        ]);

    $response->assertSessionHasErrors(['card_image']);
});

test('edit validation fails with invalid reward id', function () {
    $admin = adminForUsers();

    $response = $this->actingAs($admin)
        ->put(route('admin.rewards.update', 99999), [
            'card_name' => 'Test',
            'points_amount' => 100,
        ]);

    $response->assertNotFound();
});