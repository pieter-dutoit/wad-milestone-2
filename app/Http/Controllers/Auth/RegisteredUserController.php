<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $userCount = User::where('s_number', $request->input('s_number'))->count();

        if ($userCount > 0) {
            // Set password for existing user (created by file upload)
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::where('s_number', $request->input('s_number'))->first();
            $user->update([
                'password' => $request->password
            ]);
            $user->save();

            Auth::login($user);

            return redirect(route('enrolments.index', absolute: false));
        } else {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                's_number' => ['required', 'string', 'min:5', 'max:10', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $learnerRoleId = Role::where('role', 'student')->first()->id;

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                's_number' => $request->s_number,
                'password' => Hash::make($request->password),
                'role_id' => $learnerRoleId,
                'is_activated' => true
            ]);

            event(new Registered($user));

            Auth::login($user);

            return redirect(route('enrolments.index', absolute: false));
        }
    }
}
