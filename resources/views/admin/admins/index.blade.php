<x-admin.layouts.app :breadcrumb="$breadcrumb">
    <x-slot:pageTitle>{{ __('Administrators') }}</x-slot>

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
        @can('auth-check', $userAuth->authorize('admin-admins-create'))
            <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#modalAdd">{{ __('Add Administrator') }}</button>
        @endcan
    </div>

    <form action="{{ route('admin.admins.store') }}" method="POST">
        @csrf
        <x-admin.components.modal name="add">
            <x-slot:modalTitle>{{ __('Add Administrator') }}</x-slot>

            <x-admin.forms.select :label="__('Group')" :options="$groupsNotAdmin" name="group_id" :default-option="__('Select group')" />

            <x-slot:modalFooter>
                <button type="submit" class="btn btn-primary">{{ __('Add Administrator') }}</button>
            </x-slot>
        </x-admin.components.modal>
    </form>

    {!! $adminsTable->displayHTML() !!}

    @push('scripts')
        <link rel="stylesheet" href="{{ asset('assets/admin/plugins/bootstrap-table/bootstrap-table.min.css') }}">
    @endpush

    @push('scriptsBottom')
        <script src="{{ asset('assets/admin/plugins/bootstrap-table/bootstrap-table.min.js') }}"></script>
        {!! $adminsTable->displayJS() !!}
    @endpush
</x-admin.layouts.app>