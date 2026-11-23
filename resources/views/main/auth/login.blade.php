<x-main.layouts.app>
    <x-slot:pageTitle>{{ __('Login') }}</x-slot>

    <div class="container">
        <div class="row g-2 justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">

                @if (session('status'))
                    <div class="alert alert-success mb-2">{{ session('status') }}</div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <h1>{{ __('Login') }}</h1>

                        <form action="{{ route('login') }}" method="POST">
                            @csrf

                            <x-main.forms.input-text name="email" :label="__('Email') . ' / ' . __('Username')" />
                            <x-main.forms.password name="password" :label="__('Password')" />

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="remember" id="checkboxRemember" class="form-check-input" @checked(old() ? old('remember') : true)>
                                    <label for="checkboxRemember" class="form-check-label">{{ __('Remember me') }}</label>
                                </div>
                            </div>

                            @if (Route::has('password.request'))
                                <div class="mb-3 text-end">
                                    <a href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
                                </div>
                            @endif

                            <div class="mb-3 d-grid">
                                <button type="submit" class="btn btn-primary">{{ __('Login') }}</button>
                            </div>
                        </form>

                        @if (config('services.google.enable_google_login'))
                            <div class="mb-3 tw:flex tw:items-center tw:gap-2">
                                <div class="tw:w-full hr-color"></div>
                                <div class="tw:whitespace-nowrap">{{ __('or') }}</div>
                                <div class="tw:w-full hr-color"></div>
                            </div>

                            <div class="mb-3 tw:flex tw:items-center tw:justify-center tw:gap-2">
                                <a href="{{ route('auth-google') }}" class="btn btn-light border-dark tw:w-full tw:flex tw:items-center tw:justify-center tw:gap-2">
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" xmlns:xlink="http://www.w3.org/1999/xlink" class="tw:w-4 tw:h-4">
                                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                                        <path fill="none" d="M0 0h48v48H0z"></path>
                                    </svg>
                                    <div>{{ __('Sign in with Google') }}</div>
                                </a>
                            </div>
                        @endif

                        @if ($options->get('enable_register') == 'Y')
                            <p class="mb-0">
                                {{ __('Don\'t have an account?') }} <a href="{{ route('register') }}">{{ __('Register') }}</a>
                            </p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-main.layouts.app>