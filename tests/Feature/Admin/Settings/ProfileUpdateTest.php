<?php

use App\Livewire\Admin\Settings\Profile;
use App\Models\Admin;
use Livewire\Livewire;

test('profile page is displayed', function () {
    $this->actingAs(Admin::factory()->create(), 'admin');

    $this->get('/admin/settings/profile')->assertOk();
});

test('profile information can be updated', function () {
    $user = Admin::factory()->create();

    $this->actingAs($user, 'admin');

    $response = Livewire::test(Profile::class)
        ->set('name', 'Test Admin')
        ->set('email', 'test@example.com')
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toEqual('Test Admin');
    expect($user->email)->toEqual('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when email address is unchanged', function () {
    $user = Admin::factory()->create();

    $this->actingAs($user, 'admin');

    $response = Livewire::test(Profile::class)
        ->set('name', 'Test Admin')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $response->assertHasNoErrors();

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = Admin::factory()->create();

    $this->actingAs($user, 'admin');

    $response = Livewire::test('admin.settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/admin/login');

    expect($user->fresh())->toBeNull();
    expect(auth('admin')->check())->toBeFalse();
});

test('correct password must be provided to delete account', function () {
    $user = Admin::factory()->create();

    $this->actingAs($user, 'admin');

    $response = Livewire::test('admin.settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
});
