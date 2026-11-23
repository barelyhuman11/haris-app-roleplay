<x-main.layouts.app>
    @push('metaData')
        {!! $metaData->render() !!}
        {!! $openGraph->render() !!}
    @endpush

    <x-slot:pageTitle>{{ $pageTitle }}</x-slot>

    <div class="container">

        <!-- Breadcrumb -->
        <x-main.components.breadcrumb :links="$breadcrumb" class="mb-2" />
        <!-- [END] Breadcrumb -->

        <div class="row g-2">

            <!-- Main column -->
            <div class="col-12 col-lg-8">

                <!-- Content -->
                <div class="card">
                    <div class="card-body">
                        <h1 class="mb-3">{{ $post->title }}</h1>

                        @if (!empty($post->excerpt))
                            <p class="lead">{{ $post->excerpt }}</p>
                        @endif

                        <div>
                            {!! $post->content !!}
                        </div>
                    </div>
                </div>
                <!-- [END] Content -->

            </div>
            <!-- [END] Main column -->

            <!-- Side column -->
            <div class="col-12 col-lg-4">

                <!-- Related posts -->
                <div class="card">
                    <div class="card-header">
                        <b>{{ __('Related Posts') }}</b>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach ($relatedPosts as $relatedPost)
                            <a href="{{ route('post', ['post' => $relatedPost]) }}" class="list-group-item list-group-item-action">
                                <span>{{ $relatedPost->title }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                <!-- [END] Related posts -->

            </div>
            <!-- [END] Side column -->

        </div>
    </div>
</x-main.layouts.app>