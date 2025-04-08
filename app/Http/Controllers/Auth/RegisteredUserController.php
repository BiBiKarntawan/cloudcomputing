<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DynamoUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use function Laravel\Prompts\alert;
use Illuminate\Validation\ValidationException;


class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        if (DynamoUser::findByEmail($request->email)) {
            throw ValidationException::withMessages([
                'email' => 'This email is already registered.',
            ]);
        }
        $user = DynamoUser::createItem([
            'user_name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);
        //Auth::login($user);
        return to_route('login');
    }
}
