<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Helpers\ReCAPTCHA;
use App\Helpers\Options;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $options = $this->options;
        if ($options->get('enable_register') != 'Y') {
            abort(404);
        }
        
        return view('main.auth.register', ['useRecaptcha' => true]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $options = $this->options;
        if ($options->get('enable_register') != 'Y') {
            abort(404);
        }

        $request->validate([
            'username' => ['required', 'string', 'min:5', 'max:24', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:8', 'max:32'],
            'g-recaptcha-response' => ['required'],
        ]);

        $reCAPTCHA = new ReCAPTCHA($request->{'g-recaptcha-response'});

        if (!$reCAPTCHA->verify()) {
            return redirect()->back()->with('recaptchaInvalid', __('reCAPTCHA is invalid'));
        }

        $defaultGroup = Group::where('is_admin', 'N')
            ->first();

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'group_id' => $this->options->get('default_group', $defaultGroup->id),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
