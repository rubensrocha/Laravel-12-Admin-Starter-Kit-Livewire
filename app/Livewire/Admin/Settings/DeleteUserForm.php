<?php

namespace App\Livewire\Admin\Settings;

use App\Livewire\Admin\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class DeleteUserForm extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::guard('admin')->user(), $logout(...))->delete();

        $this->redirect(route('admin.login'), navigate: true);
    }
}
