<nav class="mb-2">
    <div class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container">
    
            <a href="{{ route('index') }}" class="navbar-brand">{{ $options->get('site_title', config('app.name')) }}</a>
    
            <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
    
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a 
                            href="{{ route('index') }}" 
                            @class(['nav-link', 'active' => Route::currentRouteName() === 'index'])
                            @if (Route::currentRouteName() === 'index') aria-current="page" @endif
                        >{{ __('Home') }}</a>
                    </li>
                </ul>
    
                <!-- Search bar -->
                @if (Route::currentRouteName() != 'index')
                    <div class="mx-3 flex-fill d-none d-md-block">
                        <form action="{{ route('search') }}" method="GET">
                            <div class="input-group">
                                <input
                                    type="text"
                                    class="form-control"
                                    name="q"
                                    placeholder="{{ __('Find posts') }}"
                                    value="{{ request()->query('q') }}"
                                >
                                <button class="btn btn-primary" type="submit">{{ __('Search') }}</button>
                            </div>
                        </form>
                    </div>
                @endif
                <!-- [END] Search bar -->
    
                <ul class="navbar-nav ms-auto">
                    @guest
                        @if ($options->get('enable_register') == 'Y')
                            <li class="nav-item">
                                <a 
                                    href="{{ route('register') }}" 
                                    @class(['nav-link', 'active' => Route::currentRouteName() === 'register'])
                                    @if (Route::currentRouteName() === 'register') aria-current="page" @endif
                                >{{ __('Register') }}</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a 
                                href="{{ route('login') }}" 
                                @class(['nav-link', 'active' => Route::currentRouteName() === 'login'])
                                @if (Route::currentRouteName() === 'login') aria-current="page" @endif
                            >{{ __('Login') }}</a>
                        </li>
                    @endguest
    
                    @auth
                        <li class="nav-item d-none d-md-flex align-items-center ms-3">
                            <img src="{{ Auth::user()->avatar() }}" alt="{{ Auth::user()->username }}" class="rounded-circle img-fluid" loading="lazy" style="width: 38px;">
                        </li>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">{{ Auth::user()->username }}</a>
                            <ul class="dropdown-menu dropdown-menu-end" data-bs-popper="static">
                                @if (Auth::user()->group_id == 1)
                                    <li>
                                        <a href="{{ route('admin.index') }}" class="dropdown-item" target="_blank">{{ __('Dashboard') }}</a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                @endif
                                <li>
                                    <a href="{{ route('account.account-settings.index') }}" class="dropdown-item">{{ __('Account Settings') }}</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li x-data>
                                    <form action="{{ route('logout') }}" method="POST" x-ref="formLogout">
                                        @csrf
                                        <a href="{{ route('logout') }}" class="dropdown-item" @click.prevent="$refs.formLogout.submit()">{{ __('Logout') }}</a>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
    
        </div>
    </div>

    @if (Route::currentRouteName() != 'index')
        <!-- Search bar mobile -->
        <div class="bg-dark d-block d-md-none pb-2">
            <div class="container">
                <form action="{{ route('search') }}" method="GET">
                    <div class="input-group">
                        <input
                            type="text"
                            class="form-control"
                            name="q"
                            placeholder="{{ __('Find posts') }}"
                            value="{{ request()->query('q') }}"
                        >
                        <button class="btn btn-primary" type="submit">{{ __('Search') }}</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- [END] Search bar mobile -->
    @endif
</nav>