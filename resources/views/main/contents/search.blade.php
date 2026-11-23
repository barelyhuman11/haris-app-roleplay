<x-main.layouts.app>
    <x-slot:pageTitle>{{ __('Search Results') . ' : ' . $searchQuery }}</x-slot>

    <div class="container">

        <h1 class="my-3">{{ __('Search Results') . ' : ' . $searchQuery }}</h1>

        @if (count($posts) > 0)
            
            <!-- Search results -->
            <div class="row g-2">
                @foreach ($posts as $post)
                    <div class="col-12 col-md-6 col-lg-4">
                        <x-main.contents.post :$post />
                    </div>
                @endforeach
            </div>
            <!-- [END] Search results -->

            <!-- Pagination -->
            {{ $posts->links('components.main.components.simple-pagination') }}
            <!-- [END] Pagination -->

        @else

            <!-- Empty search results -->
            <p>{{ __('No results for search :q.', ['q' => $searchQuery]) }}</p>
            <!-- [END] Empty search results -->

        @endif

    </div>
</x-main.layouts.app>