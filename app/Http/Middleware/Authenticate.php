<?php

namespace App\Http\Middleware;

use App\Response\ErrorCode;
use App\Response\MyResponse;
use App\Response\ResponseStatus;
use App\Response\ResponseStatusCode;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            return MyResponse::generateJson(
                ResponseStatus::UNAUTHORIZED,
                null,
                ErrorCode::UNAUTHORIZED,
                ResponseStatusCode::UNAUTHORIZED
            );
        }

        return $next($request);
    }
}
