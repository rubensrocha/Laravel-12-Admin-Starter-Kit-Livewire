<?php

use App\Livewire\Admin\Settings\Password;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('password can be updated', function () {
    $user = Admin::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user, 'admin');

    $response = Livewire::test(Password::class)
        ->set('current_password', 'password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword');

    $response->assertHasNoErrors();

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $user = Admin::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->actingAs($user, 'admin');

    $response = Livewire::test(Password::class)
        ->set('current_password', 'wrong-password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password')
        ->call('updatePassword');

    $response->assertHasErrors(['current_password']);
});
