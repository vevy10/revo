<?php

use App\Enums\UserRole;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use App\Notifications\UserInvitationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    foreach (UserRole::seedData() as $role) {
        Role::query()->create($role);
    }
});

function userWithRole(UserRole $role): User
{
    return User::factory()->create([
        'role_id' => Role::query()->where('code', $role->value)->firstOrFail()->id,
    ]);
}

test('only admins can invite users', function () {
    Notification::fake();

    Sanctum::actingAs(userWithRole(UserRole::Manager));

    $this->postJson('/api/invitations', [
        'email' => 'cashier@example.test',
        'role' => UserRole::Cashier->value,
    ])->assertForbidden();

    expect(User::query()->where('email', 'cashier@example.test')->exists())->toBeFalse();
});

test('admin creates a pending user and sends an invitation', function () {
    Notification::fake();

    Sanctum::actingAs(userWithRole(UserRole::Admin));

    $this->postJson('/api/invitations', [
        'email' => 'cashier@example.test',
        'role' => UserRole::Cashier->value,
    ])->assertCreated()
        ->assertJsonPath('invitation.email', 'cashier@example.test')
        ->assertJsonPath('invitation.role.code', UserRole::Cashier->value);

    $user = User::query()->where('email', 'cashier@example.test')->firstOrFail();

    expect($user->activated_at)->toBeNull()
        ->and($user->password)->toBeNull()
        ->and($user->role->code)->toBe(UserRole::Cashier->value);

    $invitation = Invitation::query()->where('email', 'cashier@example.test')->firstOrFail();

    expect($invitation->expires_at->equalTo($invitation->created_at->addHours(48)))->toBeTrue();

    Notification::assertSentTo($user, UserInvitationNotification::class);
});

test('invited user activates account and receives an api token', function () {
    $role = Role::query()->where('code', UserRole::Cashier->value)->firstOrFail();
    $user = User::query()->create([
        'email' => 'cashier@example.test',
        'role_id' => $role->id,
    ]);
    $plainToken = 'valid-invitation-token';

    Invitation::query()->create([
        'user_id' => $user->id,
        'role_id' => $role->id,
        'email' => $user->email,
        'token' => hash('sha256', $plainToken),
        'expires_at' => now()->addDay(),
    ]);

    $this->postJson("/api/invitations/{$plainToken}/activate", [
        'first_name' => 'Ada',
        'last_name' => 'Lovelace',
        'password' => 'Password1234',
        'password_confirmation' => 'Password1234',
    ])->assertOk()
        ->assertJsonPath('user.first_name', 'Ada')
        ->assertJsonPath('user.role.code', UserRole::Cashier->value)
        ->assertJsonStructure(['token']);

    $user->refresh();

    expect($user->activated_at)->not->toBeNull()
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($user->invitations()->first()->accepted_at)->not->toBeNull();
});

test('invitation token can only be used once', function () {
    $role = Role::query()->where('code', UserRole::Cashier->value)->firstOrFail();
    $user = User::query()->create([
        'email' => 'single-use@example.test',
        'role_id' => $role->id,
    ]);
    $plainToken = 'single-use-invitation-token';

    Invitation::query()->create([
        'user_id' => $user->id,
        'role_id' => $role->id,
        'email' => $user->email,
        'token' => hash('sha256', $plainToken),
        'expires_at' => now()->addHours(48),
    ]);

    $payload = [
        'first_name' => 'Marie',
        'last_name' => 'Curie',
        'password' => 'Password1234',
        'password_confirmation' => 'Password1234',
    ];

    $this->postJson("/api/invitations/{$plainToken}/activate", $payload)->assertOk();

    $this->postJson("/api/invitations/{$plainToken}/activate", $payload)
        ->assertConflict()
        ->assertJsonPath('message', 'Cette invitation a déjà été utilisée.');

    $this->getJson("/api/invitations/{$plainToken}")
        ->assertConflict()
        ->assertJsonPath('message', 'Cette invitation a déjà été utilisée.');
});

test('expired invitation cannot activate an account', function () {
    $role = Role::query()->where('code', UserRole::Cashier->value)->firstOrFail();
    $user = User::query()->create([
        'email' => 'expired@example.test',
        'role_id' => $role->id,
    ]);
    $plainToken = 'expired-invitation-token';

    Invitation::query()->create([
        'user_id' => $user->id,
        'role_id' => $role->id,
        'email' => $user->email,
        'token' => hash('sha256', $plainToken),
        'expires_at' => now()->subMinute(),
    ]);

    $this->postJson("/api/invitations/{$plainToken}/activate", [
        'first_name' => 'Grace',
        'last_name' => 'Hopper',
        'password' => 'Password1234',
        'password_confirmation' => 'Password1234',
    ])->assertGone();
});

test('active user can login and access profile', function () {
    $user = userWithRole(UserRole::Cashier);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertOk()
        ->assertJsonPath('user.role.code', UserRole::Cashier->value)
        ->assertJsonStructure(['token']);

    $this->withHeader('Authorization', 'Bearer '.$response->json('token'))
        ->getJson('/api/auth/me')
        ->assertOk()
        ->assertJsonPath('user.email', $user->email);
});
