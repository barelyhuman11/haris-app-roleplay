<x-main.layouts.app :use-recaptcha="true">
    <x-slot:pageTitle>{{ __('Register') }}</x-slot>

    <div class="container">
        <div class="row g-2 justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">

                @if (session('recaptchaInvalid'))
                    <div class="alert alert-danger mb-2">{{ session('recaptchaInvalid') }}</div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <h1>{{ __('Register') }}</h1>

                        <form action="{{ route('register') }}" method="POST">
                            @csrf

                            <x-main.forms.input-text name="username" :label="__('Username')" />
                            <x-main.forms.input-text name="email" :label="__('Email')" />
                            <x-main.forms.password name="password" :label="__('Password')" />

                            <div class="mb-3">
                                <x-main.forms.recaptcha size="normal" />
                            </div>

                            <div class="mb-3 d-grid">
                                <button type="submit" class="btn btn-primary">{{ __('Register') }}</button>
                            </div>
                        </form>

                        @if (config('services.google.enable_google_login'))
                            <div class="mb-3 tw:flex tw:items-center tw:gap-4">
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
                                    <div>{{ __('Sign up with Google') }}</div>
                                </a>
                            </div>
                        @endif

                        <p class="mb-0">{{ __('Already registered?') }} <a href="{{ route('login') }}">{{ __('Login') }}</a></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-main.layouts.app>