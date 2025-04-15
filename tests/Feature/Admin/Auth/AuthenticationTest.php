<?php

use App\Livewire\Admin\Auth\Login;
use App\Models\Admin;
use Livewire\Livewire;

test('login screen can be rendered', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200);
});

test('admins can authenticate using the login screen', function () {
    $user = Admin::factory()->create();

    $response = Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.index', absolute: false));

    $this->assertAuthenticated('admin');
});

test('admins can not authenticate with invalid password', function () {
    $user = Admin::factory()->create();

    $response = Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'wrong-password')
        ->call('login');

    $response->assertHasErrors('email');

    $this->assertGuest('admin');
});

test('admins can logout', function () {
    $user = Admin::factory()->create();

    $response = $this->actingAs($user)->post('/admin/logout');

    $response->assertRedirect('/admin/login');

    $this->assertGuest('admin');
});
