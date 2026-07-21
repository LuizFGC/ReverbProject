<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected PresenceService $presenceService
    ) {}

    public function attempt(string $email, string $password, bool $remember = false): bool
    {
        $user = $this->userRepository->findByEmail($email);

        if (! $user || ! Hash::check($password, $user->password)) {
            return false;
        }

        Auth::login($user, $remember);

        request()->session()->regenerate();

        //Presenca ainda nao esta funcionnando
        $this->presenceService->update(
            $user->sid,
            'online'
        );

        return true;
    }


    public function logout()
    {
        $user = Auth::user();

        if ($user) {
            $this->presenceService->update(
                $user->sid,
                'offline'
            );
        }

        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }


    public function redirectPathForCurrentUser(): string
    {
        $role = Auth::user()?->role;

        return $role === 'agent' || $role === 'gerente'
            ? '/dashboard'
            : '/portal';
    }
}
