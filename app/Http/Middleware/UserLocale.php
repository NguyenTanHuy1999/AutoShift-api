<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Api\Entities\User;

class UserLocale
{
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
        // $auth = Auth::user();
        // if(!empty($request->header('LANG'))){
        //     app('translator')->setLocale($request->header('LANG'));
        //     if(!empty($auth)) {
        //        if(empty($auth->language) || ($auth->language && $auth->language != $request->header('LANG'))) {
        //             $user = User::find($auth->id);
        //             if(!empty($user)) {
        //                 $user->language = $request->header('LANG');
        //                 $user->save();
        //             }
        //         }
        //     } 
            
        // } else {
        //     if(!empty($auth)){
        //         $lang = $auth->language;
        //         if(!empty($lang)){
        //             app('translator')->setLocale($lang);
        //         } else {
        //             app('translator')->setLocale(config('app.locale'));
        //         }
        //     } else {
        //         app('translator')->setLocale(config('app.locale'));
        //     }
        // }
        app('translator')->setLocale('vi');
        return $next($request);
    }
}
