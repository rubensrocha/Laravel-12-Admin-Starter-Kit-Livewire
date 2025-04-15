<?php

use App\Models\Admin;

test('admin guests are redirected to the login page', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

test('authenticated admins can visit the index', function () {
    $this->actingAs($user = Admin::factory()->create(), 'admin');

    $this->get('/admin')->assertStatus(200);
});
