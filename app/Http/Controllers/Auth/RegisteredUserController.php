<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|numeric|digits:9|unique:'.User::class,
        ]);

        $invitation = Invitation::firstWhere('email', $request->email);

        if(!$invitation) {
            return redirect()->back()->withErrors([
                'email' => 'No se ha encontrado ninguna invitación para este número de teléfono.',
            ]);
        }

        $user = User::create([
            'name' => $invitation->name,
            'email' => $request->email,
            'daily_points' => $invitation->daily_points,
            'sugars' => $invitation->sugars,
            'proteins' => $invitation->proteins,
            'fats' => $invitation->fats,
            'weekly_points' => $invitation->weekly_points,
            'password' => Hash::make(Str::password(8)),
            'dietician_id' => $invitation->dietician_id ?? '',
        ]);

        event(new Registered($user));

        Auth::login($user);

        $invitation->delete();

        return redirect(RouteServiceProvider::HOME);
    }
}
