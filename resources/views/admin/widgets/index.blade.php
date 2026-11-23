<x-admin.layouts.app :breadcrumb="$breadcrumb">
    <x-slot:pageTitle>{{ __('Widgets') }}</x-slot>

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

    <div class="d-flex justify-content-end mb-3">
        @can('auth-check', $userAuth->authorize('admin-widgets-create'))
            <a href="{{ route('admin.settings.widgets.create') }}" class="btn btn-primary">{{ __('Create Widget') }}</a>
        @endcan
    </div>

    <div x-data="widgets">
        <div class="row">
            @forelse ($widgets as $widget)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-muted mb-2">{{ $sections[$widget->name] }}</div>
                            <div class="font-weight-bold">{{ \App\Helpers\Widgets::detail($widget->value, 'name') }}</div>
                            <div class="text-right">
                                <!-- Menu -->
                                <div class="dropdown">
                                    <button class="btn btn-dark btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">{{ __('Menu') }}</button>
                                    <div class="dropdown-menu">
                                        <a href="{{ route('admin.settings.widgets.edit', ['widget' => $widget]) }}" class="dropdown-item">{{ __('Edit') }}</a>
                                        <button
                                            type="button"
                                            class="dropdown-item"
                                            data-toggle="modal"
                                            data-target="#modalDelete"
                                            data-link-delete="{{ route('admin.settings.widgets.destroy', ['widget' => $widget]) }}"
                                            data-item-name="{{ \App\Helpers\Widgets::detail($widget->value, 'name') }}"
                                            @click="deleteItem"
                                        >{{ __('Delete') }}</button>
                                    </div>
                                </div>
                                <!-- [END] Menu -->
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="mb-0">{{ __('No widgets.') }}</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    
        <!-- Delete -->
        <x-admin.components.modal name="delete">
            <x-slot:modalTitle>{{ __('Delete widget?') }}</x-slot>
            <p class="mb-0">{{ __('Delete widget') }} : <span class="font-weight-bold" x-text="itemName"></span></p>
            <x-slot:modalFooter>
                <form :action="linkDelete" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </form>
            </x-slot>
        </x-admin.components.modal>
        <!-- [END] Delete -->
    </div>

    @push('scripts')
        <link rel="stylesheet" href="{{ asset('assets/admin/plugins/bootstrap-table/bootstrap-table.min.css') }}">
    @endpush

    @push('scriptsBottom')
        <script src="{{ asset('assets/admin/plugins/bootstrap-table/bootstrap-table.min.js') }}"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('widgets', () => ({
                    linkDelete: false,
                    itemName: false,
                    deleteItem(e) {
                        this.linkDelete = e.target.dataset.linkDelete;
                        this.itemName = e.target.dataset.itemName;
                    },
                }));
            });
        </script>
    @endpush
</x-admin.layouts.app>