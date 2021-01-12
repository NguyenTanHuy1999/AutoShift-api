<?php

namespace App\Http\Middleware;

use App\Api\Repositories\Contracts\UserRepository;
use Closure;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Validate password.
        if (empty($request->header('secret_key')) || $request->header('secret_key') != 'admin@BaByKey2017') {
            return response('Unauthenticated request.', 401);
        }

        return $next($request);
    }
}
