<?php

namespace App\Livewire\Admin\Auth;

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.auth')]
class VerifyEmail extends Component
{
    /**
     * Check if the user has verified their email before rendering the component.
     */
    public function rendering()
    {
        if(Auth::guard('admin')->user()->hasVerifiedEmail()) {
            return $this->redirect(route('admin.index'), navigate: true);
        }
    }

    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::guard('admin')->user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::guard('admin')->user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect(route('admin.index'), navigate: true);
    }
}
