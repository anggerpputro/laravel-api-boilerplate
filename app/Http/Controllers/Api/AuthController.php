<?php
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Gemboot\Controllers\CoreRestController as GembootController;

use Spatie\Permission\Models\Role;
use App\User;

/**
 * @group Authentication
 *
 * APIs for authentication
 */
class AuthController extends GembootController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', [
            'except' => [
                'register',
                'login'
            ]
        ]);
    }

    /**
     * Registering user.
     *
     * @bodyParam name string required
     * @bodyParam username string required
     * @bodyParam password string required
     * @response {
     *  "access_token": "token",
     *  "token_type": "Bearer",
     *  "expires_in": 60,
     * }
     * @return \Illuminate\Http\JsonResponse
     */
    public function register()
    {
        $request = request(['name', 'username', 'password']);
        $validator = \Validator::make($request, [
            'name' => 'required|string',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->responseBadRequest(['error'=>$validator->errors()]);
        }

        \DB::beginTransaction();
        try {
            $user = User::create($request);

            // default role = opd
            $role = Role::findByName("opd", "api");
            $user->assignRole($role);

            \DB::commit();

            return $this->respondWithToken(auth()->tokenById($user->id), [
                'user' => $user,
                'roles' => $user->getRoleNames(),
            ]);
        } catch (\Exception $e) {
            return $this->responseError([
                'error' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @bodyParam username string required
     * @bodyParam password string required
     * @response {
     *  "access_token": "token",
     *  "token_type": "Bearer",
     *  "expires_in": 60,
     * }
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['username', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return $this->responseBadRequest([], "Username and password did not match!");
        }

        return $this->respondWithToken($token, [
            'user' => auth()->user(),
            'roles' => auth()->user()->getRoleNames(),
        ]);
    }

    /**
     * Get the authenticated User.
     *
     * @authenticated
     * @response {
     *  "name": "User Name",
     *  "username": "User Username",
     * }
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->responseSuccess([
            'user' => auth()->user(),
            'roles' => auth()->user()->getRoleNames(),
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @authenticated
     * @response {
     *  "message": "Successfully logged out",
     * }
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @authenticated
     * @response {
     *  "access_token": "token",
     *  "token_type": "Bearer",
     *  "expires_in": 60,
     * }
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, array $additional_data = [])
    {
        $response = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];

        return response()->json(array_merge($response, $additional_data));
    }
}
