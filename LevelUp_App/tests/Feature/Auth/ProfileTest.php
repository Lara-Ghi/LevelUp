<?php

use App\Models\User;

test('authenticated user can access their profile page', function () {
    // Arrange: Create and authenticate a user
    $user = User::factory()->create([
        'name' => 'John',
        'surname' => 'Doe',
        'username' => 'johndoe',
        'date_of_birth' => '1990-01-01',
        'sitting_position' => 70,
        'standing_position' => 120,
    ]);
    
    $this->actingAs($user);

    // Act: Access profile page
    $response = $this->get(route('profile'));

    // Assert: Should load successfully
    $response->assertOk();
    $response->assertViewIs('profile');
    
    // Assert: Should have user data in view
    $response->assertViewHas('user');
    
    // Assert: Page should display user information
    $response->assertSee('John');
    $response->assertSee('Doe');
    $response->assertSee('johndoe');
});

test('profile page loads correct user data', function () {
    // Arrange: Create users with different data
    $user1 = User::factory()->create([
        'name' => 'Alice',
        'surname' => 'Smith',
        'username' => 'alice123',
        'total_points' => 150,
    ]);
    
    $user2 = User::factory()->create([
        'name' => 'Bob',
        'surname' => 'Johnson', 
        'username' => 'bob456',
        'total_points' => 200,
    ]);

    // Test user1's profile
    $this->actingAs($user1);
    $response = $this->get(route('profile'));
    
    $response->assertOk();
    $response->assertSee('Alice');
    $response->assertSee('Smith');
    $response->assertSee('alice123');
    $response->assertDontSee('Bob');
    $response->assertDontSee('Johnson');

    // Test user2's profile
    $this->actingAs($user2);
    $response = $this->get(route('profile'));
    
    $response->assertOk();
    $response->assertSee('Bob');
    $response->assertSee('Johnson');
    $response->assertSee('bob456');
    $response->assertDontSee('Alice');
    $response->assertDontSee('Smith');
});

test('guests cannot access profile page', function () {
    // Act: Try to access profile page as guest
    $response = $this->get(route('profile'));

    // Assert: Should be redirected to login
    $response->assertRedirect(route('login'));
    $this->assertGuest();
});
