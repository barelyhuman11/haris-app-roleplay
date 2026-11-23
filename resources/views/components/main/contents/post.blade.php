@props(['post'])

<div class="card h-100">
    <div class="card-body">
        <!-- Post -->
        <div class="fw-semibold">
            <a href="{{ route('post', ['post' => $post]) }}" class="text-dark">{{ $post->title }}</a>
        </div>
        <!-- [END] Post -->
    </div>
</div>