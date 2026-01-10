<?php

declare(strict_types=1);

use App\Infrastructure\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Auth API', function () {

    describe('POST /api/v1/login', function () {
        it('logs in with valid credentials', function () {
            $user = User::factory()->create([
                'username' => 'testuser',
                'password' => bcrypt('password123'),
            ]);

            $response = $this->postJson('/api/v1/login', [
                'username' => 'testuser',
                'password' => 'password123',
            ]);

            $response->assertOk()
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => ['uuid', 'name', 'username', 'email'],
                        'token' => ['access_token', 'token_type'],
                    ],
                ])
                ->assertJsonPath('success', true)
                ->assertJsonPath('data.user.username', 'testuser')
                ->assertJsonPath('data.token.token_type', 'Bearer');
        });

        it('fails with invalid username', function () {
            User::factory()->create([
                'username' => 'testuser',
                'password' => bcrypt('password123'),
            ]);

            $response = $this->postJson('/api/v1/login', [
                'username' => 'wronguser',
                'password' => 'password123',
            ]);

            $response->assertUnauthorized()
                ->assertJsonPath('success', false)
                ->assertJsonPath('error_code', 'INVALID_CREDENTIALS');
        });

        it('fails with invalid password', function () {
            User::factory()->create([
                'username' => 'testuser',
                'password' => bcrypt('password123'),
            ]);

            $response = $this->postJson('/api/v1/login', [
                'username' => 'testuser',
                'password' => 'wrongpassword',
            ]);

            $response->assertUnauthorized()
                ->assertJsonPath('success', false)
                ->assertJsonPath('error_code', 'INVALID_CREDENTIALS');
        });

        it('validates required fields', function () {
            $response = $this->postJson('/api/v1/login', []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['username', 'password']);
        });

        it('validates password minimum length', function () {
            $response = $this->postJson('/api/v1/login', [
                'username' => 'testuser',
                'password' => '12345', // less than 6 characters
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
        });
    });

    describe('POST /api/v1/logout', function () {
        it('logs out authenticated user', function () {
            $user = User::factory()->create();
            $token = $user->createToken('test-device')->plainTextToken;

            $response = $this->withHeader('Authorization', "Bearer {$token}")
                ->postJson('/api/v1/logout');

            $response->assertOk()
                ->assertJsonPath('success', true)
                ->assertJsonPath('message', 'Logged out successfully');

            // Verify token is revoked
            expect($user->tokens()->count())->toBe(0);
        });

        it('fails without authentication', function () {
            $response = $this->postJson('/api/v1/logout');

            $response->assertUnauthorized();
        });

        it('fails with invalid token', function () {
            $response = $this->withHeader('Authorization', 'Bearer invalid-token')
                ->postJson('/api/v1/logout');

            $response->assertUnauthorized();
        });
    });

    describe('POST /api/v1/logout-all', function () {
        it('logs out from all devices', function () {
            $user = User::factory()->create();

            // Create multiple tokens (simulating multiple devices)
            $user->createToken('device-1');
            $user->createToken('device-2');
            $user->createToken('device-3');
            $currentToken = $user->createToken('current-device')->plainTextToken;

            expect($user->tokens()->count())->toBe(4);

            $response = $this->withHeader('Authorization', "Bearer {$currentToken}")
                ->postJson('/api/v1/logout-all');

            $response->assertOk()
                ->assertJsonPath('success', true)
                ->assertJsonPath('message', 'Logged out from all devices successfully');

            // Verify all tokens are revoked
            expect($user->fresh()->tokens()->count())->toBe(0);
        });

        it('fails without authentication', function () {
            $response = $this->postJson('/api/v1/logout-all');

            $response->assertUnauthorized();
        });
    });

    describe('GET /api/v1/me', function () {
        it('returns authenticated user data', function () {
            $user = User::factory()->create([
                'name' => 'John Doe',
                'username' => 'johndoe',
                'email' => 'john@example.com',
            ]);
            $token = $user->createToken('test-device')->plainTextToken;

            $response = $this->withHeader('Authorization', "Bearer {$token}")
                ->getJson('/api/v1/me');

            $response->assertOk()
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => ['uuid', 'name', 'username', 'email', 'is_active', 'created_at'],
                ])
                ->assertJsonPath('success', true)
                ->assertJsonPath('data.name', 'John Doe')
                ->assertJsonPath('data.username', 'johndoe')
                ->assertJsonPath('data.email', 'john@example.com');
        });

        it('fails without authentication', function () {
            $response = $this->getJson('/api/v1/me');

            $response->assertUnauthorized();
        });

        it('fails with expired/revoked token', function () {
            $user = User::factory()->create();
            $token = $user->createToken('test-device')->plainTextToken;

            // Revoke the token
            $user->tokens()->delete();

            $response = $this->withHeader('Authorization', "Bearer {$token}")
                ->getJson('/api/v1/me');

            $response->assertUnauthorized();
        });
    });

    describe('Login creates new token and revokes old one for same device', function () {
        it('revokes previous token for same device name on new login', function () {
            $user = User::factory()->create([
                'username' => 'testuser',
                'password' => bcrypt('password123'),
            ]);

            // First login
            $response1 = $this->postJson('/api/v1/login', [
                'username' => 'testuser',
                'password' => 'password123',
                'device_name' => 'mobile',
            ]);

            $response1->assertOk();
            expect($user->tokens()->count())->toBe(1);

            // Second login with same device name
            $response2 = $this->postJson('/api/v1/login', [
                'username' => 'testuser',
                'password' => 'password123',
                'device_name' => 'mobile',
            ]);

            $response2->assertOk();
            // Should still be 1 token (old one revoked, new one created)
            expect($user->fresh()->tokens()->count())->toBe(1);
        });

        it('keeps tokens for different devices', function () {
            $user = User::factory()->create([
                'username' => 'testuser',
                'password' => bcrypt('password123'),
            ]);

            // Login from mobile
            $this->postJson('/api/v1/login', [
                'username' => 'testuser',
                'password' => 'password123',
                'device_name' => 'mobile',
            ])->assertOk();

            // Login from web
            $this->postJson('/api/v1/login', [
                'username' => 'testuser',
                'password' => 'password123',
                'device_name' => 'web',
            ])->assertOk();

            // Should have 2 tokens (one for each device)
            expect($user->fresh()->tokens()->count())->toBe(2);
        });
    });

});
