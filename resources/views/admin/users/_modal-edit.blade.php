<form action="{{ route('admin.users.bulk-edit') }}" method="POST">
    @csrf
    @method('PUT')
    <x-admin.components.modal name="bulkEdit">
        <x-slot:modalTitle>{{ __('Edit User') }}</x-slot>

        <x-admin.forms.select name="group_id" :label="__('Group')" :options="$groups" :selected="0" />

        <x-slot:modalFooter>
            <input type="hidden" name="bulkID" value="">
            <button type="submit" class="btn btn-primary" data-bulk-submit="true">{{ __('Save') }}</button>
        </x-slot>
    </x-admin.components.modal>
</form>