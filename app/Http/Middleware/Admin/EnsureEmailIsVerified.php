<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified as BaseEnsureEmailIsVerified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class EnsureEmailIsVerified extends BaseEnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|null
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        // Routes for admin and user, based on middleware group name
        $default_redirect_route = in_array('auth:admin', $request->route()->middleware(), false) ? 'admin.verification.notice' : 'verification.notice';
        $default_guard = in_array('auth:admin', $request->route()->middleware(), false) ? 'admin' : '';

        if (! $request->user($default_guard) ||
            ($request->user($default_guard) instanceof MustVerifyEmail &&
                ! $request->user($default_guard)->hasVerifiedEmail())) {
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : Redirect::guest(URL::route($redirectToRoute ?: $default_redirect_route));
        }

        return $next($request);
    }
}
