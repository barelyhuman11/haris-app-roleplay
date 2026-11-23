<x-main.layouts.app>
    @push('metaData')
        {!! $metaData->render() !!}
        {!! $openGraph->render() !!}
    @endpush

    <x-slot:pageTitle>{{ $pageTitle }}</x-slot>

    <div class="container">

        <!-- Hero -->
        <div class="card py-4 mb-3">
            <div class="card-body">

                <div class="row justify-content-center">
                    <div class="col-12 col-lg-9 col-xl-8">
                        <div>
                            <h1 class="mb-2">{{ $options->get('site_title', config('app.name')) }}</h1>

                            @if (!empty($options->get('site_tagline')))
                                <p class="mb-2">{{ $options->get('site_tagline') }}</p>
                            @endif

                            <!-- Search bar -->
                            <form action="{{ route('search') }}" method="GET">
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="q"
                                        placeholder="{{ __('Find posts') }}"
                                        aria-label="{{ __('Find posts') }}"
                                        aria-describedby="heroSearchButton"
                                    >
                                    <button
                                        class="btn btn-primary"
                                        type="submit"
                                        id="heroSearchButton"
                                    ><span class="px-2">{{ __('Search') }}</span></button>
                                </div>
                            </form>
                            <!-- [END] Search bar -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- [END] Hero -->

        <!-- Latest posts -->
        <h2 class="mb-3">{{ __('Latest Posts') }}</h2>
        <div class="row g-2">
            @foreach ($posts as $post)
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <x-main.contents.post :$post />
                </div>
            @endforeach
        </div>
        <!-- [END] Latest posts -->

        <!-- Pagination -->
        {{ $posts->links('components.main.components.simple-pagination') }}
        <!-- [END] Pagination -->

    </div>
</x-main.layouts.app>