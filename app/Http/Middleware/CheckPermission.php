<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckPermission
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
        if (Auth::check()) {
            $uri = $request->path();
            $method = $request->method();
            $user = Auth::user();

            $uri_exploded = explode("/", $uri);
            if ($uri_exploded[0] == "api") {
                $check_permission_uri = isset($uri_exploded[1]) ? "/".$uri_exploded[1] : "";
                if (! empty($check_permission_uri)) {
                    $any_permissions = [];
                    switch (strtoupper($method)) {
                        case 'GET':
                        case 'HEAD':
                            $any_permissions = [
                                "access $check_permission_uri",
                                "list $check_permission_uri",
                                "read $check_permission_uri",
                            ];
                            break;
                        case 'POST':
                            $any_permissions = [
                                "create $check_permission_uri",
                            ];
                            break;
                        case 'PUT':
                        case 'PATCH':
                            $any_permissions = [
                                "update $check_permission_uri",
                            ];
                            break;
                        case 'DELETE':
                            $any_permissions = [
                                "delete $check_permission_uri",
                            ];
                            break;
                        default:
                            break;
                    }

                    // dd([
                    //     "role" => $user->getRoleNames(),
                    //     // "permissions" => $user->getAllPermissions(),
                    //     "any_permissions" => $any_permissions
                    // ]);
                    $forbid = false;
                    if (! empty($any_permissions)) {
                        // check user permission
                        if (! $user->hasAnyPermission($any_permissions)) {
                            $forbid = true;
                        }
                    } else {
                        $forbid = true;
                    }

                    if ($forbid) {
                        return response()->json('Forbidden.', 403);
                    }
                }
            }
        }

        return $next($request);
    }
}
