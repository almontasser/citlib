<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActiveUser
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    /** @var User $user */
    $user = Auth::user();
    if (!$user || !($user->isAdmin() || $user->isUser())) {
      return redirect()->route('home');
    }

    return $next($request);
  }
}