<?php

use App\Models\Admin;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

test('email verification screen can be rendered', function () {
    $user = Admin::factory()->unverified()->create();

    $response = $this->actingAs($user, 'admin')->get('/admin/verify-email');

    $response->assertStatus(200);
});

test('email can be verified', function () {
    $user = Admin::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'admin.verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user, 'admin')->get($verificationUrl);

    Event::assertDispatched(Verified::class);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('admin.index', absolute: false).'?verified=1');
});

test('email is not verified with invalid hash', function () {
    $user = Admin::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'admin.verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user, 'admin')->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});
