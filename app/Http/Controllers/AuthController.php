<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    /**
     * @var AuthService
     */
    private $authService;

    /**
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->middleware(['json']);
        $this->authService = $authService;
    }

    /**
     * @return JsonResponse|RedirectResponse
     * @throws AuthenticationException
     */
    public function login()
    {
        try {
            $spotifyUser = Socialite::driver('spotify')
                ->stateless()->user();
        } catch (ClientException $exception) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }
        try {
            $token = $this->authService->handleUserLogin($spotifyUser);
        } catch (AuthenticationException $e) {
            $cookie =  Cookie::make('is_banned', true, 12000,'/',null,null,false);
            Cookie::queue($cookie);
            return redirect(config('app.frontend_url'))->withCookie($cookie);
        }

        $cookie =  Cookie::make('AUTH-TOKEN', $token->accessToken, 12000,'/',null,null,false);
        Cookie::queue($cookie);

        return redirect(config('app.frontend_url'))->withCookie($cookie);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json('Logged out successfully');
    }

    /**
     * @return mixed
     */
    public function redirect()
    {
        return Socialite::driver('spotify')->stateless()->redirect();
    }
}
