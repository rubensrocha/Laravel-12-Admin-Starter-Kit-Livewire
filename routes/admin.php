<?php

use App\Http\Controllers\Admin\Auth\VerifyEmailController;
use App\Livewire\Admin\Auth\ConfirmPassword;
use App\Livewire\Admin\Auth\ForgotPassword;
use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Auth\Register;
use App\Livewire\Admin\Auth\ResetPassword;
use App\Livewire\Admin\Auth\VerifyEmail;
use App\Livewire\Admin\Settings\Appearance;
use App\Livewire\Admin\Settings\Password;
use App\Livewire\Admin\Settings\Profile;
use Illuminate\Support\Facades\Route;


Route::middleware(['guest:admin'])->group(function () {
    Route::get('login', Login::class)->name('login');
    Route::get('register', Register::class)->name('register');
    Route::get('forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::middleware(['auth:admin'])->group(function () {
    // Auth
    Route::post('logout', App\Livewire\Admin\Actions\Logout::class)
        ->name('logout');
    Route::get('verify-email', VerifyEmail::class)
        ->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::get('confirm-password', ConfirmPassword::class)
        ->name('password.confirm');

    // General Routes
    Route::view('/', 'admin.index')
        ->middleware(['auth', 'verified'])
        ->name('index');

    // Profile Routes
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->middleware('admin.password.confirm')->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});
