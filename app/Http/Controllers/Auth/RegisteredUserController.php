<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
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
            'email' => 'required|numeric|unique:'.User::class,
        ]);

        $referer = request('referer');

        $invitation = null;
        if($referer) {
            $campaign = Campaign::firstWhere('slug', $referer);

            if(!$campaign || $campaign->end_date < now()) {
                return redirect()->back()->withErrors([
                    'referer' => 'Campaña expirada.',
                ]);
            }
        }

        if(!$referer) {
            $invitation = Invitation::firstWhere('email', $request->email);

            if(!$invitation) {
                return redirect()->back()->withErrors([
                    'email' => 'No se ha encontrado ninguna invitación para este número de teléfono.',
                ]);
            }
        }



        $user = User::create(array_filter([
            'name' => $invitation->name ?? 'user-'.Str::random(5),
            'email' => $request->email,
            'daily_points' => $invitation?->daily_points ?? null,
            'sugars' => $invitation?->sugars ?? null,
            'proteins' => $invitation?->proteins ?? null,
            'fats' => $invitation?->fats ?? null,
            'weekly_points' => $invitation?->weekly_points ?? null,
            'password' => Hash::make(Str::password(8)),
            'dietician_id' => $invitation?->dietician_id ?? 1,
            'campaign_id' => $campaign?->id ?? null,
        ]));

        event(new Registered($user));

        Auth::login($user, true);

        if($invitation) {
            $invitation->delete();
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
