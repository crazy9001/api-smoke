<?php
/**
 * Created by PhpStorm.
 * User: Toinn
 * Date: 7/25/2018
 * Time: 3:45 PM
 */

namespace Vtv\Auth\Http\Middleware;

use Closure;
use Auth;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;

class RoleAuthenticate extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role, $permission)
    {
        if (! $token = $this->auth->setRequest($request)->getToken()) {
            //return $this->respond('tymon.jwt.absent', 'token_not_provided', 400);
            return $this->sendError('tymon.jwt.absent', 'token_not_provided', 400);
        }

        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            return $this->sendError('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
        } catch (JWTException $e) {
            return $this->sendError('tymon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
        }

        if (! $user) {
            return $this->sendError('tymon.jwt.user_not_found', 'user_not_found', 404);
        }

        $roles = is_array($role) ? $role : explode('|', $role);
        if (!$user->hasAnyRole($roles)) {
            return $this->sendError('tymon.jwt.invalid', 'You are don\'t permission', 405, 'Unauthorized');
        }

        $permissions = is_array($permission) ? $permission : explode('|', $permission);
        foreach ($permissions as $permission) {
            if ($user->hasPermissionTo($permission)) {
                $this->events->fire('tymon.jwt.valid', $user);
                return $next($request);
            }
            return $this->sendError('tymon.jwt.invalid', 'You are don\'t permission', 405, 'Unauthorized');
        }


    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

}