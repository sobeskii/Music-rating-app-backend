<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    private const DEFAULT_IMG = 'https://ui-avatars.com/api/?size=128?&length=1&background=random';

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @throws AuthenticationException
     */
    public function handleUserLogin($spotifyUser)
    {
        $user = User::where('spotify_id', $spotifyUser->id)->first();

        if ($user) {
            $user->update([
                'spotify_token' => $spotifyUser->token,
                'spotify_refresh_token' => $spotifyUser->refreshToken,
                'spotify_avatar' => ($spotifyUser->avatar != null) ? $spotifyUser->avatar : self::DEFAULT_IMG.'&'.'name='.$spotifyUser->name,
            ]);
            $this->userService->handleUserTopArtists($user);
        } else {
            $user = User::create([
                'name' => $spotifyUser->name,
                'spotify_id' => $spotifyUser->id,
                'spotify_token' => $spotifyUser->token,
                'spotify_refresh_token' => $spotifyUser->refreshToken,
                'spotify_avatar' => ($spotifyUser->avatar != null) ? $spotifyUser->avatar : self::DEFAULT_IMG.'&'.'name='.$spotifyUser->name,
                'spotify_token_expiresin' => Carbon::now()->addSeconds($spotifyUser->expiresIn),
                'role_id' => 1
            ]);

            $this->userService->handleUserTopArtists($user);
        }

        $userRole = $user->role;
        if($userRole->role == 'banned'){
            throw new AuthenticationException('User is banned!');
        }

        if ($userRole) {
            $this->scope = $userRole->role;
        }

        return $user->createToken($user->email . '-' . now(), [$this->scope]);
    }

    public function logout() {
        auth()->user()->topArtists()->delete();
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
    }
}
