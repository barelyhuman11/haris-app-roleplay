<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mainTable', () => ({
            linkDelete: false,
            itemName: false,
            deleteItem(e) {
                this.linkDelete = e.target.dataset.linkDelete;
                this.itemName = e.target.dataset.itemName;
            },
        }));
    });

    $('#{{ $tableName }}').bootstrapTable({
        onPostBody: function () {
            const bulkActionsBtns = document.querySelectorAll('button[data-bulk-actions="true"]');
            const checkboxes = document.querySelectorAll('input[name="items"], input[name="btSelectAll"]');
            const checkedCounts = document.querySelectorAll('[data-checked-count]');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    const checkedCheckboxes = document.querySelectorAll('input[name="items"]:checked');

                    bulkActionsBtns.forEach(btn => {
                        if (checkedCheckboxes.length > 0) {
                            btn.classList.remove('disabled');
                        }
                        else {
                            btn.classList.add('disabled');
                        }
                    });

                    checkedCounts.forEach(checkedCount => checkedCount.innerHTML = checkedCheckboxes.length);
                });
            });
        },
    });

    function filterTable(params) {
        const filters = document.querySelectorAll('[data-filter-key]');
        filters.forEach(filter => {
            params[filter.dataset.filterKey] = filter.dataset.filterCurrentValue ?? filter.dataset.filterDefaultValue;
        });
        return params;
    }

    function refreshTable() {
        $(document).ready(function () {
            const table = $('#{{ $tableName }}');
            table.bootstrapTable('refresh');
        });
    }

    const bulkSubmitBtns = document.querySelectorAll('button[data-bulk-submit]');
    bulkSubmitBtns.forEach(bulkSubmitBtn => {
        bulkSubmitBtn.addEventListener('click', e => {
            const bulkID = [];
            const checkedCheckboxes = document.querySelectorAll('input[name="items"]:checked');
            checkedCheckboxes.forEach(checkedCheckbox => {
                bulkID.push(checkedCheckbox.value);
            });
            const JSONBulkID = JSON.stringify(bulkID);

            const bulkIDFields = document.querySelectorAll('input[name="bulkID"]');
            bulkIDFields.forEach(bulkIDField => {
                bulkIDField.value = JSONBulkID;
            });
        });
    });
</script>