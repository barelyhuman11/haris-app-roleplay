<x-admin.layouts.app :breadcrumb="$breadcrumb">
    <x-slot:pageTitle>{{ __('Users') }}</x-slot>

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
        @can('auth-check', $userAuth->authorize('admin-users-create'))
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">{{ __('Create User') }}</a>
        @endcan
    </div>

    {!! $usersTable->displayHTML() !!}

    @push('scripts')
        <link rel="stylesheet" href="{{ asset('assets/admin/plugins/bootstrap-table/bootstrap-table.min.css') }}">
    @endpush

    @push('scriptsBottom')
        <script src="{{ asset('assets/admin/plugins/bootstrap-table/bootstrap-table.min.js') }}"></script>
        {!! $usersTable->displayJS() !!}
    @endpush
</x-admin.layouts.app>