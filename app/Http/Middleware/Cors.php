<?php

namespace app\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Create a new middleware instance.
     *
     * @param \Illuminate\Contracts\Auth\Factory $auth
     */
    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, HEAD, POST, PUT, DELETE', 'OPTIONS',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age' => '60',
            'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept, Authorization',
        ];

        if ($request->isMethod('OPTIONS')) {
            return response(null, 200, $headers);
        }

        $response = $next($request);
        $class = get_class($response);
        if ($class == "Symfony\Component\HttpFoundation\RedirectResponse" || $class == 'Symfony\Component\HttpFoundation\StreamedResponse') {
            // Do Nothing
        } else {
            foreach ($headers as $key => $value) {
                $response->header($key, $value);
            }

            $response->header('charset', 'utf-8');
        }


        return $response;
    }
}
