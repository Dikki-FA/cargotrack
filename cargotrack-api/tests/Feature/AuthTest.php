<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('dapat login dan menerima token', function () {
    $user = User::factory()->admin()->create([
        'email' => 'tester-login@cargotrack.test',
        'password' => 'password',
        ]);
        
    $response = $this->postJson('/api/v1/auth/login', [
        'email' => 'tester-login@cargotrack.test',
        'password' => 'password',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'data' => ['token', 'token_type', 'user'],
            'message',
        ]);
});

it('menolak login dengan password salah', function () {
    User::factory()->admin()->create([
        'email' => 'tester-wrong@cargotrack.test',
        'password' => bcrypt('password'),
    ]);

    $this->postJson('/api/v1/auth/login', [
        'email' => 'tester-wrong@cargotrack.test',
        'password' => 'salah',
    ])->assertUnauthorized();
});