<?php

use App\Livewire\Admin\Auth\Register;
use Livewire\Livewire;

test('registration screen can be rendered', function () {
    $response = $this->get('/admin/register');

    $response->assertStatus(200);
});

test('new admins can register', function () {
    $response = Livewire::test(Register::class)
        ->set('name', 'Test Admin')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.index', absolute: false));

    $this->assertAuthenticated('admin');
});
