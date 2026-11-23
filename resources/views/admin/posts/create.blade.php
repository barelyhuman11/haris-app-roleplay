<x-admin.layouts.app :breadcrumb="$breadcrumb">
    <x-slot:pageTitle>{{ __('Create Post') }}</x-slot>

    @if (session('success'))
        <x-admin.components.alert>
            {{ session('success') }}
        </x-admin.components.alert>
    @endif

    @if (session('error'))
        <x-admin.components.alert type="danger">
            {{ session('error') }}
        </x-admin.components.alert>
    @endif

    <form action="{{ route('admin.posts.store') }}" method="POST" x-data="form" @submit.prevent="submit">
        @csrf

        <div class="row">
            <div class="col-12 col-lg-9">
                <div class="card">
                    <div class="card-body">

                        <x-admin.forms.input-text name="post_title" :label="__('Post title')" :value="old('post_title')" />
                        <x-admin.forms.textarea name="post_content" :label="__('Post content')">{{ old('post_content') }}</x-admin.forms.textarea>
                        <input type="hidden" name="status" x-model="status">

                        <div class="form-group">
                            <button class="btn btn-secondary" type="button" @click="postSettings = !postSettings">
                                {{ __('Post settings') }}
                                <i class="fa-fw fas fa-caret-down" x-show="postSettings == false"></i>
                                <i class="fa-fw fas fa-caret-up" x-show="postSettings == true"></i>
                            </button>
                        </div>

                        <div x-show="postSettings">
                            <x-admin.forms.input-text name="post_slug" :label="__('URL')" :value="old('post_slug')" />
                            <x-admin.forms.textarea name="post_description" :label="__('Meta Description')" rows="3">{{ old('post_description') }}</x-admin.forms.textarea>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block" @click="status = 'PUBLISH'">{{ __('Save') }}</button>
                        <button type="submit" class="btn btn-outline-secondary btn-block" @click="status = 'DRAFT'">{{ __('Save as Draft') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('scriptsBottom')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('form', () => ({
                    status: 'PUBLISH',
                    postSettings: {{ $errors->has('post_slug') || $errors->has('post_description') ? 'true' : 'false' }},
                    submit(e) {
                        e.target.submit();
                    }
                }));
            });
        </script>
    @endpush
</x-admin.layouts.app>