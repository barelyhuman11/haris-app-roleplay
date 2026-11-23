<div x-data="{{ $tableName }}">
    <div class="card">
        <div class="card-body">

            <!-- Toolbar -->
            <div id="toolbar" class="d-flex align-items-center">

                <!-- Bulk actions -->
                <div class="dropdown mr-2">
                    <button type="button" class="btn btn-secondary dropdown-toggle disabled" data-toggle="dropdown" aria-expanded="false" data-bulk-actions="true">{{ __('Bulk Actions') }}</button>
                    <div class="dropdown-menu">
                        @if (!empty($modalBulkDelete))
                            <button type="button" class="dropdown-item" data-toggle="modal" data-target="#modalBulkDelete">{{ __('Delete') }}</button>
                        @endif

                        @foreach ($bulkActionModals as $bulkAction)
                            <button type="button" class="dropdown-item" data-toggle="modal" data-target="#{{ $bulkAction['button']['target'] }}">{{ $bulkAction['button']['text'] }}</button>
                        @endforeach

                        @foreach ($bulkActionForms as $bulkAction)
                            <form action="{{ $bulkAction['route'] }}" method="POST">
                                @csrf
                                @if ($bulkAction['method'] != 'POST')
                                    @method($bulkAction['method'])
                                @endif
                                <input type="hidden" name="bulkID" value="">
                                <button type="submit" class="dropdown-item" data-bulk-submit="true">{{ $bulkAction['buttonText'] }}</button>
                            </form>
                        @endforeach
                    </div>
                </div>
                <!-- [END] Bulk actions -->

                <!-- Filter -->
                @foreach ($filters as $filter)
                    <div class="dropdown mr-2" x-data="{ currentID: '{{ $filter['noFilterID'] }}', currentName: '{{ $filter['noFilterText'] }}' }" data-filter-key="{{ $filter['filterKey'] }}" :data-filter-current-value="currentID" data-filter-default-value="{{ $filter['noFilterID'] }}" x-init="$watch('currentID', refreshTable)">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            {{ $filter['filterLabel'] }} : <span x-text="currentName"></span>
                        </button>
                        <div class="dropdown-menu">
                            <button type="button" class="dropdown-item" @click="currentID = '{{ $filter['noFilterID'] }}'; currentName = '{{ $filter['noFilterText'] }}';">{{ $filter['noFilterText'] }}</button>
                            @foreach ($filter['choices'] as $choice)
                                <button type="button" class="dropdown-item" @click="currentID = '{{ $choice['choiceID'] }}'; currentName = '{{ $choice['choiceText'] }}';">{{ $choice['choiceText'] }}</button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                <!-- [END] Filter -->

            </div>
            <!-- [END] Toolbar -->

            <!-- Table -->
            <table
                id="{{ $tableName }}"
                data-url="{{ $tableDataURL }}"
                data-pagination="true"
                data-side-pagination="server"
                data-search="true"
                data-show-columns="true"
                data-show-columns-toggle-all="true"
                data-toolbar="#toolbar"
                data-id-field="id"
                data-select-item-name="items"
                data-query-params="filterTable"
                data-classes="{{ $isSmall ? 'table table-bordered table-hover table-sm' : 'table table-bordered table-hover' }}"
            >
                <thead>
                    <tr>
                        <th data-checkbox="true"></th>
                        {!! $tableFields !!}
                        <th data-field="menu" data-align="center" data-switchable="false" data-width="1">{{ __('Menu') }}</th>
                    </tr>
                </thead>
            </table>
            <!-- [END] Table -->

            <!-- Modals -->
            @if (!empty($modalDelete))
                <!-- Delete -->
                <x-admin.components.modal name="delete">
                    <x-slot:modalTitle>{{ $modalDelete['title'] }}</x-slot>
                    <p class="mb-0">{{ $modalDelete['text'] }} : <span class="font-weight-bold" x-text="itemName"></span></p>
                    <x-slot:modalFooter>
                        <form :action="linkDelete" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                        </form>
                    </x-slot>
                </x-admin.components.modal>
                <!-- [END] Delete -->
            @endif

            @if (!empty($modalBulkDelete))
                <!-- Bulk Delete -->
                <x-admin.components.modal name="bulkDelete">
                    <x-slot:modalTitle>{{ $modalBulkDelete['title'] }}</x-slot>
                    <p class="mb-0">{!! $modalBulkDelete['text'] !!}</p>
                    <x-slot:modalFooter>
                        <form action="{{ $modalBulkDelete['route'] }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="bulkID" value="">
                            <button type="submit" class="btn btn-danger" data-bulk-submit="true">{{ __('Delete') }}</button>
                        </form>
                    </x-slot>
                </x-admin.components.modal>
                <!-- [END] Bulk Delete -->
            @endif

            @foreach ($bulkActionModals as $bulkAction)
                {{ $bulkAction['modalView'] }}
            @endforeach
            <!-- [END] Modals -->

        </div>
    </div>
</div>