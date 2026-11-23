<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Socialite;
use App\Models\User;
use App\Models\UserMeta;
use App\Models\Group;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class GoogleController extends Controller
{
    public function redirect()
    {
        if (config('services.google.enable_google_login') != true) {
            abort(404);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        if (config('services.google.enable_google_login') != true) {
            abort(404);
        }

        $googleUser = Socialite::driver('google')->user();
        
        $googleUserEmail = $googleUser->email;
        $googleUserID = $googleUser->id;
        $googleUserAvatar = $googleUser->avatar;

        $user = User::where('email', $googleUserEmail)->first();
        if ($user) {
            UserMeta::updateOrCreate(
                ['user_id' => $user->id, 'key' => 'google_id'],
                ['value' => $googleUserID]
            );

            if (empty($user->email_verified_at)) {
                $user->update(['email_verified_at' => now()]);
            }

            Auth::login($user);
        }
        else {
            $options = $this->options;
            if ($options->get('enable_register') != 'Y') {
                abort(404);
            }

            $newUserUsername = explode('@', $googleUserEmail)[0];

            // Make sure the username is unique, if not, add number suffix until it is unique
            $suffix = 1;
            $newUserUsernameUnique = $newUserUsername;
            while (User::where('username', $newUserUsernameUnique)->exists()) {
                $newUserUsernameUnique = $newUserUsername . $suffix;
                $suffix++;
            }

            // Download the avatar and store it in S3
            $currentTimestamp = time();
            $filenameAndPath = 'avatars/' . $newUserUsernameUnique . '-' . $currentTimestamp . '.jpg';
            $avatarContents = file_get_contents($googleUserAvatar);
            Storage::disk('s3')->put($filenameAndPath, $avatarContents, 'public');
            $finalPath = Storage::disk('s3')->url($filenameAndPath);

            $newUser = User::create([
                'username'              => $newUserUsernameUnique,
                'avatar'                => $finalPath,
                'avatar_path'           => $filenameAndPath,
                'email'                 => $googleUserEmail,
                'email_verified_at'     => now(),
                'password'              => Hash::make(str()->random(8)),
                'group_id'              => $this->options->get('default_group', Group::where('is_admin', 'N')->first()->id),
            ]);

            UserMeta::create([
                'user_id'   => $newUser->id,
                'key'       => 'google_id',
                'value'     => $googleUserID,
            ]);

            Auth::login($newUser);
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
