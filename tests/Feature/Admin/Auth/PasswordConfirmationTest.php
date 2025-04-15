<?php

use App\Livewire\Admin\Auth\ConfirmPassword;
use App\Models\Admin;
use Livewire\Livewire;

test('confirm password screen can be rendered', function () {
    $user = Admin::factory()->create();

    $response = $this->actingAs($user, 'admin')->get('/admin/confirm-password');

    $response->assertStatus(200);
});

test('password can be confirmed', function () {
    $user = Admin::factory()->create();

    $this->actingAs($user, 'admin');

    $response = Livewire::test(ConfirmPassword::class)
        ->set('password', 'password')
        ->call('confirmPassword');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.index', absolute: false));
});

test('password is not confirmed with invalid password', function () {
    $user = Admin::factory()->create();

    $this->actingAs($user, 'admin');

    $response = Livewire::test(ConfirmPassword::class)
        ->set('password', 'wrong-password')
        ->call('confirmPassword');

    $response->assertHasErrors(['password']);
});
