<div class="dropdown">
    <button class="btn btn-dark btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">{{ __('Menu') }}</button>
    <div class="dropdown-menu">
        @can('auth-check', $userAuth->authorize('admin-posts-edit'))
            <a href="{{ route('admin.posts.edit', ['post' => $post]) }}" class="dropdown-item">{{ __('Edit') }}</a>
        @endcan

        @can('auth-check', $userAuth->authorize('admin-posts-delete'))
            <button
                type="button"
                class="dropdown-item"
                data-toggle="modal"
                data-target="#modalDelete"
                data-link-delete="{{ route('admin.posts.destroy', ['post' => $post]) }}"
                data-item-name="{{ $post->title }}"
                @click="deleteItem"
            >{{ __('Delete') }}</button>
        @endcan
    </div>
</div>