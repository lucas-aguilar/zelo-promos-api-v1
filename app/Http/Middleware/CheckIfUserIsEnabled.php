<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfUserIsEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth('api')->user();
        if($user->enabled != 1){
            $response = [
                'success' => false,
                'message' => 'Esse usuário foi desativado. Por favor contate o suporte.',
            ];
            return response()->json($response, 404);
        }
        return $next($request);
    }
}
